<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pegawai;
use App\Jabatan;
use App\Cabang;

class SetUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (\Gate::allows('ADMIN')) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki hak akses');
        });
    }

    public function index(Request $request)
    {
        $cabangFilter = $request->get('cabang');
        $keyword = $request->keyword;
        $pegawai = Pegawai::with('relJabatan','relCabang')
        ->when($cabangFilter, function($query) use ($cabangFilter){
            $query->where('cabang', $cabangFilter);
        })
        ->when($keyword, function($query) use ($keyword){
            $query->where('name','LIKE',"%$keyword%");
        })
        ->paginate(15)
        ->appends($request->all());

        $cabangs = Cabang::pluck('name', 'id');
        foreach ($pegawai as $p) {
            $p->atasan1_data = Pegawai::where('jabatan', $p->atasan1)->first();
            $p->atasan2_data = Pegawai::where('jabatan', $p->atasan2)->first();
        }
        return view('setuser.index', compact('pegawai', 'cabangs', 'cabangFilter'));
    }

    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $jabatan = Jabatan::pluck('name', 'id');
        $cabang = Cabang::pluck('name', 'id');
        $atasan1 = Pegawai::where('jabatan', $pegawai->atasan1)->first();
        $atasan2 = Pegawai::where('jabatan', $pegawai->atasan2)->first();

        return view('setuser.edit', compact('pegawai', 'jabatan', 'cabang', 'atasan1', 'atasan2'));
    }

   public function update(Request $request, $id)
{
    $pegawai = Pegawai::findOrFail($id);

    $pegawai->jabatan = $request->get('jabatan');
    $pegawai->cabang  = $request->get('cabang');
    $pegawai->atasan1 = $request->get('atasan1');
    $pegawai->atasan2 = $request->get('atasan2');
    $pegawai->save();
    $user = \App\User::where('pegawai_id', $pegawai->id)->first();
    if ($user) {
        $user->cabang = $request->get('cabang');
        $user->save();
    }

    return redirect()->route('setuser.index')
        ->with('status', 'User berhasil diperbarui!');
}

}
