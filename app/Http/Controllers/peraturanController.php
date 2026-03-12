<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use App\peraturan;
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
            $query = Peraturan::query()

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
                    $button = '<a href="peraturan/' . $data->id . '/edit" class="btn btn-primary btn-sm">Edit</a> ';
                    $button .= '<a href="peraturan/' . $data->id . '" class="btn btn-success btn-sm">Detail</a> ';
                    $button .= '<button type="button" id="' . $data->id . '" class="delete btn btn-danger btn-sm">Delete</button>';

                    return $button;
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
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $new_peraturan = new \App\peraturan();

        $new_peraturan->name = $request->get('name');
        $new_peraturan->kategori = $request->get('kategori');
        $new_peraturan->jenis_surat = $request->get('jenis_surat');
        $new_peraturan->jenis_ojk = $request->get('sub_jenis'); // tambahan baru
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

        return redirect()->route('peraturan.index')->with('status', 'Peraturan Berhasil Ditambahkan');
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

    public function simpanedit(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'kategori' => 'required|in:internal,external',
            'jenis_surat' => 'required',
            'nosk' => 'required',
            'tglsk' => 'required|date',
            'tgllaku' => 'required|date',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $edit_peraturan = \App\peraturan::findOrFail($id);
        $edit_peraturan = \App\peraturan::findorFail($id);
        $description = $request->get('description');
        $dom = new \DomDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHtml($description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        $bs64 = 'base64';

        foreach ($images as $k => $img) {
            $data = $img->getAttribute('src');
            if (strpos($data, $bs64) == true) {
                $data = base64_decode(preg_replace('#^data:image/\w+;base64.#i', '', $data));
                //list($type, $data) = explode(';', $data);
                //list(, $data)      = explode(',', $data);

                $image_name = '/storage/peraturan/' . 'post_' . time() . $k . '.png';
                $path = public_path() . $image_name;

                file_put_contents($path, $data);

                $img->removeAttribute('src');
                $img->setAttribute('src', $image_name);
            } else {
                $image_name = '/' . $data;
                $img->setAttribute('src', $image_name);
            }
        }

        $description_save = $dom->saveHTML();

        $edit_peraturan->name = $request->get('name');
        $edit_peraturan->kategori = $request->get('kategori');
        $edit_peraturan->jenis_surat = $request->get('jenis_surat');
        $edit_peraturan->nosk = $request->get('nosk');
        $edit_peraturan->tglsk = $request->get('tglsk');
        $edit_peraturan->tgllaku = $request->get('tgllaku');
        $edit_peraturan->uraian = $request->get('uraian');

        //$edit_peraturan->pdf = $description_save;

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
