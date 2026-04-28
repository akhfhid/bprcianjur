<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\peraturan;
use App\Services\PeraturanNotificationBlastService;
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
            $data = peraturan::latest()->get();

            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '<a href="peraturan/' . $data->id . '/edit"> <button class="btn btn-primary btn-sm">Edit</button></a>';
                    $button .= '<a href="peraturan/' . $data->id . '"> <button class="btn btn-success btn-sm">Detail</button></a>';
                    $button .= '&nbsp;&nbsp;&nbsp;<button type="button" name="delete" data-id="' . $data->id . '" class="delete-btn btn btn-danger btn-sm">Delete</button>';
                    return $button;
                })
                ->editColumn('id', 'ID: {{ $id }}')
                ->make(true);
            //return datatables()->of($data)->toJson();
        }
        return view('peraturan.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        // Membuat instance baru untuk peraturan
        $new_peraturan = new \App\peraturan();

        // Mengisi data dari request
        $new_peraturan->name = $request->get('name');
        $new_peraturan->nosk = $request->get('nosk');
        $new_peraturan->tglsk = $request->get('tglsk');
        $new_peraturan->tgllaku = $request->get('tgllaku');
        $new_peraturan->uraian = $request->get('uraian');

        // Menangani file PDF jika ada
        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');
            $filename = time() . '.' . $file->getClientOriginalExtension(); // Menentukan nama file
            $file->storeAs('pdfs', $filename, 'public'); // Menyimpan file ke folder pdfs dalam public storage
            $new_peraturan->pdf = $filename; // Menyimpan nama file PDF ke kolom 'pdf'
        }

        // Menyimpan peraturan yang baru
        $new_peraturan->created_by = \Auth::user()->id; // Menyimpan ID user yang membuat
        $new_peraturan->save(); // Menyimpan ke database

        // Kirim notif setelah response, tanpa queue worker, dengan filter penerima yang sama.
        $this->dispatchPeraturanNotificationsByCabang($new_peraturan);

        // Mengarahkan kembali ke halaman index dengan pesan sukses
        return redirect()->route('peraturan.index')->with('status', 'Peraturan Berhasil Ditambahkan');
    }

    /**
     * Notifikasi peraturan baru ke pegawai aktif tanpa queue worker.
     *
     * @param  \App\peraturan  $peraturan
     * @return void
     */
    protected function dispatchPeraturanNotificationsByCabang(peraturan $peraturan)
    {
        try {
            app(PeraturanNotificationBlastService::class)->sendAfterResponse($peraturan);
        } catch (\Throwable $e) {
            Log::error('Gagal menjadwalkan notifikasi peraturan', [
                'peraturan_id' => $peraturan->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit_peraturan = \App\peraturan::findorFail($id);
        return view('peraturan.edit', ['peraturan' => $edit_peraturan]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function simpanedit(Request $request, $id)
    {
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
        $peraturan->deleted_by = \Auth::id();
        $peraturan->save();
        $peraturan->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Peraturan berhasil dipindahkan ke Trash',
            ]);
        }

        return redirect()->route('peraturan.index')->with('status', 'Peraturan Successfully moved to trash');
    }

    public function trash()
    {
        $deletedperaturan = \App\peraturan::onlyTrashed()->paginate(10);
        return view('peraturan.trash', ['peraturan' => $deletedperaturan]);
    }
    public function restore($id)
    {
        $peraturan = \App\peraturan::withTrashed()->findOrFail($id);

        if ($peraturan->trashed()) {
            $peraturan->deleted_by = null;
            $peraturan->save();
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
