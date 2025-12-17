<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Pegawai;
use Illuminate\Support\Facades\Gate;
class mutasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware(function($request, $next){
      if (Gate::allows('ADMIN') || Gate::allows('ADMIN_SDM')) {
                return $next($request);
            }

            abort(403, 'Anda tidak memiliki hak akses');
        });
    }
    public function index(Request $request)
    {
        $name = $request->get('name');
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","SUBMIT")->get();
        $data=[];
        foreach ($mutasi as $m) {
        $pegawai = \App\Pegawai::where('id',$m['pegawai_id'])->first();
            $idpeg = $pegawai['id'];
            $namapeg = $pegawai['name'];
            $cabangpeg = $pegawai['cabang'];
            $jabatanpeg = $pegawai['jabatan'];
        $cabang = \App\Cabang::where('id',$cabangpeg)->first();
            $namacab=$cabang['name'];
        $jabatan = \App\Jabatan::where('id',$jabatanpeg)->first();
            $namajab=$jabatan['name'];
        $cabangmut = \App\Cabang::where('id',$m['cabang'])->first();
            $namacabmut=$cabangmut['name'];
        $jabatanmut = \App\Jabatan::where('id',$m['jabatan'])->first();
            $namajabmut=$jabatanmut['name'];

            $data[]=[
                'id' => $m['id'],
                'pegawai_id' => $idpeg,
                "name" => $namapeg,
                "cabseb"=>$namacab,
                "cabmut"=>$namacabmut,
                "jabseb"=>$namajab,
                "jabmut"=>$namajabmut,
                "jenis"=>$m['jenis'],
                "status"=>$m['status']

            ];
        }
        return view ('mutasi.index',['mutasi'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pegawai = \App\Pegawai::pluck('name','id');
        return view('mutasi.create',['pegawai'=>$pegawai]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pegawai_id = $request->get('pegawai');
        $jabatan = $request->get('jabatan');
        $cabang = $request->get('cabang');
        $jenis = $request->get('jenis');
        
        $mutasi = new \App\mutasi;
        
        $mutasi->pegawai_id = $pegawai_id;
        $mutasi->jabatan = $jabatan;
        $mutasi->cabang = $cabang;
        $mutasi->jenis = $jenis;
        $mutasi->status = 'SUBMIT';
        $mutasi->created_by = \Auth::user()->id;
        $mutasi->save();

        return redirect()->route('mutasi.index')->with('status','Data Permohonan Mutasi Successfully Created');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function setuju($id){
        $mutasi = \App\mutasi::findorFail($id);
        $pegawai = \App\Pegawai::where('id',$mutasi['pegawai_id'])->first();
        $jabatan = \App\Jabatan::where('id',$mutasi['jabatan'])->first();
        $cabang = \App\Cabang::where('id',$mutasi['cabang'])->first();
        $riwayatkerja = new \App\riwayatkerja;
        $now= \Carbon\Carbon::now()->format('Y');

        $mutasi->status = 'DISETUJUI';
        $pegawai->jabatan =$mutasi->jabatan;
        $pegawai->cabang = $mutasi->cabang;
        $riwayatkerja->name = $jabatan->name;
        $riwayatkerja->kantorcabang = $cabang->name;
        $riwayatkerja->pegawai_id = $mutasi->pegawai_id;
        $riwayatkerja->thnangkat = $now;
        $riwayatkerja->created_by = \Auth::user()->id;

        $mutasi->save();
        $riwayatkerja->save();
        $pegawai->save();

        return redirect()->route('mutasi.index')->with('status','Data Mutasi Disetujui');

    }
    public function tolak($id){
        $mutasi = \App\mutasi::findorFail($id);
        $mutasi->status = 'DITOLAK';
        $mutasi->save();

        return redirect()->route('mutasi.index')->with('status','Data Mutasi Ditolak');

    }
    public function disetujui(Request $request){
         $name = $request->get('name');
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DISETUJUI")->get();
        $data=[];
        foreach ($mutasi as $m) {
        $pegawai = \App\Pegawai::where('id',$m['pegawai_id'])->first();
            $idpeg = $pegawai['id'];
            $namapeg = $pegawai['name'];
            $cabangpeg = $pegawai['cabang'];
            $jabatanpeg = $pegawai['jabatan'];
        $cabang = \App\Cabang::where('id',$cabangpeg)->first();
            $namacab=$cabang['name'];
        $jabatan = \App\Jabatan::where('id',$jabatanpeg)->first();
            $namajab=$jabatan['name'];
        $cabangmut = \App\Cabang::where('id',$m['cabang'])->first();
            $namacabmut=$cabangmut['name'];
        $jabatanmut = \App\Jabatan::where('id',$m['jabatan'])->first();
            $namajabmut=$jabatanmut['name'];

            $data[]=[
                'id' => $m['id'],
                'pegawai_id' => $idpeg,
                "name" => $namapeg,
                "cabseb"=>$namacab,
                "cabmut"=>$namacabmut,
                "jabseb"=>$namajab,
                "jabmut"=>$namajabmut,
                "jenis"=>$m['jenis'],
                "status"=>$m['status']

            ];
        }
        return view ('mutasi.disetujui',['mutasi'=>$data]);
    }
    public function ditolak(Request $request){
         $name = $request->get('name');
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DITOLAK")->get();
        $data=[];
        foreach ($mutasi as $m) {
        $pegawai = \App\Pegawai::where('id',$m['pegawai_id'])->first();
            $idpeg = $pegawai['id'];
            $namapeg = $pegawai['name'];
            $cabangpeg = $pegawai['cabang'];
            $jabatanpeg = $pegawai['jabatan'];
        $cabang = \App\Cabang::where('id',$cabangpeg)->first();
            $namacab=$cabang['name'];
        $jabatan = \App\Jabatan::where('id',$jabatanpeg)->first();
            $namajab=$jabatan['name'];
        $cabangmut = \App\Cabang::where('id',$m['cabang'])->first();
            $namacabmut=$cabangmut['name'];
        $jabatanmut = \App\Jabatan::where('id',$m['jabatan'])->first();
            $namajabmut=$jabatanmut['name'];

            $data[]=[
                'id' => $m['id'],
                'pegawai_id' => $idpeg,
                "name" => $namapeg,
                "cabseb"=>$namacab,
                "cabmut"=>$namacabmut,
                "jabseb"=>$namajab,
                "jabmut"=>$namajabmut,
                "jenis"=>$m['jenis'],
                "status"=>$m['status']

            ];
        }
        return view ('mutasi.ditolak',['mutasi'=>$data]);

    }
}
