<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class UserController extends Controller
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
        $status =$request->get('status');
        $filterkeyword = $request->get('keyword');
        if($filterkeyword){
            $users = \App\User::where('name','LIKE',"%$filterkeyword%")
            //->where('status', $status)
            ->paginate(10);
        }else{
            $users = \App\User::where('name', 'LIKE', "%$filterkeyword%")
                    ->paginate(10);
        }
        return view('users.index',['users'=>$users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = \App\roles::pluck('name','ket');
        return view("users.create",['roles'=>$roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_user = new \App\User;
        $new_user->name =$request->get('name');
        $new_user->username = $request->get('username');
        $new_user->roles = $request->get("roles");
        $new_user->address = $request->get('address');
        $new_user->phone = $request->get('phone');
        $new_user->email=$request->get('email');
        $new_user->password = \Hash::make($request->get('password'));

        if ($request->file('avatar')){
            $file = $request->file('avatar')->store('avatars','public');
            $new_user->avatar = $file;
        }
        $new_user->save();
        return redirect()->route('users.create')->with('status','User Succesfully Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = \App\User::findOrFail($id);
        return view('users.show',['user'=>$user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = \App\User::findOrFail($id);
        $pegawai = \App\Pegawai::where('name',$user['name'])->first();
        $roles = \App\roles::pluck('name','ket');
        return view('users.edit',['user'=>$user, 'roles'=>$roles,'pegawai'=>$pegawai]);
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
        //$user = \App\User::findOrFail($id);
        //$pegawai = \App\Pegawai::where('name',$user['name'])->first();
        //$user->name = $request->get('name');
        //$user->roles =json_encode($request->get('roles'));
        //$user->address = $request->get('address');
        //$user->phone = $request->get('phone');
        //$user->status = $request->get('status');
        //$user->pegawai_id = $request->get('pegawai_id');


        //if($request->file('avatar')){
          //  if($user->avatar && file_exists(storage_path('app/public/'.$user->avatar))){
              //  \Storage::delete('public/'.$user->avatar);
            //}
            //$file =  $request->file('avatar')->store('avatar','public');
            //$user->avatar = $file;
        //}
        //$user->save();

        //return redirect()->route('users.index',[$id])->with('status','User Succesfully Updated');
        $user = \App\User::findOrFail($id);
        $user->name = $user->username;
        $user->password = \Hash::make($request->get('password'));
        $user->roles = $request->get('roles');
        $user->pegawai_id = $request->get('pegawai_id');
        $user->status = $request->get('status');
        $user->save();
        return redirect()->route('users.index')->with('status','User Berhasil Diaktifkan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \App\User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('status','User Succesfully Deleted');
    }
    public function active($id){
        $user = \App\User::findOrFail($id);
        $pegawai = \App\Pegawai::where('name',$user['username'])->first();
        //$user_id = $pegawai->id;
        $roles = \App\roles::pluck('name','ket');
         return view('users.active',['user'=>$user,'pegawai'=>$pegawai,'roles'=>$roles]);
    }
    public function aktif(Request $request,$id){
        $user = \App\User::findOrFail($id);
        $user->name = $request->get('name');
        $user->password = \Hash::make($request->get('password'));
        $user->roles = $request->get('roles');
        $user->pegawai_id = $request->get('pegawai_id');
        $user->status = $request->get('status');
        $user->save();
        return view('users.index')->with('status','User Berhasil Diaktifkan');
    }

    public function updateuser(Request $request,$id){
        $user = \App\User::findorfail($id);

        $user->name = $request->get('name');
        $user->roles =$request->get('roles');
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        $user->status = $request->get('status');
        $user->pegawai_id = $request->get('pegawai_id');


        if($request->file('avatar')){
        if($user->avatar && file_exists(storage_path('app/public/'.$user->avatar))){
        \Storage::delete('public/'.$user->avatar);
        }
        $file =  $request->file('avatar')->store('avatar','public');
        $user->avatar = $file;
        }
        $user->save();

        return redirect()->route('users.index',[$id])->with('status','User Succesfully Updated');
    }
}
