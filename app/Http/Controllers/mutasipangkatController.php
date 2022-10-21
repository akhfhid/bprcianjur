<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Pegawai;
use \App\Pangkat;
use \Carbon\carbon;
use Illuminate\Support\Facades\Gate;
class mutasipangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware(function($request, $next){
        if(gate::allows('ADMIN')) return $next($request);
        abort(403,'Anda tidak memiliki hak akses');
        });
    }
    public function index(Request $request)
    {
        $name = $request->get('name');
        $mutasipangkat = \App\mutasipangkat::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","SUBMIT")->get();
        $data=[];
        $now= \Carbon\Carbon::now()->format('Y-m-d');
        
        foreach ($mutasipangkat as $mp) {
        $pegawai = \App\Pegawai::where('id',$mp['pegawai_id'])->first();
        $namapeg= $pegawai->name;
        $idpeg = $pegawai->id;
        $masuk = \Carbon\Carbon::parse($pegawai['tglmasuk']);
        $mkerja =$masuk->diffinYears($now);
        
        $pangsb = \App\Pangkat::where('id',$pegawai['pangkat'])->first();
        $pangkatseb = $pangsb->name;
        $pangm = \App\Pangkat::where('id',$mp['pangkat'])->first();
        $pangkat = $pangm->name;

        $data[]=[
            "id" => $mp['id'], 
            "pegawai_id" => $idpeg,
            "namapeg" => $namapeg,
            "mkerja" => $mkerja,
            "pangseb" => $pangkatseb,
            "pangkat" => $pangkat,
            "jenis" => $mp['jenis']
        ];
        }

        


        return view ('mutasipangkat.index',['mutasipangkat'=>$data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view ('mutasipangkat.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mutasipangkat = new \App\mutasipangkat;

        $mutasipangkat->pegawai_id = $request->get('pegawai');
        $mutasipangkat->pangkat = $request->get('pangkat');
        $mutasipangkat->jenis = $request->get('jenis');
        $mutasipangkat->status = 'SUBMIT';
        $mutasipangkat->created_by = \Auth::user()->id;

        $mutasipangkat->save();
        return redirect()->route('mutasipangkat.create')->with('status','Permohonan Berhasil Dibuat');
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
        
    }
    public function setuju($id){

        $mpangkat = \App\mutasipangkat::findorFail($id);
        $peg = \App\Pegawai::where('id',$mpangkat['pegawai_id'])->first();

        $mpangkat->status = 'DISETUJUI';
        $peg->pangkat = $mpangkat->pangkat;

        $mpangkat->save();
        $peg->save();

        return redirect()->route('mutasipangkat.index')->with('status','Permohonan Mutasi Pangkat Berhasil Disetujui');
    }
    public function tolak($id){
        $mpangkat = \App\mutasipangkat::findorFail($id);
        $mpangkat->status = 'DITOLAK';
        $mpangkat->save();
        return redirect()->route('mutasipangkat.index')->with('status','Permohonan Mutasi Pangkat Ditolak');

    }
    public function disetujui(Request $request){
        $name = $request->get('name');
        $mutasipangkat = \App\mutasipangkat::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DISETUJUI")->get();
        $data=[];
        $now= \Carbon\Carbon::now()->format('Y-m-d');
        
        foreach ($mutasipangkat as $mp) {
        $pegawai = \App\Pegawai::where('id',$mp['pegawai_id'])->first();
        $namapeg= $pegawai->name;
        $idpeg = $pegawai->id;
        $masuk = \Carbon\Carbon::parse($pegawai['tglmasuk']);
        $mkerja =$masuk->diffinYears($now);
        
        $pangsb = \App\Pangkat::where('id',$pegawai['pangkat'])->first();
        $pangkatseb = $pangsb->name;
        $pangm = \App\Pangkat::where('id',$mp['pangkat'])->first();
        $pangkat = $pangm->name;

        $data[]=[
            "id" => $mp['id'], 
            "pegawai_id" => $idpeg,
            "namapeg" => $namapeg,
            "mkerja" => $mkerja,
            "pangseb" => $pangkatseb,
            "pangkat" => $pangkat,
            "jenis" => $mp['jenis']
        ];
        }
        return view('mutasipangkat.setuju',['mutasipangkat'=>$data]);

    }
    public function ditolak(Request $request)
    {
        $name = $request->get('name');
        $mutasipangkat = \App\mutasipangkat::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DITOLAK")->get();
        $data=[];
        $now= \Carbon\Carbon::now()->format('Y-m-d');
        
        foreach ($mutasipangkat as $mp) {
        $pegawai = \App\Pegawai::where('id',$mp['pegawai_id'])->first();
        $namapeg= $pegawai->name;
        $idpeg = $pegawai->id;
        $masuk = \Carbon\Carbon::parse($pegawai['tglmasuk']);
        $mkerja =$masuk->diffinYears($now);
        
        $pangsb = \App\Pangkat::where('id',$pegawai['pangkat'])->first();
        $pangkatseb = $pangsb->name;
        $pangm = \App\Pangkat::where('id',$mp['pangkat'])->first();
        $pangkat = $pangm->name;

        $data[]=[
            "id" => $mp['id'], 
            "pegawai_id" => $idpeg,
            "namapeg" => $namapeg,
            "mkerja" => $mkerja,
            "pangseb" => $pangkatseb,
            "pangkat" => $pangkat,
            "jenis" => $mp['jenis']
        ];
        }
        return view('mutasipangkat.tolak',['mutasipangkat'=>$data]);
    }
}
