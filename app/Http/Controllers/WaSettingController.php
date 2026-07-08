<?php

namespace App\Http\Controllers;

use App\Cabang;
use App\Helpers\WhatsAppHelper;
use App\WaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WaSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman pengaturan WA.
     */
    public function index()
    {
        $setting = WaSetting::getSetting();
        $allCabang = Cabang::orderBy('name')->get();

        // Hitung jumlah penerima per cabang (pegawai dengan jabatan notifikasi & nomor HP valid)
        $jabatanNotif = [
            'Pimpinan Cabang', 'Kepala Bagian', 'Kepala Divisi',
            'Kepala Seksi Kredit & Dana', 'Kepala Seksi Umum & Akunting',
            'Kepala SKAI', 'Kepala Kantor Kas',
        ];

        $recipientCountByCabang = \App\Pegawai::whereNotNull('cabang')
            ->whereHas('jabatan', function ($q) use ($jabatanNotif) {
                $q->where(function ($sub) use ($jabatanNotif) {
                    foreach ($jabatanNotif as $jn) {
                        $sub->orWhere('name', 'like', $jn . '%');
                    }
                });
            })
            ->where(function ($q) {
                $q->where(function ($s) { $s->whereNotNull('nohp')->where('nohp', '<>', ''); })
                  ->orWhere(function ($s) { $s->whereNotNull('phone')->where('phone', '<>', ''); });
            })
            ->select('cabang', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('cabang')
            ->pluck('total', 'cabang')
            ->toArray();

        // Urutkan cabang berdasarkan cabang_order jika ada
        $orderedIds = $setting->getCabangOrderArray();

        if (!empty($orderedIds)) {
            $orderedCabang = collect($orderedIds)
                ->map(function($id) use ($allCabang) { return $allCabang->firstWhere('id', $id); })
                ->filter()
                ->values();

            // Tambahkan cabang yang belum ada di order list (baru ditambahkan)
            $unordered = $allCabang->whereNotIn('id', $orderedIds)->values();
            $cabangList = $orderedCabang->merge($unordered);
        } else {
            $cabangList = $allCabang;
        }

        $waApiUrl  = env('WA_API_URL', WhatsAppHelper::DEFAULT_API_URL);
        $waApiCode = env('WA_API_CODE', '');
        $waEnabled = filter_var(env('WA_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
        $blastEnabled = filter_var(env('WA_PERATURAN_BLAST_ENABLED', true), FILTER_VALIDATE_BOOLEAN);

        return view('admin.wa_setting.index', compact(
            'setting', 'cabangList', 'recipientCountByCabang',
            'waApiUrl', 'waApiCode', 'waEnabled', 'blastEnabled'
        ));
    }

    /**
     * Simpan pengaturan delay.
     */
    public function updateDelay(Request $request)
    {
        $request->validate([
            'delay_per_person' => 'required|integer|min:0|max:3600',
            'delay_per_cabang' => 'required|integer|min:0|max:3600',
        ]);

        $setting = WaSetting::getSetting();
        $setting->delay_per_person = $request->delay_per_person;
        $setting->delay_per_cabang = $request->delay_per_cabang;
        $setting->save();

        return back()->with('status_delay', 'Pengaturan delay berhasil disimpan.');
    }

    /**
     * Simpan urutan cabang (AJAX).
     */
    public function updateCabangOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        $setting = WaSetting::getSetting();
        $setting->cabang_order = json_encode(array_values($request->order));
        $setting->save();

        return response()->json(['success' => true, 'message' => 'Urutan cabang berhasil disimpan.']);
    }

    /**
     * Update API key & URL ke .env
     */
    public function updateEnv(Request $request)
    {
        $request->validate([
            'wa_api_url'  => 'required|url',
            'wa_api_code' => 'required|string|min:5',
            'wa_enabled'  => 'nullable',
            'blast_enabled' => 'nullable',
        ]);

        $envPath = base_path('.env');

        if (!file_exists($envPath) || !is_writable($envPath)) {
            return back()->with('status_env_error', 'File .env tidak ditemukan atau tidak bisa ditulis.');
        }

        $envContent = file_get_contents($envPath);

        $replacements = [
            'WA_API_URL'                 => $request->wa_api_url,
            'WA_API_CODE'                => $request->wa_api_code,
            'WA_ENABLED'                 => $request->has('wa_enabled') ? 'true' : 'false',
            'WA_PERATURAN_BLAST_ENABLED' => $request->has('blast_enabled') ? 'true' : 'false',
        ];

        foreach ($replacements as $key => $value) {
            // Escape forward slashes in value for regex
            $escapedValue = addcslashes($value, '/');

            if (preg_match("/^{$key}=/m", $envContent)) {
                // Replace existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$escapedValue}",
                    $envContent
                );
            } else {
                // Append new key
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);

        Log::info('WA Setting: .env updated', [
            'wa_api_url'   => $request->wa_api_url,
            'wa_enabled'   => $request->has('wa_enabled'),
            'blast_enabled' => $request->has('blast_enabled'),
        ]);

        return back()->with('status_env', 'Konfigurasi API WA berhasil disimpan ke .env.');
    }

    /**
     * Test kirim pesan ke nomor custom.
     */
    public function testSend(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string',
            'message' => 'required|string|min:3|max:1000',
        ]);

        $result = WhatsAppHelper::sendMessage($request->phone, $request->message);

        if ($result['success'] ?? false) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim ke ' . $request->phone,
                'detail'  => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Gagal mengirim pesan.',
            'detail'  => $result,
        ], 422);
    }
}
