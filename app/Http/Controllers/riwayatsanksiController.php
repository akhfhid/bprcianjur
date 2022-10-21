<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class riwayatsanksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sanksi = \App\Sanksi::pluck("name","id");

        return view('riwayatsanksi.create',["sanksi"=>$sanksi]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $idpeg = $request->get("pegawai");
        $idsanksi = $request->get("sanksi");
        $tglsanksi  =$request->get("tglsanksi");
        $ket = $request->get("ket");
        $nosanksi = $request->get("nosanksi");
        $pegawai = \App\pegawai::where("id",$idpeg)->first();
        $sanksi = \App\sanksi::where("id",$idsanksi)->first();
        $tunda = $sanksi->tunda;

        $new_riwayatsanksi = new \App\riwayatsanksi;

        $new_riwayatsanksi->sanksi= $idsanksi;
        $new_riwayatsanksi->id_peg=$idpeg;
        $new_riwayatsanksi->tglsanksi = $tglsanksi;
        $new_riwayatsanksi->nosanksi = $nosanksi;
        $new_riwayatsanksi->ket=$ket;
        $new_riwayatsanksi->created_by = \Auth::user()->id;
        $pegawai->tunda = $tunda;
        $new_riwayatsanksi->save();
        $pegawai->save();

        return redirect()->route("riwayatsanksi.create")->with("status","Sanksi Berhasil Ditambahkan");


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
        $riwayat = \App\riwayatsanksi::findorfail($id);
        $pegawai = \App\Pegawai::where('id',$riwayat['id_peg'])->first();
        $jenis = \App\sanksi::pluck("name","id");
        $nsanksi = \App\sanksi::where('id',$riwayat['sanksi'])->first();
        $nama = $nsanksi->name;

        return view('riwayatsanksi.edit',['riwayat'=> $riwayat,'pegawai'=>$pegawai,'nama'=>$nama,'jenis'=>$jenis]);
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
        $riwayatsanksi = \App\riwayatsanksi::findorfail($id);
        $pegawai = \App\Pegawai::where("id",$riwayatsanksi['id_peg'])->first();

        $idpeg = $pegawai->id;
        $idsanksi = $request->get("sanksi");
        $tglsanksi  =$request->get("tglsanksi");
        $ket = $request->get("ket");
        $nosanksi = $request->get("nosanksi");
        $sanksi = \App\sanksi::where("id",$idsanksi)->first();
        $tunda = $sanksi->tunda;


        $riwayatsanksi->sanksi= $idsanksi;
        $riwayatsanksi->id_peg=$idpeg;
        $riwayatsanksi->tglsanksi = $tglsanksi;
        $riwayatsanksi->nosanksi = $nosanksi;
        $riwayatsanksi->ket=$ket;
        $riwayatsanksi->created_by = \Auth::user()->id;
        $pegawai->tunda = $tunda;
        $riwayatsanksi->save();
        $pegawai->save();
        return redirect()->route("riwayatsanksi.list",$idpeg)->with('status','Data sanksi Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $riwayatsanksi= \App\riwayatsanksi::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$riwayatsanksi['pegawai_id'])->first();
        $idpeg = $pegawai->id;
        $riwayatsanksi->forcedelete();
        return redirect()->route("riwayatsanksi.list",$idpeg)->with('status','Data sanksi Successfully Deleted');
    }
    public function ajaxsearch(Request $request){
        $keyword = $request->get("q");

        $pegawai = \App\Pegawai::where("name","LIKE","%$keyword%")->get();

        return $pegawai;
    }

    public function list($id){
        $pegawai = \App\Pegawai::findorfail($id);
        $sanksi = \App\sanksi::pluck("name","id");
        $riwayatsanksi = \App\riwayatsanksi::where('id_peg',[$pegawai['id']])->paginate(10);
        $datasanksi=[];

        foreach ($riwayatsanksi as $rsanksi) {
            $sanksipegawai = \App\sanksi::where('id',[$rsanksi['sanksi']])->first();
            $sankpeg = $sanksipegawai['name'];

            $datasanksi[]=[
                "id"=>$rsanksi['id'],
                "sanksipeg"=>$sankpeg,
                "tglsanksi"=>$rsanksi['tglsanksi'],
                "nosanksi"=>$rsanksi['nosanksi'],
                "ket"=>$rsanksi['ket']
            ];
        }

        return view('riwayatsanksi.index',['datasanksi'=>$datasanksi,'pegawai'=>$pegawai]);
    }
    public function deletePermanent($id){
        $riwayatsanksi= \App\riwayatsanksi::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$riwayatsanksi['id_peg'])->first();
        //$idpeg = $riwayatsanksi['pegawai_id'];
        $riwayatsanksi->forcedelete();
        return redirect()->route('riwayatsanksi.list',$pegawai)->with('status','Data sanksi Successfully Deleted');

    }
    public function tambah($id){
        $pegawai = \App\Pegawai::findorfail($id);
        $sanksi = \App\sanksi::pluck('name','id');
        return view('riwayatsanksi.tambah',['pegawai'=>$pegawai, 'sanksi'=>$sanksi]);

    }
    public function simpan(Request $request){
        $idpeg = $request->get("idpeg");
        $idsanksi = $request->get("sanksi");
        $tglsanksi  =$request->get("tglsanksi");
        //$nosanksi = $request->get("nomor");
        $ket = $request->get("ket");


        $new_riwayatsanksi = new \App\riwayatsanksi;

        $new_riwayatsanksi->sanksi= $idsanksi;
        $new_riwayatsanksi->id_peg=$idpeg;
        $new_riwayatsanksi->tglsanksi = $tglsanksi;
        $new_riwayatsanksi->nosanksi = $request->get('nomor');;
        $new_riwayatsanksi->ket=$ket;
        $new_riwayatsanksi->created_by = \Auth::user()->id;

        $new_riwayatsanksi->save();


        return redirect()->route("riwayatsanksi.list",$idpeg)->with("status","Sanksi Berhasil Ditambahkan");

    }
}
