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
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::allows('ADMIN') || Gate::allows('ADMIN_SDM')) {
                return $next($request);
            }

            abort(403, 'Anda tidak memiliki hak akses');

            abort(403, 'Anda tidak memiliki hak akses');
        });
    }
    public function index(Request $request)
    {
        $filterkeyword = $request->keyword;
        $filterstatus = $request->status;
        $loginUser = auth()->user();

        $query = \App\User::query();

        if ($filterkeyword) {
            $query->where(function ($q) use ($filterkeyword) {
                $q->where('name', 'like', "%{$filterkeyword}%")->orWhere('username', 'like', "%{$filterkeyword}%");
            });
        }

        if ($filterstatus) {
            $query->where('status', $filterstatus);
        }

        if ($loginUser->id != 1) {
            $query->whereNotIn('id', [1, 10, 178]);
            // 1  = Administrator
            // 10 = Admin Kepatuhan
            // 178 = Admin SDM
        }

        $users = $query->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = \App\roles::pluck('name', 'ket');
        return view('users.create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_user = new \App\User();
        $new_user->name = $request->get('name');
        $new_user->username = $request->get('username');
        $new_user->roles = $request->get('roles');
        $new_user->address = $request->get('address');
        $new_user->phone = $request->get('phone');
        $new_user->email = $request->get('email');
        $new_user->loguser = $request->get('log');
        $new_user->password = \Hash::make($request->get('password'));

        if ($request->file('avatar')) {
            $file = $request->file('avatar')->store('avatars', 'public');
            $new_user->avatar = $file;
        }
        $new_user->save();
        return redirect()->route('users.create')->with('status', 'User Succesfully Created');
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
        // $pegawai = \App\Pegawai::where('name',$user['name'])->get();
        return view('users.show', ['user' => $user]);
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
        $pegawai = \App\Pegawai::where('name', $user['name'])->first();
        $roles = \App\roles::pluck('name', 'ket');
        return view('users.edit', ['user' => $user, 'roles' => $roles, 'pegawai' => $pegawai]);
        //return view('users.edit',['user'=>$user, 'roles'=>$roles]);
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
        return redirect()->route('users.index')->with('status', 'User Berhasil Diaktifkan');
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
        return redirect()->route('users.index')->with('status', 'User Succesfully Deleted');
    }
    public function active($id)
    {
        $user = \App\User::findOrFail($id);
        $pegawai = \App\Pegawai::where('name', $user['username'])->first();
        //$user_id = $pegawai->id;
        $roles = \App\roles::pluck('name', 'ket');
        return view('users.active', ['user' => $user, 'pegawai' => $pegawai, 'roles' => $roles]);
    }
    public function aktif(Request $request, $id)
    {
        $user = \App\User::findOrFail($id);
        $user->name = $request->get('name');
        $user->password = \Hash::make($request->get('password'));
        $user->roles = $request->get('roles');
        $user->pegawai_id = $request->get('pegawai_id');
        $user->status = $request->get('status');
        $user->save();
        return view('users.index')->with('status', 'User Berhasil Diaktifkan');
    }

    public function updateuser(Request $request, $id)
    {
        $user = \App\User::findorfail($id);

        $user->name = $request->get('name');
        $user->roles = $request->get('roles');
        $user->address = $request->get('address');
        $user->phone = $request->get('phone');
        $user->status = $request->get('status');
        $user->loguser = $request->get('log');
        $user->pegawai_id = $request->get('pegawai_id');

        if ($request->file('avatar')) {
            if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
                \Storage::delete('public/' . $user->avatar);
            }
            $file = $request->file('avatar')->store('avatar', 'public');
            $user->avatar = $file;
        }
        $user->save();

        return redirect()
            ->route('users.index', [$id])
            ->with('status', 'User Succesfully Updated');
    }
}
