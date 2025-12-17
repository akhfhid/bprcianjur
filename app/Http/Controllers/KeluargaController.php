<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class KeluargaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __cinstruct(){
        $this->middleware(function($request, $next){
      if (Gate::allows('ADMIN') || Gate::allows('ADMIN_SDM')) {
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
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$iduser = \Auth::user();
        $new_keluarga = new \App\keluarga;

        $new_keluarga->name = $request->get('name');
        $new_keluarga->templahir = $request->get('templahir');
        $new_keluarga->tgllahir = $request->get('tgllahir');
        $new_keluarga->alamat = $request->get('alamat');
        $new_keluarga->agama = $request->get('agama');
        $new_keluarga->skawin = $request->get('status');
        $new_keluarga->pekerjaan = $request->get('pekerjaan');
        $new_keluarga->hubungan = $request->get('hubungan');
        $new_keluarga->pegawai_id = $request->get('idpeg');
        $new_keluarga->created_by = \Auth::user()->id;
        $new_keluarga->save();
        
        return redirect()->route('keluarga.list',$request->get('idpeg'))->with('status','Anggota Keluarga Berhasil Ditambahkan');


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
        $keluarga = \App\keluarga::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$keluarga['pegawai_id'])->first();
        $nikah = \App\Kawin::pluck('name','id');
        $hubkel = \App\Hubungan::pluck('name','id');
        $agama = \App\Agama::pluck('name','id');


        return view('keluarga.edit',['keluarga'=>$keluarga,'pegawai'=>$pegawai,'nikah'=>$nikah,'hubkel'=>$hubkel,'agama'=>$agama]);
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
        $datakeluarga = \App\keluarga::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$datakeluarga['pegawai_id'])->first();

        $datakeluarga->name = $request->get('name');
        $datakeluarga->templahir = $request->get('templahir');
        $datakeluarga->tgllahir = $request->get('tgllahir');
        $datakeluarga->alamat = $request->get('alamat');
        $datakeluarga->agama = $request->get('agama');
        $datakeluarga->skawin = $request->get('status');
        $datakeluarga->pekerjaan = $request->get('pekerjaan');
        $datakeluarga->hubungan = $request->get('hubungan');
        $datakeluarga->pegawai_id = $request->get('pegawai_id');
        $datakeluarga->updated_by = \Auth::user()->id;
        $datakeluarga->save();
       return redirect()->route('keluarga.list',$pegawai)->with('status','Data Pegawai Successfully Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keluarga = \App\Keluarga::findOrFail($id);
        $keluarga->delete();
        return redirect()->route('keluarga.list')->with('status','data moved to trash');
    }

    public function tambah($id)
    {
     $pegawai = \App\Pegawai::findOrFail($id);
        $nikah = \App\Kawin::pluck('name','id');
        $hubkel = \App\Hubungan::pluck('name','id');
        //$keluarga = \App\keluarga();
        $agama = \App\Agama::pluck('name','id'); 

        return view('keluarga.create',['pegawai'=>$pegawai,'nikah'=>$nikah,'hubkel'=>$hubkel,'agama'=>$agama]);    
    }

    public function list($id){
        $pegawai = \App\Pegawai::findOrFail($id);
        $datakeluarga = \App\keluarga::where('pegawai_id',[$pegawai['id']])->get();
        $data = [];
        $now = \Carbon\carbon::now()->format('Y-m-d');

        foreach ($datakeluarga as $x) {
        $bday = \Carbon\Carbon::parse($x['tgllahir']);
        $umur =$bday->diffinYears($now);
        $data[]= [
            'id'=> $x['id'],
            'name'=> $x['name'],
            'templahir' => $x['templahir'],
            'tgllahir'=> $x['tgllahir'],
            'umur' => $umur,
            'hub' => $x['hubungan'],
            'alamat' => $x['alamat']
        ];
        }

        return view('keluarga.index',['datakeluarga'=>$data,'pegawai'=>$pegawai]);
    }

    public function trash(){
        //$pegawai = \App\pegawai::findOrFail($id);
        //$idpeg= $pegawai->id;
        $keluarga = \App\keluarga::onlyTrashed();
        //$datakeluarga = $keluarga->get();
            //$keluarga = \App\keluarga::onlyTrashed()->paginate(10);
        //$datakeluarga = \App\keluarga::onlyTrashed();
        $datadelete = [];
        $now = \Carbon\carbon::now()->format('Y-m-d');

        foreach ($keluarga as $x) {
        $bday = \Carbon\Carbon::parse($x['tgllahir']);
        $datapegawai = \App\pegawai::where($x['id'],[$pegawai['id']])->get();
        $pegawai = $datapegawai->name;
        $umur =$bday->diffinYears($now);
        $datadelete[]= [
            'id'=> $x['id'],
            //'npeg'=>$pegawai,
            'name'=> $x['name'],
            'templahir' => $x['templahir'],
            'tgllahir'=> $x['tgllahir'],
            'umur' => $umur,
            'hub' => $x['hubungan'],
            'alamat' => $x['alamat']
        ];

        }
        return view('keluarga.trash',['keluarga'=>$datadelete]);
    }

    public function deletePermanent($id){
        $keluarga= \App\keluarga::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$keluarga['pegawai_id'])->first();
        $keluarga->forcedelete();
        return redirect()->route('keluarga.list',$pegawai)->with('status','Data Pegawai Successfully Deleted');

    }
}
