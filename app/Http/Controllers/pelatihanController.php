<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class pelatihanController extends Controller
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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_pelatihan = new \App\pelatihan;

        $new_pelatihan->name = $request->get('name');
        $new_pelatihan->penyelenggara = $request->get('penyelenggara');
        $new_pelatihan->thnlatih = $request->get('thnlatih');
        $new_pelatihan->pegawai_id = $request->get('idpeg');

        $sertif = $request -> file('image');
       if($sertif){
         $sertifikat = $sertif->store('sertifikat','public');
        $new_pelatihan->image = $sertifikat;
        }
        $new_pelatihan->created_by = \Auth::user()->id;
        $new_pelatihan->save();
        return redirect()->route('pelatihan.list',$request->get('idpeg'))->with('status','data riwayat pelatihan berhasil ditambahkan');
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
        $pelatihan = \App\pelatihan::findorfail($id);
        $pegawai = \App\Pegawai::where('id',$pelatihan['pegawai_id'])->first();

        return view('pelatihan.edit',['pelatihan'=>$pelatihan,'pegawai'=>$pegawai]);
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
        $edit_pelatihan = \App\pelatihan::findorfail($id);
        $pegawai = \App\Pegawai::where('id',$edit_pelatihan['pegawai_id'])->first();

        $edit_pelatihan->name = $request->get('name');
        $edit_pelatihan->penyelenggara = $request->get('penyelenggara');
        $edit_pelatihan->thnlatih = $request->get('thnlatih');
        $edit_pelatihan->pegawai_id = $request->get('idpeg');

        $sertif = $request -> file('image');
       if($sertif){
         $sertifikat = $sertif->store('sertifikat','public');
        $edit_pelatihan->image = $sertifikat;
        }
        $edit_pelatihan->updated_by = \Auth::user()->id;
        $edit_pelatihan->save();
        return redirect()->route('pelatihan.list',$request->get('idpeg'))->with('status','data riwayat pelatihan berhasil diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $pelatihan = \App\pelatihan::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',[$pelatihan['pegawai_id']])->first();


        $pelatihan->delete();
       // $datapelatihan = \App\pelatihan::where('pegawai_id',[$pegawai['id']])->paginate(10);
        return redirect()->route('pelatihan.list',$pegawai)->with("status","Data Pegawai Berhasil Dihapus");

    }
    public function list($id){
         $pegawai = \App\Pegawai::findorfail($id);
        $datapelatihan = \App\pelatihan::where('pegawai_id',[$pegawai['id']])->paginate(10);

        return view('pelatihan.index',['datapelatihan'=>$datapelatihan,'pegawai'=>$pegawai]);

    }
    public function tambah($id){
         $pegawai = \App\Pegawai::findorfail($id);

        return view('pelatihan.create',['pegawai'=>$pegawai]);
    }
}
