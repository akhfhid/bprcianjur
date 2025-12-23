<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JabatanController extends Controller
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
    public function index(Request $request)
    {
        $jabatan = \App\Jabatan::paginate(10);
        $filterkeyword = $request->get('name');

        if($filterkeyword){
            $jabatan = \App\Jabatan::where("name", "LIKE", "%$filterkeyword%")->paginate(10);
        }

        return view('jabatan.index',['jabatan' => $jabatan]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = \App\Category::pluck("name","id");

        return view('jabatan.create',["jenis"=>$jenis]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $pensiun = $request->get('pensiun');
        $tunis = $request->get('tunis');
        $tunak = $request->get('tunak');
        $tunpang = $request->get('tunpang');
        $umak = $request->get('umak');
        $kantor = $request->get('kantor');
        $atasan = $request->get('atasan');
        //$jenis = $request->get('jenis');


        $new_jabatan = new \App\Jabatan;
        $new_jabatan->name = $name;
        $new_jabatan->pensiun = $pensiun;
        $new_jabatan->tunis = $tunis;
        $new_jabatan->tunak = $tunak;
        $new_jabatan->tunpang = $tunpang;
        $new_jabatan->umak = $umak;
        //$new_jabatan->jenis = $jenis;
        $new_jabatan->kantor = $kantor;
        $new_jabatan->atasan = $atasan;
        $new_jabatan->created_by = \Auth::user()->id;
        $new_jabatan->save();

        return redirect()->route('jabatan.index')->with('status','Jabatan Successfully Created');
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
        $jabatan = \App\Jabatan::findOrFail($id);
        $atasan1 = $jabatan->atasan;
        //$jenjab = $jabatan['jenis'];
      // $jenis = \App\Category::pluck("name","id");
       //$jenisjab = \App\Category::where('id',$jenjab)->first();
       // $namejab = $jenisjab['name'];
        $atasan = \App\Jabatan::pluck("name","id");
        $jabatasan = \App\Jabatan::where('id',$atasan1)->first();
            $jabname = $jabatasan['name'];
       // 'jenis'=>$jenis,'namejab'=>$namejab,

        return view ('jabatan.edit',['jabatan'=>$jabatan,'jabname'=>$jabname,
                        'atasan'=>$atasan]);
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
        $name = $request->get('name');
        $pensiun = $request->get('pensiun');
        $tunis = $request->get('tunis');
        $tunak = $request->get('tunak');
        $tunpang = $request->get('tunpang');
        $jenis = $request->get('jenis');
        $umak = $request->get('umak');
        $kantor = $request->get('kantor');
        $atasan = $request->get('atasan');


        $jabatan = \App\Jabatan::findOrFail($id);
        $jabatan->name =$name;
        $jabatan->pensiun=$pensiun;
        $jabatan->tunis=$tunis;
        $jabatan->tunak=$tunak;
        $jabatan->tunpang=$tunpang;
        //$jabatan->jenis = $jenis;
        $jabatan->umak=$umak;
        $jabatan->kantor = $kantor;
        $jabatan->atasan = $atasan;
        $jabatan->updated_by = \Auth::user()->id;
        $jabatan->save();
        return redirect()->route('jabatan.index')->with('status','Jabatan Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jabatan = \App\Jabatan::findOrFail($id);

        $jabatan->delete();

        return redirect()->route('jabatan.index')->with('status','Jabatan Successfully moved to trash');
    }

    public function trash(){
        $deleted_jabatan = \App\Jabatan::onlyTrashed()->paginate(10);
        return view('jabatan.trash',['jabatan'=> $deleted_jabatan]);
    }

    public function restore($id){

        $jabatan = \App\Jabatan::withTrashed()->findOrFail($id);
        if($jabatan->trashed()){
            $jabatan->restore();
        }else{
            return redirect()->route('jabatan.index')->with('status', 'Jabatan is not in trash');
        }
        return redirect()->route('jabatan.index')->with('status','Jabatan Successfully Restored');
    }

    public function deletePermanent($id){
        $jabatan = \App\Jabatan::withTrashed()->findOrFail($id);
        if(!$jabatan->trashed()){
            return redirect()->route('jabatan.index')->with('status','Can not Delete Permanent Active Jabatan');
        } else {
            $jabatan -> forceDelete();

            return redirect()->route('jabatan.trash')->with('status','Jabatan Permanently Deleted');
        }
    }
    public function ajaxsearch(Request $request){
        $keyword = $request->get('q');

            $jabatan = \App\Jabatan::where("name","LIKE","%$keyword%")->get();

            return $jabatan;
    }
}
