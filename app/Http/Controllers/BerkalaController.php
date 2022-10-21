<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BerkalaController extends Controller
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
        $pangkat = $request->get('pangkat');
        $idpang = $request->get('idpang');
        $gol = $request->get('gol');
        $gapok = $request->get('gapok');

        $new_berkala = new \App\berkala;
        $new_berkala->idpang = $idpang;
        $new_berkala->gol = $gol;
        $new_berkala->gapok = $gapok;
        $new_berkala->created_by = \Auth::user()->id;
        $new_berkala->save();

        return redirect()->route('berkala.list',$idpang)->with("status","Data Berhasil Ditambahkan");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $berkala = \App\berkala::findOrFail($id);
        $pangkat = \App\Pangkat::where('id',[$berkala['idpang']])->first();

        return view('berkala.edit',["berkala"=>$berkala,"pangkat"=>$pangkat]);
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
        $pangkat = $request->get('pangkat');
        $idpang = $request->get('idpang');
        $gol = $request->get('gol');
        $gapok = $request->get('gapok');

        $berkala =  \App\berkala::findOrFail($id);
        $berkala->idpang = $idpang;
        $berkala->gol = $gol;
        $berkala->gapok = $gapok;
        $berkala->updated_by = \Auth::user()->id;
        $berkala->save();

        return redirect()->route('berkala.list',$idpang)->with("status","Data Berhasil Diperbaharui");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $berkala = \App\berkala::findOrFail($id);
        $pangkat = \App\Pangkat::where('id',$berkala['idpang'])->first();
        $idpang =  $pangkat->id;
        $berkala->forcedelete();
        return redirect()->route("berkala.list",$idpang)->with('status','Data sanksi Successfully Deleted');
    }

    public function list($id){
        $pangkat = \App\Pangkat::findOrFail($id);
        $berkala = \App\berkala::where('idpang',[$pangkat['id']])->paginate(10);

        $databerkala=[];
        foreach ($berkala as $b) {
           $namepang = \App\Pangkat::where('id',[$b['idpang']])->first();
           $pang = $namepang['name'];
           $mkpang = $b['gol'];
           $gapok = $b['gapok'];
        $databerkala[]=[
                "id" => $b['id'],
                "name" => $pang,
                "berkala" => $mkpang,
                "gapok" => $gapok

            ];


        }
        

        return view ("berkala.index",["berkalas"=>$berkala,"pangkat"=>$pangkat, "databerkala"=>$databerkala]);
    }

    public function tambah($id){
        //$berkala = \App\berkala::findorfail($id);
        //$pangkat = \App\Pangkat::where('id',[$berkala['idpang']])->get();
        $pangkat = \App\Pangkat::findOrFail($id);
        return view ("berkala.create",["pangkat"=>$pangkat]);
    }
}
