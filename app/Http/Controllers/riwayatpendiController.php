<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class riwayatpendiController extends Controller
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
        
        $new_riwayatpendi = new \App\riwayatpendi;

        $new_riwayatpendi->pendidikan = $request->get("pendidikan");
        $new_riwayatpendi->name = $request->get("name");
        $new_riwayatpendi->jurusan = $request->get("jurusan");
        $new_riwayatpendi->gelar = $request->get("gelar");
        $new_riwayatpendi->thnlulus = $request->get("thnlulus");
        $new_riwayatpendi->pegawai_id = $request->get("idpeg");
        $new_riwayatpendi->created_by = \Auth::user()->id;
        $new_riwayatpendi->save();
        return redirect()->route('riwayatpendi.list',$request->get('idpeg'))->with('status','data riwayat pendidikan berhasil');
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
        $riwayatpendi = \App\riwayatpendi::findorfail($id);
        $pegawai = \App\Pegawai::where('id',$riwayatpendi['pegawai_id'])->first();
        $pendidikan = \App\Pendidikan::pluck('name','id');

        return view('riwayatpendi.edit',['riwayatpendi'=>$riwayatpendi,'pegawai'=>$pegawai,'pendidikan'=>$pendidikan]);
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
        $edit_riwayatpendi = \App\riwayatpendi::findorfail($id);
        $pegawai = \App\Pegawai::where('id',$edit_riwayatpendi['pegawai_id'])->first();
        $edit_riwayatpendi->pendidikan = $request->get("pendidikan");
        $edit_riwayatpendi->name = $request->get("name");
        $edit_riwayatpendi->jurusan = $request->get("jurusan");
        $edit_riwayatpendi->gelar = $request->get("gelar");
        $edit_riwayatpendi->thnlulus = $request->get("thnlulus");
        $edit_riwayatpendi->pegawai_id = $request->get("pegawai_id");       
        $edit_riwayatpendi->updated_by = \Auth::user()->id;
        $edit_riwayatpendi->save();

        return redirect()->route('riwayatpendi.list',$pegawai)->with('status','Data Pegawai Successfully Updated');
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

    public function tambah($id){
        $pegawai = \App\Pegawai::findorfail($id);
        $pendidikan = \App\Pendidikan::pluck('name','id');

        return view('riwayatpendi.create',['pegawai'=>$pegawai, 'pendidikan'=>$pendidikan]);

    }

    public function list($id){
        $pegawai = \App\Pegawai::findorfail($id);
        $datariwayat = \App\riwayatpendi::where('pegawai_id',[$pegawai['id']])->paginate(10);

        return view('riwayatpendi.index',['datariwayat'=>$datariwayat,'pegawai'=>$pegawai]);
    }
    public function deletePermanent($id){
        $riwayatpendi= \App\riwayatpendi::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$riwayatpendi['pegawai_id'])->first();
        $riwayatpendi->forcedelete();
        return redirect()->route('riwayatpendi.list',$pegawai)->with('status','Data Pegawai Successfully Deleted');

    }
}
