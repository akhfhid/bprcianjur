<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use App\peraturan;
use App\Helpers\WhatsAppHelper;
use DataTables;

class peraturanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (gate::allows('PATUH')) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki hak akses');
        });
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = peraturan::query()
                ->when($request->kategori, function ($q) use ($request) {
                    $q->where('kategori', $request->kategori);
                })
                ->when($request->jenis_surat && $request->jenis_surat != 'all', function ($q) use ($request) {
                    $q->where('jenis_surat', $request->jenis_surat);
                })
                ->when($request->sub_jenis && $request->sub_jenis != 'all', function ($q) use ($request) {
                    $q->where('jenis_ojk', $request->sub_jenis);
                })
                ->latest();

            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    // Menggunakan data-id dan class modern sesuai style UI
                    $btn = '';

                    // Tombol View
                    $btn .= '<a href="' . url('peraturan/' . $data->id) . '" class="action-btn view" title="Detail"><i class="fas fa-eye"></i></a> ';

                    // Tombol Edit
                    $btn .= '<a href="' . url('peraturan/' . $data->id . '/edit') . '" class="action-btn edit" title="Edit"><i class="fas fa-edit"></i></a> ';

                    // Tombol Delete
                    $btn .= '<button type="button" data-id="' . $data->id . '" class="action-btn delete delete-btn" title="Hapus"><i class="fas fa-trash"></i></button>';

                    return $btn;
                })
                ->addColumn('jenis_ojk', function ($data) {
                    return $data->jenis_ojk ?? '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('peraturan.index');
    }

    public function statistik()
    {
        return response()->json([
            'total' => \App\peraturan::count(),
            'sk' => \App\peraturan::where('jenis_surat', 'SK')->count(),
            'se' => \App\peraturan::where('jenis_surat', 'SE')->count(),
            'tahun_ini' => \App\peraturan::whereYear('created_at', date('Y'))->count(),
        ]);
    }
    public function create()
    {
        return view('peraturan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $description=$request->get('description');
        // $dom = new \DomDocument();
        // $dom->loadHtml($description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        // $images = $dom->getElementsByTagName('img');

        // foreach($images as $k => $img){
        //     $data = $img->getAttribute('src');

        //     list($type, $data) = explode(';', $data);
        //     list(, $data)      = explode(',', $data);
        //     $data = base64_decode($data);

        //     $image_name= "/storage/peraturan/" . time().$k.'.png';
        //     $path = public_path() . $image_name;

        //     file_put_contents($path, $data);

        //     $img->removeAttribute('src');
        //     $img->setAttribute('src', $image_name);
        // }

        // $description = $dom->saveHTML();

        // Validasi untuk memastikan file PDF diterima jika ada
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'nosk' => 'required|string|max:255',
        //     'tglsk' => 'required|date',
        //     'tgllaku' => 'required|date',
        //     'uraian' => 'required|string',
        //     'pdf' => 'nullable|file|mimes:pdf|max:10240', // Hanya menerima file PDF jika ada
        // ]);

        $request->validate([
            'name' => 'required',
            'kategori' => 'required|in:internal,external',
            'jenis_surat' => 'required',
            'nosk' => 'required',
            'tglsk' => 'required|date',
            'tgllaku' => 'required|date',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
        ], [
            'pdf.mimes' => 'File harus berformat PDF.',
            'pdf.max' => 'Ukuran file PDF maksimal 50 MB.',
        ]);

        $new_peraturan = new \App\peraturan();

        $new_peraturan->name = $request->get('name');
        $new_peraturan->kategori = $request->get('kategori');
        $new_peraturan->jenis_surat = $request->get('jenis_surat');
        $new_peraturan->jenis_ojk = $request->get('sub_jenis'); 
        $new_peraturan->nosk = $request->get('nosk');
        $new_peraturan->tglsk = $request->get('tglsk');
        $new_peraturan->tgllaku = $request->get('tgllaku');
        $new_peraturan->uraian = $request->get('uraian');

        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('pdfs', $filename, 'public');
            $new_peraturan->pdf = $filename;
        }
        $new_peraturan->created_by = \Auth::user()->id;
        $new_peraturan->save();

        try {
            $notifStat = $this->sendPeraturanBaruNotificationToAllPegawai($new_peraturan);
            \Log::info('Notifikasi WA peraturan baru selesai diproses', $notifStat);
        } catch (\Throwable $e) {
            \Log::error('Gagal memproses notifikasi WA peraturan baru', [
                'peraturan_id' => $new_peraturan->id,
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()->route('peraturan.index')->with('status', 'Peraturan Berhasil Ditambahkan');
    }

    private function sendPeraturanBaruNotificationToAllPegawai($peraturan)
    {
        $delayPerPegawaiSeconds = 5;

        $query = \App\Pegawai::query()
            ->select(['id', 'name', 'nohp'])
            ->whereNotNull('nohp')
            ->where('nohp', '<>', '');

        if (\Schema::hasColumn('pegawais', 'status_active')) {
            $query->where('status_active', 1);
        }

        $pegawais = $query->orderBy('name')->get();

        $success = 0;
        $failed = 0;
        $processed = 0;
        $sentPhones = [];

        foreach ($pegawais as $pegawai) {
            $normalizedPhone = WhatsAppHelper::convertPhoneNumber($pegawai->nohp);

            if (!$normalizedPhone || isset($sentPhones[$normalizedPhone])) {
                continue;
            }

            $sentPhones[$normalizedPhone] = true;
            $processed++;

            if ($processed > 1) {
                sleep($delayPerPegawaiSeconds);
            }

            $result = WhatsAppHelper::sendPeraturanBaruNotificationToPegawai($peraturan, $pegawai);
            if (!empty($result['success'])) {
                $success++;
            } else {
                $failed++;
            }
        }

        return [
            'peraturan_id' => $peraturan->id,
            'total_pegawai' => $pegawais->count(),
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
            'delay_per_pegawai_seconds' => $delayPerPegawaiSeconds,
        ];
    }
    public function show($id)
    {
        $peraturan = \App\peraturan::findorFail($id);
        $time = \Carbon\Carbon::now()->translatedFormat('d/m-Y');
        $new_loguser = new \App\loguser();
        $user = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id', $user)->first();
        $new_loguser->nampeg = $pegawai->name;
        $new_loguser->keterangan = $peraturan->name;
        //$new_loguser->waktu = $time;
        $new_loguser->save();

        //$pdf = PDF::loadview('peraturan.show',['peraturan'=>$peraturan]);
        //return $pdf->stream();
        //exit(0);

        return view('peraturan.show', ['peraturan' => $peraturan, 'time' => $time]);
    }
    public function edit($id)
    {
        $edit_peraturan = \App\peraturan::findorFail($id);
        return view('peraturan.edit', ['peraturan' => $edit_peraturan]);
    }
    public function loguser(Request $request)
    {
        $keyword = trim((string) $request->get('keyword'));
        $perPage = 15;

        $combinedQuery = null;

        if (\Schema::hasTable('logusers')) {
            $logAksesQuery = \DB::table('logusers as lu')->select([\DB::raw("'Log Akses' as sumber"), 'lu.nampeg as nampeg', \DB::raw('lu.keterangan as keterangan'), \DB::raw('lu.created_at as waktu_akses'), \DB::raw('NULL as mulai'), \DB::raw('NULL as selesai'), \DB::raw('NULL as active_seconds')]);

            if ($keyword !== '') {
                $logAksesQuery->where(function ($q) use ($keyword) {
                    $q->where('lu.nampeg', 'like', "%{$keyword}%")->orWhere('lu.keterangan', 'like', "%{$keyword}%");
                });
            }

            $combinedQuery = $logAksesQuery;
        }

        if (\Schema::hasTable('peraturan_view_sessions')) {
            $aktivitasPeraturanQuery = \DB::table('peraturan_view_sessions as pvs')
                ->leftJoin('peraturans as p', 'p.id', '=', 'pvs.peraturan_id')
                ->leftJoin('pegawais as peg', 'peg.id', '=', 'pvs.pegawai_id')
                ->select([\DB::raw("'Aktivitas Peraturan' as sumber"), \DB::raw('COALESCE(peg.name, "-") as nampeg'), \DB::raw('COALESCE(p.name, "-") as keterangan'), \DB::raw('pvs.started_at as waktu_akses'), \DB::raw('pvs.started_at as mulai'), \DB::raw('pvs.ended_at as selesai'), \DB::raw('pvs.active_seconds as active_seconds')]);

            if ($keyword !== '') {
                $aktivitasPeraturanQuery->where(function ($q) use ($keyword) {
                    $q->where('peg.name', 'like', "%{$keyword}%")
                        ->orWhere('p.name', 'like', "%{$keyword}%")
                        ->orWhere('pvs.role', 'like', "%{$keyword}%");
                });
            }

            if ($combinedQuery) {
                $combinedQuery = $combinedQuery->unionAll($aktivitasPeraturanQuery);
            } else {
                $combinedQuery = $aktivitasPeraturanQuery;
            }
        }

        if ($combinedQuery) {
            $logs = \DB::query()->fromSub($combinedQuery, 'logs')->orderByDesc('waktu_akses')->simplePaginate($perPage)->appends($request->query());
        } else {
            $logs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, $request->integer('page', 1), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return view('kepatuhan.loguser', compact('keyword', 'logs'));
    }
    public function simpanedit(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'kategori' => 'required|in:internal,external',
            'jenis_surat' => 'required',
            'nosk' => 'required',
            'tglsk' => 'required|date',
            'tgllaku' => 'required|date',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
            'description' => 'nullable|string',
        ], [
            'pdf.mimes' => 'File harus berformat PDF.',
            'pdf.max' => 'Ukuran file PDF maksimal 50 MB.',
        ]);

        $edit_peraturan = \App\peraturan::findOrFail($id);
        $description = $request->get('description') ?? '';
        $description_save = null;

        if (!empty($description)) {
            $dom = new \DomDocument('1.0', 'UTF-8');
            libxml_use_internal_errors(true);

            $dom->loadHTML($description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            $images = $dom->getElementsByTagName('img');

            foreach ($images as $k => $img) {
                $data = $img->getAttribute('src');

                if (strpos($data, 'base64') !== false) {
                    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));

                    $image_name = '/storage/peraturan/post_' . time() . $k . '.png';
                    $path = public_path() . $image_name;

                    file_put_contents($path, $data);

                    $img->setAttribute('src', $image_name);
                } else {
                    $img->setAttribute('src', $data);
                }
            }

            $description_save = $dom->saveHTML();
        }

        $edit_peraturan->name = $request->name;
        $edit_peraturan->kategori = $request->kategori;
        $edit_peraturan->jenis_surat = $request->jenis_surat;
        $edit_peraturan->nosk = $request->nosk;
        $edit_peraturan->tglsk = $request->tglsk;
        $edit_peraturan->tgllaku = $request->tgllaku;
        $edit_peraturan->uraian = $request->uraian;

        if ($description_save !== null) {
            $edit_peraturan->description = $description_save;
        }

        $edit_peraturan->updated_by = \Auth::user()->id;

        $edit_peraturan->save();

        return redirect()->route('peraturan.index')->with('status', 'Peraturan Berhasil Diperbaharui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $peraturan = \App\peraturan::findOrFail($id);
        $peraturan->delete();

        // Jika request via AJAX (dari DataTables)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Peraturan berhasil dipindahkan ke Trash.',
            ]);
        }

        return redirect()->route('peraturan.index')->with('status', 'Peraturan Successfully moved to trash');
    }

    public function trash()
    {
        $deletedperaturan = \App\peraturan::onlyTrashed()->latest('deleted_at')->paginate(10);

        return view('peraturan.trash', ['peraturan' => $deletedperaturan]);
    }
    public function restore($id)
    {
        $peraturan = \App\peraturan::withTrashed()->findOrFail($id);

        if ($peraturan->trashed()) {
            $peraturan->restore();
        } else {
            return redirect()->route('peraturan.index')->with('status', 'peraturan is not in trash');
        }

        return redirect()->route('peraturan.index')->with('status', 'peraturan Successfully Restored');
    }

    public function deletePermanent($id)
    {
        $peraturan = \App\peraturan::withTrashed()->findOrFail($id);

        // if($peraturan->trashed()){
        //   return redirect()->route('peraturan.index')
        // ->with('status'.'Cannot Delete Permanent Active peraturan');
        //} else {
        $peraturan->forceDelete();

        return redirect()->route('peraturan.trash')->with('status', 'peraturan Permanently Deleted');
        //}
    }
    public function show_pdf($id)
    {
        $peraturan = \App\peraturan::findorFail($id);
        $time = \Carbon\Carbon::now()->translatedFormat('d/m-Y');

        //$pdf = PDF::loadview('peraturan.show',['peraturan'=>$peraturan]);
        //return $pdf->stream();
        //exit(0);

        return view('peraturan.show_pdf', ['peraturan' => $peraturan, 'time' => $time]);
    }
}
