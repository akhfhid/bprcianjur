<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ordercuti;
use App\Cabang;
use App\Pegawai;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $query = ordercuti::with(['pegawai.relCabang', 'user'])->orderByDesc('created_at');

        if ($request->filled('cabang')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('cabang', $request->cabang);
            });
        }

        if ($request->filled('q')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        $cutis = $query->get()->groupBy('pegawai_id');
        $cabangs = Cabang::pluck('name', 'id');

        return view('cuti.index', compact('cutis', 'cabangs'));
    }

 public function pegawai(Request $request, $pegawaiId)
{
    $pegawai = Pegawai::findOrFail($pegawaiId);

    $query = ordercuti::where('pegawai_id', $pegawaiId)
        ->orderByDesc('created_at');

    if ($request->filled('jenis')) {
        $query->where('jeniscuti', 'LIKE', '%' . $request->jenis . '%');
    }

    if ($request->filled('q')) {
        $q = $request->q;
        $query->where(function ($sub) use ($q) {
            $sub->where('jeniscuti', 'like', "%$q%")
                ->orWhere('status', 'like', "%$q%")
                ->orWhere('alasan', 'like', "%$q%");
        });
    }

    $cutis = $query->paginate(10)->withQueryString();

    $jabatanIds = collect($cutis->items())
        ->pluck('otoatasan')
        ->merge(collect($cutis->items())->pluck('diketatasan'))
        ->filter()
        ->unique();
    $atasanPegawaiMap = Pegawai::whereIn('jabatan', $jabatanIds)
        ->where('cabang', $pegawai->cabang)
        ->get()
        ->groupBy('jabatan')
        ->map(function ($items) {
            return $items->first(); // ambil 1 pegawai 
        });

    return view('cuti.pegawai', compact(
        'pegawai',
        'cutis',
        'atasanPegawaiMap'
    ));
}


    public function show($id)
    {
        $cuti = ordercuti::with(['user', 'pegawai', 'cabang'])->findOrFail($id);
        return view('cuti.show', compact('cuti'));
    }

    public function edit($id)
    {
        $cuti = ordercuti::with('pegawai')->findOrFail($id);
        $jabatanList = \App\Jabatan::orderBy('name')->get();

        return view('cuti.edit', [
            'cuti' => $cuti,
            'pegawaiId' => $cuti->pegawai_id,
            'jabatanList' => $jabatanList,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tglawal' => 'required|date',
            'tglakhir' => 'required|date|after_or_equal:tglawal',
            'alasan' => 'required|string',
            'otoatasan' => 'required',
            'diketatasan' => 'required',
        ]);

        $cuti = ordercuti::findOrFail($id);

        $awal = Carbon::parse($request->tglawal);
        $akhir = Carbon::parse($request->tglakhir);

        $cuti->update([
            'tglawal' => $request->tglawal,
            'tglakhir' => $request->tglakhir,
            'jmlcuti' => $awal->diffInDays($akhir) + 1,
            'alasan' => $request->alasan,
            'otoatasan' => $request->otoatasan, // ID JABATAN
            'diketatasan' => $request->diketatasan, // ID JABATAN
        ]);

        return redirect()->route('cuti.pegawai', $cuti->pegawai_id)->with('status', 'Data cuti berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $cuti = ordercuti::findOrFail($id);
        $cuti->delete();

        return redirect($request->redirect_to ?? route('cuti.index'))->with('status', 'Data cuti berhasil dihapus');
    }
}
