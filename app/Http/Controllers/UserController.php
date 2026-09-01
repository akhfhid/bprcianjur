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
            if (Gate::allows('ADMIN') || Gate::allows('ADMIN_SDM') || Gate::allows('STAFF_SDM')) {
                return $next($request);
            }

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
        if (!$pegawai && $user->pegawai_id) {
            $pegawai = \App\Pegawai::find($user->pegawai_id);
        }
        $roles = \App\roles::pluck('name', 'ket');
        $cabangs = \App\Cabang::orderBy('name')->pluck('name', 'id');
        $kantor = \App\Cabang::orderBy('name')->pluck('name', 'id');
        // Prioritaskan nilai cabang dari users.cabang, fallback ke pegawais.cabang
        $activeCabangId = $user->cabang ?? ($pegawai ? $pegawai->cabang : null);
        $currentCabang = $activeCabangId ? \App\Cabang::find($activeCabangId) : null;
        return view('users.edit', [
            'user'          => $user,
            'roles'         => $roles,
            'pegawai'       => $pegawai,
            'cabangs'       => $cabangs,
            'kantor'        => $kantor,
            'currentCabang' => $currentCabang,
            'activeCabangId'=> $activeCabangId,
        ]);
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
        $user->roles = $request->get('roles');
        $user->pegawai_id = $request->get('pegawai_id');
        $user->status = $request->get('status');

        // Hanya update password jika field diisi
        $newPassword = $request->get('password');
        if (!empty($newPassword)) {
            $user->password = \Hash::make($newPassword);
        }

        $user->save();
        return redirect()->route('users.index')->with('status', 'User Berhasil Diperbarui');
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
        // $user->roles = $request->get('roles');
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

        // Simpan cabang ke tabel users
        $cabangId = $request->get('cabang_id');
        if ($cabangId !== null) {
            $user->cabang = $cabangId ?: null;
        }

        if ($request->file('avatar')) {
            if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
                \Storage::delete('public/' . $user->avatar);
            }
            $file = $request->file('avatar')->store('avatar', 'public');
            $user->avatar = $file;
        }
        $user->save();

        // Sync cabang & tunjangan kinerja ke tabel pegawais (sinkronisasi dua arah)
        $targetPegawai = null;
        if ($user->pegawai_id) {
            $targetPegawai = \App\Pegawai::find($user->pegawai_id);
        }
        if (!$targetPegawai) {
            $targetPegawai = \App\Pegawai::where('name', $user->name)->first();
        }

        if ($targetPegawai) {
            if ($cabangId !== null) {
                $targetPegawai->cabang = $cabangId ?: null;
            }

            $tuncabType = $request->get('tuncab_type');
            if ($tuncabType === 'custom') {
                $targetPegawai->is_custom_tuncab = 1;
                $rawVal = floatval($request->get('custom_tuncab_val'));
                if ($rawVal > 1) {
                    $targetPegawai->custom_tuncab_val = $rawVal / 100;
                } else {
                    $targetPegawai->custom_tuncab_val = $rawVal;
                }
            } elseif ($tuncabType === 'cabang') {
                $targetPegawai->is_custom_tuncab = 0;
                $targetPegawai->custom_tuncab_val = null;
                if ($request->has('tuncab')) {
                    $targetPegawai->tuncab = $request->get('tuncab') ?: null;
                } elseif ($cabangId !== null) {
                    $targetPegawai->tuncab = $cabangId ?: null;
                }
            }
            $targetPegawai->save();
        }

        return redirect()
            ->route('users.index')
            ->with('status', 'User berhasil diperbarui');
    }

    /**
     * Tampilkan form edit hak akses menu untuk user tertentu.
     * Hanya bisa diakses oleh ADMIN.
     */
    public function editHakAkses($id)
    {
        if (!Gate::allows('ADMIN')) {
            abort(403, 'Anda tidak memiliki hak akses');
        }

        $user = \App\User::findOrFail($id);

        // Daftar semua menu yang tersedia beserta labelnya
        $availableMenus = $this->getAvailableMenus();

        return view('users.hak-akses', compact('user', 'availableMenus'));
    }

    /**
     * Simpan hak akses menu untuk user tertentu.
     * Hanya bisa diakses oleh ADMIN.
     */
    public function updateHakAkses(Request $request, $id)
    {
        if (!Gate::allows('ADMIN')) {
            abort(403, 'Anda tidak memiliki hak akses');
        }

        $user = \App\User::findOrFail($id);

        // Jika reset (gunakan default roles), set null
        if ($request->get('use_default') == '1') {
            $user->menu_permissions = null;
        } else {
            // Simpan array menu yang dicentang (bisa kosong array)
            $user->menu_permissions = $request->get('menus', []);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('status', 'Hak akses menu untuk user "' . $user->username . '" berhasil diperbarui');
    }

    /**
     * Kembalikan daftar menu yang tersedia beserta grupnya.
     *
     * @return array
     */
    private function getAvailableMenus(): array
    {
        return [
            'Master Data' => [
                'jabatan'    => 'Jabatan',
                'pangkat'    => 'Pangkat',
                'cabang'     => 'Kantor Cabang',
            ],
            'Manajemen User' => [
                'users'      => 'Manajemen User',
                'loguser'    => 'Log User Aktivitas',
                'setuser'    => 'Set User',
            ],
            'Kepegawaian' => [
                'pegawai'    => 'Data Pegawai',
                'riwayat'    => 'Riwayat Pegawai',
                'berkala'    => 'Berkala / Kenaikan Gaji',
                'pelatihan'  => 'Pelatihan',
                'keluarga'   => 'Data Keluarga',
            ],
            'Keuangan & Penghasilan' => [
                'penghasilan' => 'Penghasilan',
                'gaji'        => 'Gaji',
            ],
            'Cuti' => [
                'ordercuti'  => 'Manajemen Cuti (SDM)',
                'cuti'       => 'Cuti',
            ],
            'Mutasi' => [
                'mutasi'         => 'Mutasi Jabatan',
                'mutasipangkat'  => 'Mutasi Pangkat',
            ],
            'Peraturan & Kepatuhan' => [
                'peraturan'  => 'Peraturan',
                'kepatuhan'  => 'Kepatuhan',
                'categories' => 'Kategori Peraturan',
            ],
            'Administrasi' => [
                'asisten_sikap' => 'Asisten SIKAP AI',
                'wa_setting'    => 'Setting WhatsApp',
                'resetpassword' => 'Reset Password User',
            ],
        ];
    }
}

