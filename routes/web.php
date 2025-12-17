<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});
route::get('/reset', function () {
    return view('auth.passwords.reset');
})->name('reset');
Auth::routes();
route::match(['GET', 'POST'], '/register', function () {
    return redirect('/login');
})->name('register');

route::middleware(['auth'])->group(function () {
    Route::prefix('set-user')
        ->middleware('auth')
        ->group(function () {
            Route::get('/', 'SetUserController@index')->name('setuser.index');
            Route::get('/edit/{id}', 'SetUserController@edit')->name('setuser.edit');
            Route::post('/update/{id}', 'SetUserController@update')->name('setuser.update');
        });
    Route::post('/pegawai/toggle-active/{id}', 'PegawaiController@toggleActive')->name('pegawai.toggle-active');

    route::get('users/{id}/active', 'UserController@active')->name('users.active');
    route::post('users/{id}/update', 'UserController@updateuser')->name('users.updateuser');
    route::get('users/{id}/edit' . 'UserController@edit')->name('users.edit');
    Route::resource('users', 'UserController');
    Route::get('/home', 'HomeController@index');
    Route::get('/categories/trash', 'CategoriesController@trash')->name('categories.trash');
    Route::get('/categories/{id}/restore', 'CategoriesController@restore')->name('categories.restore');
    Route::delete('/categories/{category}/delete-permanent', 'CategoriesController@deletePermanent')->name('categories.delete-permanent');
    Route::resource('categories', 'CategoriesController');

    Route::get('/cabang/trash', 'KantorController@trash')->name('cabang.trash');
    Route::Get('/cabang/{id}/restore', 'KantorController@restore')->name('cabang.restore');
    Route::delete('/cabang/{cabang}/delete-permanent', 'KantorController@deletePermanent')->name('cabang.delete-permanent');
    Route::resource('cabang', 'KantorController');

    Route::get('/jabatan/trash', 'JabatanController@trash')->name('jabatan.trash');
    Route::get('/jabatan/{id}/restore', 'JabatanController@restore')->name('jabatan.restore');
    Route::delete('/jabatan/{jabatan}/delete-permanent', 'JabatanController@deletePermanent')->name('jabatan.delete-permanent');
    Route::resource('jabatan', 'JabatanController');

    Route::get('/pangkat/trash', 'PangkatController@trash')->name('pangkat.trash');
    Route::get('/pangkat/{id}/restore', 'PangkatController@restore')->name('pangkat.restore');
    Route::delete('/pangkat/{pangkat}/delete-permanent', 'PangkatController@deletePermanent')->name('pangkat.delete-permanent');
    route::resource('pangkat', 'PangkatController');

    //Route::get('/ajax/pangkat/search','PangkatController@ajaxsearch');
    //Route::get('pegawai','PegawaiController@create')->name('pegawai.create');
    Route::get('/pegawai/data', 'PegawaiController@data')->name('pegawai.data');
    Route::get('/pegawai/trash', 'PegawaiController@trash')->name('pegawai.trash');
    Route::delete('/pegawai/{pegawai}/delete-permanent', 'PegawaiController@deletePermanent')->name('pegawai.delete-permanent');
    Route::post('/pegawai/{pegawai}/restore', 'PegawaiController@restore')->name('pegawai.restore');
    route::get('pegawai/{id}/cetak', 'PegawaiController@cetakpdf')->name('pegawai.cetak');
    route::post('Pegawai/Simpan', 'PegawaiController@simpan')->name('pegawai.simpan');
    route::get('Pegawai/Input', 'PegawaiController@input')->name('pegawai.input');
    route::get('/Pegawai/Jadwal', 'PegawaiController@listberkala')->name('pegawai.listberkala');
    route::get('/Pegawai/{pegawai}/Berkala/', 'PegawaiController@EditBerkala')->name('pegawai.editberkala');
    route::post('/Pegawai/Updateberkala/{pegawai}', 'PegawaiController@UpdateBerkala')->name('pegawai.updateberkala');
    route::get('/Pegawai/JadwalBerkala', 'PegawaiController@jadwalberkala')->name('pegawai.jadwalberkala');
    route::get('/Pegawai/BerkalaPangkat', 'PegawaiController@berkalapangkat')->name('pegawai.berkalapangkat');
    route::get('/Pegawai/ListPangkat', 'PegawaiController@datapangkat')->name('pegawai.datapangkat');
    route::get('/Pegawai/JadwalPangkat', 'PegawaiController@listpangkat')->name('pegawai.listpangkat');
    Route::resource('pegawai', 'PegawaiController');

    route::get('keluarga/{id}', 'KeluargaController@tambah')->name('keluarga.tambah');
    route::post('keluarga/{id}', 'KeluargaController@update')->name('keluarga.update');
    route::get('keluarga/{id}/list', 'KeluargaController@list')->name('keluarga.list');
    Route::get('/keluarga/trash', 'KeluargaController@trash')->name('keluarga.trash');
    route::get('/keluarga/{id}/restore', 'KeluargaController@restore')->name('keluarga.restore');
    route::delete('/keluarga/{keluarga}/delete-permanent', 'KeluargaController@deletePermanent')->name('keluarga.delete-permanent');
    route::resource('keluarga', 'KeluargaController');

    route::get('riwayatpendi/{id}', 'riwayatpendiController@tambah')->name('riwayatpendi.tambah');
    route::get('riwayatpendi/{id}/list', 'riwayatpendiController@list')->name('riwayatpendi.list');
    route::post('riwayatpendi/{id}', 'riwayatpendiController@update')->name('riwayatpendi.update');
    route::delete('/riwayatpendi/{riwayatpendi}/delete-permanent', 'riwayatpendiController@deletePermanent')->name('riwayatpendi.delete-permanent');
    route::resource('riwayatpendi', 'riwayatpendiController');

    route::get('riwayatkerja/{id}', 'riwayatkerjaController@tambah')->name('riwayatkerja.tambah');
    route::get('riwayatkerja/{id}/list', 'riwayatkerjaController@list')->name('riwayatkerja.list');
    route::post('riwayatkerja/{id}', 'riwayatkerjaController@update')->name('riwayatkerja.update');
    route::delete('/riwayatkerja/{riwayatkerja}/delete-permanent', 'riwayatkerjaController@deletePermanent')->name('riwayatkerja.delete-permanent');
    route::resource('riwayatkerja', 'riwayatkerjaController');

    route::get('pelatihan/{id}', 'pelatihanController@tambah')->name('pelatihan.tambah');
    route::get('pelatihan/{id}/list', 'pelatihanController@list')->name('pelatihan.list');
    route::post('pelatihan/{id}', 'pelatihanController@update')->name('pelatihan.update');
    route::delete('pelatihan/{pelatihan}/delete-permanent', 'pelatihanController@deletePermanent')->name('pelatihan.delete-permanent');
    route::resource('pelatihan', 'pelatihanController');

    route::POST('ordercuti/{id}/setuju', 'ordercutiController@setuju')->name('ordercuti.setuju');
    route::post('ordercuti/{id}/tolak', 'ordercutiController@tolak')->name('ordercuti.tolak');
    route::get('ordercuti/dataapprove', 'ordercutiController@disetujui')->name('ordercuti.datasetuju');
    route::get('ordercuti/datatolak', 'ordercutiController@ditolak')->name('ordercuti.datatolak');
    route::get('ordercuti/cutiwajib', 'ordercutiController@cutiwajib')->name('ordercuti.cutiwajib');
    route::get('ordercuti/cutilainnya', 'ordercutiController@cutilainnya')->name('ordercuti.cutilainnya');
    route::get('ordercuti/indexcutiwajib', 'ordercutiController@indexcutiwajib')->name('ordercuti.indexcutiwajib');
    route::get('ordercuti/indexcuti', 'ordercutiController@indexcuti')->name('ordercuti.indexcuti');
    route::get('ordercuti/indexcutilainnya', 'ordercutiController@indexcutilainnya')->name('ordercuti.indexcutilainnya');
    route::get('ordercuti/indexcutitahunan', 'ordercutiController@indexcutitahunan')->name('ordercuti.indexcutitahunan');
    //route::post('ordercuti/update','ordercutiController@update')->name('ordercuti.update');
    route::resource('ordercuti', 'ordercutiController');

    Route::get('/peraturan/trash', 'TrashController@trash')->name('peraturan.trash');
    Route::get('/peraturan/{id}/restore', 'peraturanController@restore')->name('peraturan.restore');
    Route::delete('/peraturan/{peraturan}/delete-permanent', 'peraturanController@deletePermanent')->name('peraturan.delete-permanent');
    Route::get('Peraturan/Print/{id}', 'peraturanController@show_pdf')->name('peraturan.show_pdf');
    route::post('/Peraturan/Update/{id}', 'peraturanController@simpanedit')->name('peraturan.simpanedit');
    route::get('/peraturan/showtrash/{id}', 'peraturanController@showtrash')->name('peraturan.showtrash');
    Route::get('peraturan/destroy/{id}', 'peraturanController@destroy')->name('peraturan.destroy');
    route::get('peraturan/{id}/edit', 'peraturanController@edit')->name('peraturan.edit');
    route::resource('peraturan', 'peraturanController');

    route::POST('mutasi/{id}/setuju', 'mutasiController@setuju')->name('mutasi.setuju');
    route::post('mutasi/{id}/tolak', 'mutasiController@tolak')->name('mutasi.tolak');
    route::get('mutasi/dataapprove', 'mutasiController@disetujui')->name('mutasi.disetujui');
    route::get('mutasi/datatolak', 'mutasiController@ditolak')->name('mutasi.ditolak');
    route::resource('mutasi', 'mutasiController');

    route::POST('mutasipangkat/{id}/setuju', 'mutasipangkatController@setuju')->name('mutasipangkat.setuju');
    route::post('mutasipangkat/{id}/tolak', 'mutasipangkatController@tolak')->name('mutasipangkat.tolak');
    route::get('mutasipangkat/setuju', 'mutasipangkatController@disetujui')->name('mutasipangkat.setuju');
    route::get('mutasipangkat/tolak', 'mutasipangkatController@ditolak')->name('mutasipangkat.tolak');
    route::resource('mutasipangkat', 'mutasipangkatController');

    //route user staff
    route::get('staff/daftarcuti', 'StaffController@cuti')->name('staff.cuti');
    route::get('staff/cuti', 'StaffController@permohonancuti')->name('staff.permohonancuti');
    route::post('staff/mintacuti', 'StaffController@mintacuti')->name('staff.mintacuti');
    route::get('staff/cutisetuju', 'StaffController@cutisetuju')->name('staff.cutisetuju');
    route::get('staff/cutitolak', 'StaffController@cutitolak')->name('staff.cutitolak');
    route::get('staff/profile', 'StaffController@profile')->name('staff.profile');
    route::get('staff/peraturan', 'StaffController@peraturan')->name('staff.peraturan');
    route::get('staff/peraturan/{id}', 'StaffController@permohonandownload')->name('staff.permohonandownload');
    route::post('staff/download', 'StaffController@mintadownload')->name('staff.mintadownload');
    route::get('staff/status', 'StaffController@statusatur')->name('staff.status');
    route::get('Staff/Peraturan/Show/{id}', 'StaffController@showatur')->name('staff.showatur');
    route::get('Staff/Peraturan/Print/{id}', 'StaffController@show_pdf')->name('staff.show_pdf');
    route::get('staff/Cuti/CutiWajib', 'StaffController@cutiwajib')->name('staff.cutiwajib');
    route::get('staff/Cuti/CutiLainnya', 'StaffController@cutilainnya')->name('staff.cutilainnya');
    route::resource('staff', 'StaffController');

    route::get('Supervisor/IndexPegawai', 'SupervisorController@indexpegawai')->name('supervisor.indexpegawai');
    route::Get('Supervisor/ProfilePegawai/{id}', 'SupervisorController@detailpegawai')->name('supervisor.detailpegawai');
    route::get('Supervisor/Edit/{id}', 'SupervisorController@editpegawai')->name('supervisor.editpegawai');
    route::post('Supervisor/Update', 'SupervisorController@updatepegawai')->name('supervisor.updatepegawai');
    route::Get('Supervisor/Cuti/Pegawai', 'SupervisorController@cutiindex')->name('supervisor.cutiindex');
    route::Get('Supervisor/Cuti/Approve', 'SupervisorController@cutisetuju')->name('supervisor.cutisetuju');
    route::Get('Supervisor/Cuti/Tolak', 'SupervisorController@cutitolak')->name('supervisor.cutitolak');
    route::Get('Supervisor/Cuti/Supervisor', 'SupervisorController@cutisupervisor')->name('supervisor.cutisupervisor');
    route::Get('Supervisor/Cuti/SupervisorTolak', 'SupervisorController@tolakcuti')->name('supervisor.tolakcuti');
    route::Get('Supervisor/Cuti/SupervisorSetuju', 'SupervisorController@setujucuti')->name('supervisor.setujucuti');
    route::Get('Supervisor/PermohonanCuti', 'SupervisorController@permohonancuti')->name('supervisor.permohonancuti');
    route::post('Supervisor/mintacuti', 'SupervisorController@mintacuti')->name('supervisor.mintacuti');
    route::POST('Supervisor/{id}/setuju', 'SupervisorController@setuju')->name('supervisor.setuju');
    route::post('Supervisor/{id}/tolak', 'SupervisorController@tolak')->name('supervisor.tolak');
    route::get('Supervisor/Rotasi/Pegawai', 'SupervisorController@rotasipegawai')->name('supervisor.pegawairotasi');
    route::get('Supervisor/Rotasi/Permohonan/{id}', 'SupervisorController@mintarotasi')->name('supervisor.permohonanrotasi');
    route::post('Supervisor/Rotasi/Input', 'SupervisorController@inputrotasi')->name('supervisor.inputrotasi');
    route::get('Supervisor/Rotasi/Index', 'SupervisorController@datarotasi')->name('supervisor.datarotasi');
    route::get('Supervisor/Rotasi/Setuju', 'SupervisorController@setujurotasi')->name('supervisor.setujurotasi');
    route::get('Supervisor/Rotasi/Tolak', 'SupervisorController@tolakrotasi')->name('supervisor.tolakrotasi');
    route::get('Supervisor/Profile', 'SupervisorController@profile')->name('supervisor.profile');
    route::get('Supervisor/Peraturan', 'SupervisorController@peraturan')->name('supervisor.peraturan');
    route::get('Supervisor/Peraturan/{id}', 'SupervisorController@permohonandownload')->name('supervisor.permohonandownload');
    route::post('Supervisor/Download', 'SupervisorController@mintadownload')->name('supervisor.mintadownload');
    route::get('supervisor/status', 'SupervisorController@statusatur')->name('supervisor.status');
    route::get('Supervisor/Peraturan/Show/{id}', 'SupervisorController@showatur')->name('supervisor.showatur');
    route::get('Supervisor/Peraturan/Print/{id}', 'SupervisorController@show_pdf')->name('supervisor.show_pdf');
    route::get('Supervisor/Cuti/CutiWajib', 'SupervisorController@cutiwajib')->name('supervisor.cutiwajib');
    route::get('Supervisor/Cuti/CutiLainnya', 'SupervisorController@cutilainnya')->name('supervisor.cutilainnya');
    route::resource('supervisor', 'SupervisorController');

    route::get('Pincab/IndexPegawai', 'PincabController@indexpegawai')->name('pincab.indexpegawai');
    route::get('Pincab/Profile', 'PincabController@profile')->name('pincab.profile');
    route::get('Pincab/ProfilePegawai/{id}', 'PincabController@detailpegawai')->name('pincab.detailpegawai');
    route::get('Pincab/Cuti/', 'PincabController@cutipincab')->name('pincab.cutipincab');
    route::get('Pincab/Cuti/Permohonan', 'PincabController@permohonancuti')->name('pincab.permohonancuti');
    route::post('Pincab/Cuti/Input', 'PincabController@mintacuti')->name('pincab.mintacuti');
    route::get('Pincab/Cuti/Setuju', 'PincabController@setujucuti')->name('pincab.setujucuti');
    route::get('Pincab/Cuti/Tolak', 'PincabController@tolakcuti')->name('pincab.tolakcuti');
    route::get('Pincab/Cuti/Index', 'PincabController@cutiindex')->name('pincab.cutiindex');
    route::post('Pincab/Cuti/Approve/{id}', 'PincabController@setuju')->name('pincab.setuju');
    route::post('Pincab/Cuti/Ignore/{id}', 'PincabController@tolak')->name('pincab.tolak');
    route::get('Pincab/Cuti/Pegawai/Setuju', 'PincabController@cutisetuju')->name('pincab.cutisetuju');
    route::Get('Pincab/Cuti/Pegawai/Tolak', 'PincabController@cutitolak')->name('pincab.cutitolak');
    route::get('Pincab/Rotasi/Index', 'PincabController@rotasiindex')->name('pincab.rotasiindex');
    route::post('Pincab/Rotasi/Approve/{id}', 'PincabController@rotasisetuju')->name('pincab.setujurotasi');
    route::post('Pincab/Rotasi/Ignore/{id}', 'PincabController@rotasitolak')->name('pincab.tolakrotasi');
    route::get('Pincab/Rotasi/Setuju', 'PincabController@rotasidisetujui')->name('pincab.rotasidisetujui');
    route::get('Pincab/Rotasi/Tolak', 'PincabController@rotasiditolak')->name('pincab.rotasiditolak');
    route::get('Pincab/Peraturan', 'PincabController@peraturan')->name('pincab.peraturan');
    route::get('Pincab/Peraturan/{id}', 'PincabController@permohonandownload')->name('pincab.permohonandownload');
    route::post('Pincab/Peraturan/Download', 'PincabController@mintadownload')->name('pincab.mintadownload');
    route::get('pincab/status', 'PincabController@statusatur')->name('pincab.status');
    route::get('Pincab/Peraturan/Show/{id}', 'PincabController@showatur')->name('pincab.showatur');
    route::get('Pincab/Peraturan/print/{id}', 'PincabController@print_pdf')->name('pincab.show_pdf');
    route::get('Pincab/Cuti/CutiWajib', 'PincabController@cutiwajib')->name('pincab.cutiwajib');
    route::get('Pincab/Cuti/CutiLainnya', 'PincabController@cutilainnya')->name('pincab.cutilainnya');
    route::resource('pincab', 'PincabController');

    route::get('Kadiv/Profile', 'KadivController@profile')->name('kadiv.profile');
    route::get('Kadiv/Pegawai/Index', 'KadivController@indexpegawai')->name('kadiv.indexpegawai');
    route::get('Kadiv/Pegawai/Detail/{id}', 'KadivController@detailpegawai')->name('kadiv.detailpegawai');
    route::get('Kadiv/Cuti/Index', 'KadivController@cutikadiv')->name('kadiv.cutikadiv');
    route::get('Kadiv/Cuti/Permohonan', 'KadivController@permohonancuti')->name('kadiv.permohonancuti');
    route::post('Kadiv/Cuti/Input', 'KadivController@mintacuti')->name('kadiv.mintacuti');
    route::get('Kadiv/Cuti/Setuju', 'KadivController@setujucuti')->name('kadiv.setujucuti');
    route::get('Kadiv/Cuti/Tolak', 'KadivController@tolakcuti')->name('kadiv.tolakcuti');
    route::get('Kadiv/Cuti/Pegawai/Index', 'KadivController@cutiindex')->name('kadiv.cutiindex');
    route::get('Kadiv/Cuti/Pegawai/Setuju', 'KadivController@cutisetuju')->name('kadiv.cutisetuju');
    route::get('Kadiv/Cuti/Pegawai/Tolak', 'KadivController@cutitolak')->name('kadiv.cutitolak');
    route::post('Kadiv/Cuti/Pegawai/Approve/{id}', 'KadivController@setuju')->name('kadiv.setuju');
    route::post('Kadiv/Cuti/Pegawai/Ignore/{id}', 'KadivController@tolak')->name('kadiv.tolak');
    route::get('Kadiv/Rotasi', 'KadivController@rotasipegawai')->name('kadiv.pegawairotasi');
    route::get('Kadiv/Rotasi/Permohonan/{id}', 'KadivController@mintarotasi')->name('kadiv.permohonanrotasi');
    route::post('Kadiv/Rotasi/Input', 'KadivController@inputrotasi')->name('kadiv.inputrotasi');
    route::get('Kadiv/Rotasi/Index', 'KadivController@datarotasi')->name('kadiv.datarotasi');
    route::get('Kadiv/Rotasi/Setuju', 'KadivController@setujurotasi')->name('kadiv.setujurotasi');
    route::get('Kadiv/Rotasi/Tolak', 'KadivController@tolakrotasi')->name('kadiv.tolakrotasi');
    route::get('Kadiv/Peraturan', 'KadivController@peraturan')->name('kadiv.peraturan');
    route::get('Kadiv/Peraturan/{id}', 'KadivController@permohonandownload')->name('kadiv.permohonandownload');
    route::post('Kadiv/Peraturan/Download', 'KadivController@mintadownload')->name('kadiv.mintadownload');
    route::get('Kadiv/status', 'KadivController@statusatur')->name('kadiv.status');
    route::get('Kadiv/Peraturan/Show/{id}', 'KadivController@showatur')->name('kadiv.showatur');
    route::get('Kadiv/Peraturan/Print/{id}', 'KadivController@show_pdf')->name('kadiv.show_pdf');
    //route::get('Kadiv/Peraturan/Atur','KadivController@index')->name('kadiv.index');
    route::get('Kadiv/Peraturan/ShowTrash/{id}', 'KadivController@showtrash')->name('kadiv.showtrash');
    route::get('Kadiv/Cuti/CutiWajib', 'KadivController@cutiwajib')->name('kadiv.cutiwajib');
    route::get('Kadiv/Cuti/CutiLainnya', 'KadivController@cutilainnya')->name('kadiv.cutilainnya');
    route::resource('Kadiv', 'KadivController');

    route::get('Kepatuhan/IndexPegawai', 'KepatuhanController@indexpegawai')->name('kepatuhan.indexpegawai');
    route::Get('Kepatuhan/ProfilePegawai/{id}', 'KepatuhanController@detailpegawai')->name('kepatuhan.detailpegawai');
    route::get('Kepatuhan/Edit/{id}', 'KepatuhanController@editpegawai')->name('kepatuhan.editpegawai');
    route::post('Kepatuhan/Update', 'KepatuhanController@updatepegawai')->name('kepatuhan.updatepegawai');
    route::Get('Kepatuhan/Cuti/Pegawai', 'KepatuhanController@cutiindex')->name('kepatuhan.cutiindex');
    route::Get('Kepatuhan/Cuti/Approve', 'KepatuhanController@cutisetuju')->name('kepatuhan.cutisetuju');
    route::Get('Kepatuhan/Cuti/Tolak', 'KepatuhanController@cutitolak')->name('kepatuhan.cutitolak');
    route::Get('Kepatuhan/Cuti/Kepatuhan', 'KepatuhanController@cutikepatuhan')->name('kepatuhan.cutikepatuhan');
    route::Get('Kepatuhan/Cuti/KepatuhanTolak', 'KepatuhanController@tolakcuti')->name('kepatuhan.tolakcuti');
    route::Get('Kepatuhan/Cuti/KepatuhanSetuju', 'KepatuhanController@setujucuti')->name('kepatuhan.setujucuti');
    route::Get('Kepatuhan/PermohonanCuti', 'KepatuhanController@permohonancuti')->name('kepatuhan.permohonancuti');
    route::post('Kepatuhan/mintacuti', 'KepatuhanController@mintacuti')->name('kepatuhan.mintacuti');
    route::POST('Kepatuhan/{id}/setuju', 'KepatuhanController@setuju')->name('kepatuhan.setuju');
    route::post('Kepatuhan/{id}/tolak', 'KepatuhanController@tolak')->name('kepatuhan.tolak');
    route::get('Kepatuhan/Rotasi/Pegawai', 'KepatuhanController@rotasipegawai')->name('kepatuhan.pegawairotasi');
    route::get('Kepatuhan/Rotasi/Permohonan/{id}', 'KepatuhanController@mintarotasi')->name('kepatuhan.permohonanrotasi');
    route::post('Kepatuhan/Rotasi/Input', 'KepatuhanController@inputrotasi')->name('kepatuhan.inputrotasi');
    route::get('Kepatuhan/Rotasi/Index', 'KepatuhanController@datarotasi')->name('kepatuhan.datarotasi');
    route::get('Kepatuhan/Rotasi/Setuju', 'KepatuhanController@setujurotasi')->name('kepatuhan.setujurotasi');
    route::get('Kepatuhan/Rotasi/Tolak', 'KepatuhanController@tolakrotasi')->name('kepatuhan.tolakrotasi');
    route::get('Kepatuhan/Profile', 'KepatuhanController@profile')->name('kepatuhan.profile');
    route::get('Kepatuhan/Peraturan', 'KepatuhanController@statusatur')->name('kepatuhan.statusatur');
    route::post('Kepatuhan/Peraturan/Approve/{id}', 'KepatuhanController@setujuatur')->name('kepatuhan.setujuatur');
    route::post('Kepatuhan/Peraturan/Ignore/{id}', 'KepatuhanController@tolakatur')->name('kepatuhan.tolakatur');
    route::get('Kepatuhan/Peraturan/Approved', 'KepatuhanController@setujudata')->name('kepatuhan.setujudata');
    route::get('Kepatuhan/Peraturan/Ignored', 'KepatuhanController@tolakdata')->name('kepatuhan.tolakdata');
    route::get('Kepatuhan/Peraturan/Log', 'KepatuhanController@loguser')->name('kepatuhan.loguser');
    route::get('Kepatuhan/Cuti/CutiWajib', 'KepatuhanController@cutiwajib')->name('kepatuhan.cutiwajib');
    route::get('Kepatuhan/Cuti/CutiLainnya', 'KepatuhanController@cutilainnya')->name('kepatuhan.cutilainnya');
    route::resource('Kepatuhan', 'KepatuhanController');

    route::get('Direksi/Profile', 'DireksiController@profile')->name('direksi.profile');
    route::get('Direksi/Pegawai/Index', 'DireksiController@indexpegawai')->name('direksi.indexpegawai');
    route::get('Direksi/Pegawai/Detail/{id}', 'DireksiController@detailpegawai')->name('direksi.detailpegawai');
    route::get('Direksi/Cuti/Index', 'DireksiController@cutidireksi')->name('direksi.cutidireksi');
    route::get('Direksi/Cuti/Permohonan', 'DireksiController@permohonancuti')->name('direksi.permohonancuti');
    route::post('Direksi/Cuti/Input', 'DireksiController@mintacuti')->name('direksi.mintacuti');
    route::get('Direksi/Cuti/Setuju', 'DireksiController@setujucuti')->name('direksi.setujucuti');
    route::get('Direksi/Cuti/Tolak', 'DireksiController@tolakcuti')->name('direksi.tolakcuti');
    route::get('Direksi/Cuti/Pegawai/Index', 'DireksiController@cutiindex')->name('direksi.cutiindex');
    route::get('Direksi/Cuti/Pegawai/Setuju', 'DireksiController@cutisetuju')->name('direksi.cutisetuju');
    route::get('Direksi/Cuti/Pegawai/Tolak', 'DireksiController@cutitolak')->name('direksi.cutitolak');
    route::post('Direksi/Cuti/Pegawai/Approve/{id}', 'DireksiController@setuju')->name('direksi.setuju');
    route::post('Direksi/Cuti/Pegawai/Ignore/{id}', 'DireksiController@tolak')->name('direksi.tolak');
    route::get('Direksi/Rotasi', 'DireksiController@rotasipegawai')->name('direksi.pegawairotasi');
    route::get('Direksi/Rotasi/Permohonan/{id}', 'DireksiController@mintarotasi')->name('direksi.permohonanrotasi');
    route::post('Direksi/Rotasi/Input', 'DireksiController@inputrotasi')->name('direksi.inputrotasi');
    route::get('Direksi/Rotasi/Index', 'DireksiController@datarotasi')->name('direksi.datarotasi');
    route::get('Direksi/Rotasi/Setuju', 'DireksiController@setujurotasi')->name('direksi.setujurotasi');
    route::get('Direksi/Rotasi/Tolak', 'DireksiController@tolakrotasi')->name('direksi.tolakrotasi');
    route::post('Direksi/Rotasi/Approve/{id}', 'DireksiController@rotasisetuju')->name('direksi.rotasisetuju');
    route::post('Direksi/Rotasi/Ignore/{id}', 'DireksiController@rotasitolak')->name('direksi.rotasitolak');
    route::get('Direksi/Peraturan', 'DireksiController@peraturan')->name('direksi.peraturan');
    route::get('Direksi/Peraturan/{id}', 'DireksiController@permohonandownload')->name('direksi.permohonandownload');
    route::post('Direksi/Peraturan/Download', 'DireksiController@mintadownload')->name('direksi.mintadownload');
    route::get('Direksi/status', 'DireksiController@statusatur')->name('direksi.status');
    route::get('Direksi/Mutasi/Pangkat', 'DireksiController@mutasipangkat')->name('direksi.mutasipangkat');
    route::get('Direksi/Mutasi/Pangkat/Approved', 'DireksiController@pangkatsetuju')->name('direksi.pangkatsetuju');
    route::get('Direksi/Mutasi/Pangkat/Ignored', 'DireksiController@pangkattolak')->name('direksi.pangkattolak');
    route::post('Direksi/Mutasi/Pangkat/Setuju/{id}', 'DireksiController@setujupangkat')->name('direksi.setujupangkat');
    route::post('Direksi/Mutasi/Pangkat/Tolak/{id}', 'DireksiController@tolakpangkat')->name('direksi.tolakpangkat');
    route::get('Direksi/Peraturan/Show/{id}', 'DireksiController@showatur')->name('direksi.showatur');
    route::get('Direksi/Peraturan/Print/{id}', 'DireksiController@show_pdf')->name('direksi.show_pdf');
    route::resource('Direksi', 'DireksiController');

    route::get('Dirbis/Profile', 'DirbisController@profile')->name('dirbis.profile');
    route::get('Dirbis/Pegawai/Index', 'DirbisController@indexpegawai')->name('dirbis.indexpegawai');
    route::get('Dirbis/Pegawai/Detail/{id}', 'DirbisController@detailpegawai')->name('dirbis.detailpegawai');
    route::get('Dirbis/Cuti/Index', 'DirbisController@cutidirbis')->name('dirbis.cutidirbis');
    route::get('Dirbis/Cuti/Permohonan', 'DirbisController@permohonancuti')->name('dirbis.permohonancuti');
    route::post('Dirbis/Cuti/Input', 'DirbisController@mintacuti')->name('dirbis.mintacuti');
    route::get('Dirbis/Cuti/Setuju', 'DirbisController@setujucuti')->name('dirbis.setujucuti');
    route::get('Dirbis/Cuti/Tolak', 'DirbisController@tolakcuti')->name('dirbis.tolakcuti');
    route::get('Dirbis/Cuti/Pegawai/Index', 'DirbisController@cutiindex')->name('dirbis.cutiindex');
    route::get('Dirbis/Cuti/Pegawai/Setuju', 'DirbisController@cutisetuju')->name('dirbis.cutisetuju');
    route::get('Dirbis/Cuti/Pegawai/Tolak', 'DirbisController@cutitolak')->name('dirbis.cutitolak');
    route::post('Dirbis/Cuti/Pegawai/Approve/{id}', 'DirbisController@setuju')->name('dirbis.setuju');
    route::post('Dirbis/Cuti/Pegawai/Ignore/{id}', 'DirbisController@tolak')->name('dirbis.tolak');
    route::get('Dirbis/Rotasi', 'DirbisController@rotasipegawai')->name('dirbis.pegawairotasi');
    route::get('Dirbis/Rotasi/Permohonan/{id}', 'DirbisController@mintarotasi')->name('dirbis.permohonanrotasi');
    route::post('Dirbis/Rotasi/Input', 'DirbisController@inputrotasi')->name('dirbis.inputrotasi');
    route::get('Dirbis/Rotasi/Index', 'DirbisController@datarotasi')->name('dirbis.datarotasi');
    route::get('Dirbis/Rotasi/Setuju', 'DirbisController@setujurotasi')->name('dirbis.setujurotasi');
    route::get('Dirbis/Rotasi/Tolak', 'DirbisController@tolakrotasi')->name('dirbis.tolakrotasi');
    route::post('Dirbis/Rotasi/Approve/{id}', 'DirbisController@rotasisetuju')->name('dirbis.rotasisetuju');
    route::post('Dirbis/Rotasi/Ignore/{id}', 'DirbisController@rotasitolak')->name('dirbis.rotasitolak');
    route::get('Dirbis/Peraturan', 'DirbisController@peraturan')->name('dirbis.peraturan');
    route::get('Dirbis/Peraturan/{id}', 'DirbisController@permohonandownload')->name('dirbis.permohonandownload');
    route::post('Dirbis/Peraturan/Download', 'DirbisController@mintadownload')->name('dirbis.mintadownload');
    route::get('Dirbis/status', 'DirbisController@statusatur')->name('dirbis.status');
    route::get('Dirbis/Mutasi/Pangkat', 'DirbisController@mutasipangkat')->name('dirbis.mutasipangkat');
    route::get('Dirbis/Mutasi/Pangkat/Approved', 'DirbisController@pangkatsetuju')->name('dirbis.pangkatsetuju');
    route::get('Dirbis/Mutasi/Pangkat/Ignored', 'DirbisController@pangkattolak')->name('dirbis.pangkattolak');
    route::post('Dirbis/Mutasi/Pangkat/Setuju/{id}', 'DirbisController@setujupangkat')->name('dirbis.setujupangkat');
    route::post('Dirbis/Mutasi/Pangkat/Tolak/{id}', 'DirbisController@tolakpangkat')->name('dirbis.tolakpangkat');
    route::get('Dirbis/Peraturan', 'DirbisController@peraturan')->name('dirbis.peraturan');
    route::get('Dirbis/Peraturan/Show/{id}', 'DirbisController@showatur')->name('dirbis.showatur');
    route::get('Dirbis/Peraturan/Print/{id}', 'DirbisController@show_pdf')->name('dirbis.show_pdf');

    route::resource('Dirbis', 'DirbisController');

    route::get('/datalog', 'LogController@datalog')->name('Loguser.datalog');
    route::resource('Loguser', 'LogController');

    //Route::group(['middleware' => ['cors']], function (){
    Route::get('/ajax/pangkat/search', 'PangkatController@ajaxsearch')->name('pangkat.ajaxsearch');
    Route::get('/ajax/pegawai/search', 'PegawaiController@ajaxsearch')->name('pegawai.ajaxsearch');
    Route::get('/ajax/jabatan/search', 'JabatanController@ajaxsearch')->name('jabatan.ajaxsearch');
    Route::get('/ajax/cabang/search', 'KantorController@ajaxsearch')->name('kantor.ajaxsearch');
    Route::get('/cat', 'CategoryController@cat')->name('categories.cat');
    Route::get('/SubCat/{id}', 'peraturanController@subcat');

    Route::Get('resetpassword/{id}/edit', 'ResetController@edit')->name('resetpassword.edit');
    route::post('resetpassword/{id}', 'ResetController@update')->name('resetpassword.update');
    route::resource('resetpassword', 'ResetController');

    route::get('riwayatangkat/{id}', 'riwayatangkatController@tambah')->name('riwayatangkat.tambah');
    route::get('riwayatangkat/{id}/list', 'riwayatangkatController@list')->name('riwayatangkat.list');
    route::post('riwayatangkat/{id}', 'riwayatangkatController@update')->name('riwayatangkat.update');
    route::delete('/riwayatangkat/{riwayatangkat}/delete-permanent', 'riwayatangkatController@deletePermanent')->name('riwayatangkat.delete-permanent');
    Route::resource('riwayatangkat', 'riwayatangkatController');
    route::get('riwayatsanksi/{id}', 'riwayatsanksiController@tambah')->name('riwayatsanksi.tambah');
    route::get('riwayatsanksi/{id}/list', 'riwayatsanksiController@list')->name('riwayatsanksi.list');
    route::post('riwayatsanksi/simpan', 'riwayatsanksiController@simpan')->name('riwayatsanksi.simpan');
    route::delete('/riwayatsanksi/{riwayatsanksi}/delete-permanent', 'riwayatsanksiController@deletePermanent')->name('riwayatsanksi.delete-permanent');
    route::resource('riwayatsanksi', 'riwayatsanksiController');

    route::get('gaji/{id}', 'gajiController@tambah')->name('gaji.tambah');
    route::get('gaji/{id}/list', 'gajiController@list')->name('gaji.list');
    route::delete('gaji/{gaji}/delete-permanent', 'gajiController@deletePermanent')->name('gaji.delete-permanent');
    route::resource('gaji', 'gajiController');

    route::get('pangkat/{id}/list', 'BerkalaController@list')->name('berkala.list');
    route::get('berkala/{id}/tambah', 'BerkalaController@tambah')->name('berkala.tambah');
    route::resource('berkala', 'BerkalaController');

    route::resource('penghasilan', 'PenghasilanController');
});

