<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
class riwayatkerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware(function($request, $next){
 if (Gate::allows('ADMIN') || Gate::allows('ADMIN_SDM')|| Gate::allows('STAFF_SDM')) {
                return $next($request);
            }

            abort(403, 'Anda tidak memiliki hak akses');
        });
    }
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
        $new_riwayatkerja = new \App\riwayatkerja;

        $new_riwayatkerja->name = $request->get('jabatan');
        $new_riwayatkerja->kantorcabang = $request->get('cabang');
        $new_riwayatkerja->tglawal = $request->get('tglawal');
        $new_riwayatkerja->tglakhir = $request->get('tglakhir');
        
        $new_riwayatkerja->pegawai_id = $request->get('idpeg');
        $new_riwayatkerja->created_by = \Auth::user()->id;
        $new_riwayatkerja->save();
        return redirect()->route('riwayatkerja.list',$request->get('idpeg'))->with('status','data riwayat status kepegawaian berhasil');
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
        
        $riwayatkerja = \App\riwayatkerja::findorfail($id);
        $pegawai = \App\Pegawai::where('id',$riwayatkerja['pegawai_id'])->first();
        $jabatan = \App\Jabatan::pluck('name','id');
        $cabang = \App\Cabang::pluck('name','id');

        return view('riwayatkerja.edit',['riwayatkerja'=>$riwayatkerja,'pegawai'=>$pegawai,'jabatan'=>$jabatan,'cabang'=>$cabang]);
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
        $edit_riwayatkerja = \App\riwayatkerja::findorfail($id);
        $pegawai = \App\Pegawai::where('id',$edit_riwayatkerja['pegawai_id'])->first();

        $edit_riwayatkerja->name = $request->get('jabatan');
        $edit_riwayatkerja->kantorcabang = $request->get('cabang');
        $edit_riwayatkerja->tglawal = $request->get('tglawal');
        $edit_riwayatkerja->tglakhir = $request->get('tglakhir');
        $edit_riwayatkerja->pegawai_id = $request->get('idpeg');
        $edit_riwayatkerja->updated_by = \Auth::user()->id;
        $edit_riwayatkerja->save();
        return redirect()->route('riwayatkerja.list',$pegawai)->with('status','Data Pegawai Successfully Updated');
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
        $jabatan = \App\Jabatan::pluck('name','id');
        $cabang = \App\Cabang::pluck('name','id');
        return view('riwayatkerja.create',['pegawai'=>$pegawai, 'jabatan'=>$jabatan,'cabang'=>$cabang]);

    }
    public function list($id){
        $pegawai = \App\Pegawai::findorfail($id);

        $datariwayat = \App\riwayatkerja::where('pegawai_id',[$pegawai['id']])->get();
        $data =  [];
        foreach ($datariwayat as $datakerja){
             
            $awal = \Carbon\Carbon::parse($datakerja["tglawal"]);
            $akhir = \Carbon\Carbon::parse ($datakerja["tglakhir"]);
            if ($akhir == null) {
                $periode = $awal->diff($now)->format('%y Tahun %m Bulan');
            } else {
                $periode = $awal->diff($akhir)->format('%y Tahun %m Bulan');
            }
            
            

            $data[]=[
                "id" => $datakerja["id"],
                "name"=> $datakerja["name"],
                "kantor" => $datakerja["kantorcabang"],
                "tglawal" => $datakerja["tglawal"],
                "tglakhir" => $datakerja["tglakhir"],
                "periode" => $periode,


            ];
        }

        return view('riwayatkerja.index',['data'=>$data,'pegawai'=>$pegawai]);
    }
    public function deletePermanent($id){
        $riwayatkerja= \App\riwayatkerja::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$riwayatkerja['pegawai_id'])->first();
        $riwayatkerja->forcedelete();
        return redirect()->route('riwayatkerja.list',$pegawai)->with('status','Data Pegawai Successfully Deleted');
    }
}
