<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class KantorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware(function($request, $next){
    if (Gate::allows('ADMIN') || Gate::allows('ADMIN_SDM'|| Gate::allows('STAFF_SDM'))) {
                return $next($request);
            }

            abort(403, 'Anda tidak memiliki hak akses');
        });
    }
    public function index(Request $request)
    {
        $cabang = \App\Cabang::paginate(10);
        $filterkeyword = $request->get('name');
        if($filterkeyword){
            $cabang = \App\Cabang::where("name","LIKE", "%$filterkeyword")->paginate(10);
        }

        return view('cabang.index',['cabang' =>$cabang]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cabang.create');
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
        $class = $request->get('class');
        $tunjangan = $request->get('tunjangan');

        $new_cabang = new \App\Cabang;
        $new_cabang ->name = $name;
        $new_cabang ->class = $class;
        $new_cabang ->tunjangan = $tunjangan;
        $new_cabang ->created_by = \Auth::User()->id;
        $new_cabang ->save();
        return redirect()->route('cabang.create')->with('status', 'Kantor Cabang Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cabang = \App\Cabang::findOrFail($id);

        return view('cabang.show',['cabang' => $cabang]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cabang_to_edit = \App\Cabang::findOrFail($id);
        return view('cabang.edit',['cabang'=> $cabang_to_edit]);
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
        $class = $request->get('class');
        $tunjangan = $request->get('tunjangan');

        $cabang = \App\Cabang::findOrFail($id);

        $cabang->name = $name;
        $cabang->class = $class;
        $cabang->tunjangan = $tunjangan;
        $cabang->updated_by = \Auth::user()->id;
        $cabang->save();
        return redirect()->route('cabang.index',['cabang' => $cabang]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cabang = \App\Cabang::findOrFail($id);
        $cabang -> delete();

        return redirect()->route('cabang.index')->with('status','Cabang Successfully moved to trash');
    }

     public function trash()
    {
        $deleted_cabang = \App\Cabang::onlyTrashed()->paginate(10);
        return view('cabang.trash',['cabang' => $deleted_cabang]);
    }
    public function restore($id){
        $cabang = \App\Cabang::withTrashed()->findOrFail($id);

        if($cabang->trashed()){
            $cabang->restore();
        }else{
            return redirect()->route('cabang.index')->with('status','Cabang is not in trash');
        }
        return redirect()->route('cabang.index')->with('status','Cabang successfully restored');

    }
    public function deletePermanent($id){
        $cabang_trashed = \App\Cabang::withTrashed()->findOrFail($id);

        if(!$cabang_trashed->trashed()){
            return redirect()->route('cabang.index')->with('status','Cannot Delete Permanent Active Cabang');
        }else{
            $cabang_trashed->forceDelete();
            return redirect()->route('cabang.index')->with ('status','Cabang Permanently Deleted');
        }
    }
    public function ajaxsearch(Request $request){
        $keyword = $request->get('q');

            $cabang = \App\Cabang::where("name","LIKE","%$keyword%")->get();

            return $cabang;
    }
}
