<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Pangkat;
use App\Cabang;
use App\Pegawai;
use App\berkala;
use PDF;
use Illuminate\Support\Facades\Gate;
class PegawaiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::allows('ADMIN') || Gate::allows('ADMIN_SDM')) {
                return $next($request);
            }

            abort(403, 'Anda tidak memiliki hak akses');
        });
    }

    public function index(Request $request)
    {
        $filterkeyword = $request->get('keyword');
        $query = \App\Pegawai::with('jabatan', 'cabang')->where('status_active', 1);

        // filter keyword jika ada
        if ($filterkeyword) {
            $query->where('name', 'LIKE', "%$filterkeyword%");
        }

        // paginate
        $datapegawai = $query->paginate(10);

        $data = [];
        $now = \Carbon\Carbon::now()->format('Y-m-d');

        foreach ($datapegawai as $x) {
            $b_day = \Carbon\Carbon::parse($x['tgllahir']);
            $umur = $b_day->diffInYears($now);

            $masuk = \Carbon\Carbon::parse($x['tglmasuk']);
            $mkerja = $masuk->diffInYears($now);

            $peg = \App\Jabatan::find($x['jabatan']);
            $cab = \App\Cabang::find($x['cabang']);
            $pang = \App\Pangkat::find($x['pangkat']);
            $statspeg = \App\statuspeg::find($x['spegawai']);

            $data[] = [
                'id' => $x['id'],
                'name' => $x['name'],
                'umur' => $umur,
                'mkerja' => $mkerja,
                'photo' => $x['photo'],
                'nikpegawai' => $x['nikpegawai'],
                'status' => $statspeg['name'] ?? '-',
                'pangkat' => $pang['name'] ?? '-',
                'jabatan' => $peg['name'] ?? '-',
                'cabang' => $cab['name'] ?? '-',
                'status_active' => $x['status_active'] ?? 0,
            ];
        }

        return view('pegawai.index', [
            'pegawai' => $data,
            'datapegawai' => $datapegawai,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenkel = \App\Jenkel::pluck('name', 'id');
        $agama = \App\Agama::pluck('name', 'id');
        $nikah = \App\Kawin::pluck('name', 'id');
        $pendidikan = \App\Pendidikan::pluck('name', 'id');
        $jabatan = \App\Jabatan::pluck('name', 'id');
        $pangkat = \App\Pangkat::pluck('name', 'id')->toArray();
        $cabang = \App\Cabang::pluck('name', 'id');
        $statuspeg = \App\statuspeg::pluck('name', 'id');
        return view('pegawai.create', ['jenkel' => $jenkel, 'agama' => $agama, 'nikah' => $nikah, 'pendidikan' => $pendidikan, 'jabatan' => $jabatan, 'cabang' => $cabang, 'statuspeg' => $statuspeg]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            //   "name"=>"required|min:5|max:100",
            //"nikpegawai"=>"required|min:5|unique:pegawais",
            //"nikpenduduk"=>"required|max:16|unique:pegawais",
            // "templahir"=>"required",
            // "tgllahir"=>"required",
            //  "jenkel"=>"required",
            // "alamat"=>"required",
            // "agama"=>"required",
            //  "status"=>"required",
            //  "pendidikan"=>"required",
            //  "tglmasuk"=>"required",
            //  "pendidikan"=>"required",
            //  "tglmasuk"=>"required",
            //  "cabang"=>"required",
            //  "jabatan"=>"required",
            //  "pangkat"=>"required",
            // "email"=>"required|unique:pegawais",
            // "photo"=>"required"
        ])->validate();
        $jab = $request->get('jabatan');
        $jabatan = \App\Jabatan::where('id', $jab)->first();
        $atasan = $jabatan->atasan;
        $jabatasan2 = \App\Jabatan::where('id', $atasan)->first();
        $atasan2 = $jabatasan2->atasan;
        $new_pegawai = new \App\Pegawai();
        $new_user = new \App\User();

        $new_pegawai->name = $request->get('name');
        $new_pegawai->nikpegawai = $request->get('nikpegawai');
        $new_pegawai->nikpenduduk = $request->get('nikpenduduk');
        $new_pegawai->templahir = $request->get('templahir');
        $new_pegawai->tgllahir = $request->get('tgllahir');
        $new_pegawai->kelamin = $request->get('jenkel');
        $new_pegawai->alamat = $request->get('alamat');
        $new_pegawai->agama = $request->get('agama');
        $new_pegawai->goldar = $request->get('goldar');
        $new_pegawai->status = $request->get('status');
        $new_pegawai->pendidikan = $request->get('pendidikan');
        $new_pegawai->tglmasuk = $request->get('tglmasuk');
        $new_pegawai->spegawai = $request->get('spegawai');
        $new_pegawai->cabang = $request->get('cabang');
        $new_pegawai->jabatan = $jab;
        $new_pegawai->atasan1 = $atasan;
        $new_pegawai->atasan2 = $atasan2;
        $new_pegawai->pangkat = $request->get('pangkat');
        $new_pegawai->email = $request->get('email');
        $new_pegawai->mkpang = $request->get('mkpang');
        $new_pegawai->scuti = '12';
        $new_pegawai->tglangkat = $request->get('tmt');
        $new_pegawai->tuncab = $request->get('tuncab');
        $new_user->name = $request->get('name');
        $new_user->username = $request->get('name');
        $new_user->nikpegawai = $request->get('nikpegawai');
        $new_user->address = $request->get('alamat');
        $new_user->email = $request->get('email');
        $new_user->cabang = $request->get('cabang');
        $new_user->status = 'INACTIVE';

        $photo = $request->file('photo');
        if ($photo) {
            $photo_profile = $photo->store('pegawai-photo', 'public');
            $photo_user = $photo->store('avatars', 'public');
            $new_pegawai->photo = $photo_profile;
            $new_user->avatar = $photo_user;
        }
        $new_pegawai->created_by = \Auth::user()->id;
        $new_user->created_by = \Auth::user()->id;
        $new_pegawai->save();
        $new_user->save();

        return redirect()->route('pegawai.index')->with('status', 'Pegawai Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pegawai = \App\Pegawai::findOrFail($id);
        $cabang = \App\Cabang::where('id', $pegawai['cabang'])->first();
        $kelamin = \App\Jenkel::where('id', $pegawai['kelamin'])->first();
        $jabatan = \App\Jabatan::where('id', $pegawai['jabatan'])->first();
        $agama = \App\Agama::where('id', $pegawai['agama'])->first();
        $kawin = \App\Kawin::where('id', $pegawai['status'])->first();
        $pendidikan = \App\Pendidikan::where('id', $pegawai['pendidikan'])->first();
        $pangkat = \App\Pangkat::where('id', $pegawai['pangkat'])->first();
        $cabang = \App\Cabang::where('id', $pegawai['cabang'])->first();
        $tunkin = \App\Cabang::where('id', $pegawai['tuncab'])->first();
        $spegawai = \App\statuspeg::where('id', $pegawai['spegawai'])->first();
        $statuspegawai = \App\statuspeg::pluck('name', 'id');
        $statpegawai = $pegawai['spegawai'];
        $gaji = \App\gaji::where('idpeg', $pegawai['id'])->first();
        $keluarga = \App\keluarga::where('pegawai_id', [$pegawai['id']])->get();
        $anak = \App\keluarga::where('pegawai_id', $pegawai['id'])->where('hubungan', 'Anak')->get();
        $jumlahanak = count($anak);

        $nikah = \App\Keluarga::where([['pegawai_id', $pegawai['id']], ['hubungan', 'Istri']])
            ->orwhere([['pegawai_id', $pegawai['id']], ['hubungan', 'Suami']])
            ->get();
        $jumlahnikah = count($nikah);
        $datakel = [];
        $now = \Carbon\Carbon::now()->format('Y-m-d');
        $b_day = \Carbon\Carbon::parse($pegawai['tgllahir']);
        $umur = $b_day->diff($now)->format('%y Tahun %m Bulan');

        $masa = \Carbon\carbon::parse($pegawai['tglmasuk']);
        $mkerja = $masa->diff($now)->format('%y Tahun %m Bulan');

        $ppensiun = $b_day->addYears(56)->format('Y-m-d');
        $hpensiun = \Carbon\carbon::parse($ppensiun);

        $smkerja = $hpensiun->diff($now)->format('%y Tahun %m Bulan');
        //$smkeria=$pensiun-$mkerja;
        foreach ($keluarga as $k) {
            $bday_kel = \Carbon\Carbon::parse($k['tgllahir']);
            $umurkel = $bday_kel->diffinYears($now);

            $datakel[] = [
                'id' => $k['id'],
                'name' => $k['name'],
                'hub' => $k['hubungan'],
                'templahir' => $k['templahir'],
                'tgllahir' => $k['tgllahir'],
                'umurkel' => $umurkel,
                'alamat' => $k['alamat'],
            ];
        }

        $riwayatpendi = \App\riwayatpendi::where('pegawai_id', $pegawai['id'])->get();
        $datapend = [];
        foreach ($riwayatpendi as $pend) {
            $datapend[] = [
                'id' => $pend['id'],
                'name' => $pend['name'],
                'jurusan' => $pend['jurusan'],
                'pendidikan' => $pend['pendidikan'],
                'gelar' => $pend['gelar'],
                'thnlulus' => $pend['thnlulus'],
            ];
            //# code...
        }
        $riwayatkerja = \App\riwayatkerja::where('pegawai_id', $pegawai['id'])->get();
        $datakerja = [];
        foreach ($riwayatkerja as $kerja) {
            $awal = \Carbon\Carbon::parse($kerja['tglawal']);
            $akhir = \Carbon\Carbon::parse($kerja['tglakhir']);
            $periode = $awal->diff($akhir)->format('%y Tahun %m Bulan');
            $datakerja[] = [
                'id' => $kerja['id'],
                'name' => $kerja['name'],
                'kantorcabang' => $kerja['kantorcabang'],
                'tglawal' => $kerja['tglawal'],
                'tglakhir' => $kerja['tglakhir'],
                'periode' => $periode,
            ];
        }
        $pangkatpeg = $pegawai['pangkat'];
        $mkpangpeg = $pegawai['mkpang'];
        $gapok = \App\berkala::where([['idpang', 'LIKE', $pangkatpeg], ['gol', 'LIKE', $mkpangpeg]])->first();

        $tunpang = $jabatan['tunpang'];
        if ($jumlahanak > 2) {
            $jmlkeluarga = $jumlahnikah + 2;
        } else {
            $jmlkeluarga = $jumlahnikah + $jumlahanak;
        }

        $tunis = $jabatan['tunis'];
        if ($statpegawai == 1) {
            $pangan = 0;
        } else {
            if ($jmlkeluarga > 3) {
                $pangan = $tunpang * 0;
            } elseif ($jmlkeluarga <= 3) {
                $pangan = $tunpang * ($jmlkeluarga + 1);
            }
        }

        $tunak = $jabatan['tunak'];
        $tunjab = $gaji['jabatan'];
        if ($statpegawai == 3) {
            $tuncab = $tunkin['tunjangan'];
        } else {
            $tuncab = 0;
        }

        $fungsi = $gaji['fungsi'];
        $gapokpeg = $gapok['gapok'];
        $bpjsks = $gaji['bpjsks'];
        $bpjstk = $gaji['bpjstk'];
        $pensiun = $jabatan['pensiun'];
        if ($statpegawai == 3) {
            $tunpen = $pensiun * $gapokpeg;
        } else {
            $tunpen = 0;
        }
        $pph = $gaji['pph'];
        if ($statpegawai == 1) {
            $tunjanganistri = 0;
        } else {
            $tunjanganistri = $tunis * $gapokpeg * $jumlahnikah;
        }

        if ($jumlahanak <= 2) {
            $tunjangananak = $tunak * $gapokpeg * $jumlahanak;
        } elseif ($jumlahanak > 2) {
            $tunjangananak = $tunak * $gapokpeg * 2;
        } elseif ($statpegawai != 3) {
            $tunjangananak = 0;
        }

        $tuncabang = $tuncab * $gapokpeg;
        $total = $gapokpeg + $tunjanganistri + $tunjangananak + $pangan + $tunjab + $tuncabang + $bpjstk + $bpjsks + $pph + $fungsi + $tunpen;

        $pelatihan = \App\pelatihan::where('pegawai_id', $pegawai['id'])->get();
        $datapelatihan = [];
        foreach ($pelatihan as $lat) {
            $datapelatihan[] = [
                'id' => $lat['id'],
                'name' => $lat['name'],
                'penyelenggara' => $lat['penyelenggara'],
                'thnlatih' => $lat['thnlatih'],
                'image' => $lat['image'],
            ];
        }

        $riwayatangkat = \App\riwayatangkat::where('pegawai_id', [$pegawai['id']])->paginate(10);
        $dataangkat = [];

        foreach ($riwayatangkat as $angkat) {
            $statuspeg = \App\statuspeg::where('id', [$angkat['status']])->first();
            $statpeg = $statuspeg['name'];

            $dataangkat[] = [
                'id' => $angkat['id'],
                'status' => $statpeg,
                'tglangkat' => $angkat['tglangkat'],
                'nosk' => $angkat['nosk'],
            ];
        }
        $sanksi = \App\sanksi::pluck('name', 'id');
        $riwayatsanksi = \App\riwayatsanksi::where('id_peg', [$pegawai['id']])->paginate(10);
        $datasanksi = [];

        foreach ($riwayatsanksi as $rsanksi) {
            $sanksipegawai = \App\sanksi::where('id', [$rsanksi['sanksi']])->first();
            $sankpeg = $sanksipegawai['name'];

            $datasanksi[] = [
                'id' => $rsanksi['id'],
                'sanksipeg' => $sankpeg,
                'tglsanksi' => $rsanksi['tglsanksi'],
                'nosanksi' => $rsanksi['nosanksi'],
                'ket' => $rsanksi['ket'],
            ];
        }

        $tglpangkat = $pegawai['tglpangkat'];
        $tglberkala = $pegawai['tglberkala'];
        $tunda = $pegawai['tunda'];
        $jdpangkat = \Carbon\carbon::parse($tglpangkat);
        $jdberkala = \Carbon\carbon::parse($tglberkala);

        $jdpang = $jdpangkat->addYears(4)->addmonths($tunda)->toDateString();
        $jdber = $jdberkala->addYears(2)->addmonths($tunda)->toDateString();

        return view('pegawai.show', [
            'pegawai' => $pegawai,
            'jmlkeluarga' => $jmlkeluarga,
            'cabang' => $cabang,
            'kelamin' => $kelamin,
            'jabatan' => $jabatan,
            'umur' => $umur,
            'agama' => $agama,
            'kawin' => $kawin,
            'pendidikan' => $pendidikan,
            'pangkat' => $pangkat,
            'keluarga' => $datakel,
            'masakerja' => $mkerja,
            'riwayatpendi' => $datapend,
            'riwayatkerja' => $datakerja,
            'tunjanganistri' => $tunjanganistri,
            'tunjangananak' => $tunjangananak,
            'tuncabang' => $tuncabang,
            'total' => $total,
            'pelatihan' => $pelatihan,
            'ppensiun' => $ppensiun,
            'smkerja' => $smkerja,
            'spegawai' => $spegawai,
            'dataangkat' => $dataangkat,
            'datasanksi' => $datasanksi,
            'bpjstk' => $bpjstk,
            'bpjsks' => $bpjsks,
            'pensiun' => $pensiun,
            'pph' => $pph,
            'fungsi' => $fungsi,
            'gapokpeg' => $gapokpeg,
            'gapok' => $gapok,
            'tglpangkat' => $tglpangkat,
            'tglberkala' => $tglberkala,
            'tunda' => $tunda,
            'jdpang' => $jdpang,
            'jdber' => $jdber,
            'jumlahanak' => $jumlahanak,
            'pangan' => $pangan,
            'tunpen' => $tunpen,
            'tunjab' => $tunjab,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pegawai = \App\Pegawai::findOrFail($id);
        $jenkel = \App\Jenkel::pluck('name', 'id');
        $agama = \App\Agama::pluck('name', 'id');
        $status = \App\Kawin::pluck('name', 'id');
        $pendidikan = \App\Pendidikan::pluck('name', 'id');
        $kantor = \App\Cabang::pluck('name', 'id');
        $jabatan = \App\Jabatan::pluck('name', 'id');
        $pangkat = \App\Pangkat::pluck('name', 'id')->toArray();
        $tetap = \App\statuspeg::pluck('name', 'id');

        $jenkelpegawai = \App\Jenkel::where('id', $pegawai['kelamin'])->first();
        $kel = $jenkelpegawai->name;

        $agamapegawai = \App\agama::where('id', $pegawai['agama'])->first();
        $ag = $agamapegawai->name;

        $statuskawin = \App\kawin::where('id', $pegawai['status'])->first();
        $stat = $statuskawin->name;

        $pendidikanpegawai = \App\pendidikan::where('id', $pegawai['pendidikan'])->first();
        $pend = $pendidikanpegawai->name;

        $kantorpegawai = \App\Cabang::where('id', $pegawai['cabang'])->first();
        $kant = $kantorpegawai->name;

        $jabatanpegawai = \App\Jabatan::where('id', $pegawai['jabatan'])->first();
        $jab = $jabatanpegawai->name;

        $statuspegawai = \App\statuspeg::where('id', $pegawai['spegawai'])->first();
        $spegawai = $statuspegawai->name;

        $pangkatpegawai = \App\Pangkat::where('id', $pegawai['pangkat'])->first();
        $pang = $pangkatpegawai->name;

        return view('pegawai.edit', ['pegawai' => $pegawai, 'jenkel' => $jenkel, 'pang' => $pang, 'jab' => $jab, 'agama' => $agama, 'status' => $status, 'pendidikan' => $pendidikan, 'kantor' => $kantor, 'jabatan' => $jabatan, 'pangkat' => $pangkat, 'kel' => $kel, 'ag' => $ag, 'stat' => $stat, 'pend' => $pend, 'kant' => $kant, 'tetap' => $tetap, 'spegawai' => $spegawai]);
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
        $jab = $request->get('jabatan');
        $jabatan = \App\Jabatan::where('id', $jab)->first();
        $atasan = $jabatan->atasan;
        $jabatasan2 = \App\Jabatan::where('id', $atasan)->first();
        $atasan2 = $jabatasan2->atasan;

        $pegawai = \App\Pegawai::findOrFail($id);
        $iduser = \App\user::where('pegawai_id', $pegawai['id'])->get();
        //$userid = $iduser->user_id;

        //$user = \App\User::findOrFail($userid);

        $pegawai->name = $request->get('name');
        $iduser->name = $request->get('name');
        $iduser->username = $request->get('name');
        $pegawai->nikpegawai = $request->get('nikpegawai');
        $iduser->nikpegawai = $request->get('nikpegawai');
        $pegawai->email = $request->get('email');
        $iduser->email = $request->get('email');
        $pegawai->nikpenduduk = $request->get('nikpenduduk');
        $pegawai->templahir = $request->get('templahir');
        $pegawai->tgllahir = $request->get('tgllahir');
        $pegawai->kelamin = $request->get('jenkel');
        $pegawai->alamat = $request->get('alamat');
        $pegawai->agama = $request->get('agama');
        $pegawai->status = $request->get('status');
        $pegawai->pendidikan = $request->get('pendidikan');
        $pegawai->tglmasuk = $request->get('tglmasuk');
        $pegawai->spegawai = $request->get('spegawai');
        $pegawai->cabang = $request->get('kantor');
        $iduser->cabang = $request->get('kantor');
        $pegawai->jabatan = $request->get('jabatan');
        $pegawai->pangkat = $request->get('pangkat');
        $pegawai->mkpang = $request->get('mkpang');
        $pegawai->goldar = $request->get('goldar');
        $pegawai->npwp = $request->get('npwp');
        $pegawai->nohp = $request->get('nohp');
        $pegawai->tuncab = $request->get('tuncab');
        $pegawai->tglangkat = $request->get('tmt');
        $pegawai->atasan1 = $atasan;
        $pegawai->atasan2 = $atasan2;

        $newphoto = $request->file('photo');
        $newavatar = $request->file('photo');

        if ($newphoto) {
            if ($pegawai->photo && file_exists(storage_path('app/public/' . $pegawai->photo))) {
                \Storage::delete('public/' . $pegawai->photo);
            }
            $new_photo_path = $newphoto->store('pegawai-photo', 'public');
            $pegawai->photo = $new_photo_path;
        }
        //if($newavatar){
        //if($iduser->avatar && file_exists(storage_path("app/public/".$iduser->avatar))){
        //     \Storage::delete("public/".$iduser->avatar);
        //     $new_avatar_path = $newphoto->store("avatars","public");
        //$iduser->avatar = $new_avatar_path;}
        //}

        //$pegawai->updated_by = \Auth::user()->id;
        $pegawai->save();
        //$pegawai->pangkat()->attach($request->get("pangkat"));
        // $user->updated_by = \Auth::user()->id;
        //$iduser->save();

        return redirect()->route('pegawai.index')->with('status', 'Data Pegawai Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pegawai = \App\Pegawai::findOrFail($id);
        $pegawai->delete();
        return redirect()->route('pegawai.index')->with('status', 'Data Pegawai moved to trash');
    }

    public function trash(Request $request)
    {
        $query = Pegawai::withTrashed()->where(function ($q) {
            $q->whereNotNull('deleted_at')->orWhere('status_active', 0);
        });

        if ($request->keyword) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }
        $pegawaiPaginate = $query
            ->orderByRaw('deleted_at IS NULL')
            ->orderByDesc('updated_at') 
            ->paginate(10);

        $pegawai = [];

        foreach ($pegawaiPaginate as $x) {
            $pegawai[] = [
                'id' => $x->id,
                'name' => $x->name,
                'nikpegawai' => $x->nikpegawai,
                'status_active' => $x->status_active,
                'status' => $x->spegawai,
                'mkerja' => \Carbon\Carbon::parse($x->tglmasuk)->diffInYears(now()),
                'jabatan' => optional(\App\Jabatan::find($x->jabatan))->name ?? '-',
                'cabang' => optional(\App\Cabang::find($x->cabang))->name ?? '-',
            ];
        }

        return view('pegawai.trash', [
            'pegawai' => $pegawai,
            'deletedpegawai' => $pegawaiPaginate,
        ]);
    }

    public function deletePermanent($id)
    {
        $pegawai = \App\Pegawai::withTrashed()->findOrFail($id);
        if (!$pegawai->trashed()) {
            return redirect()->route('pegawai.trash')->with('status', 'Data Pegawai is not in trash') - with('status_type', 'alert');
        } else {
            //$pegawai->pangkat()->detach();
            $pegawai->forceDelete();
            return redirect()->route('pegawai.trash')->with('status', 'Data Pegawai Permanently Deleted!');
        }
    }

    public function toggleRestore($id)
    {
        $pegawai = \App\Pegawai::withTrashed()->findOrFail($id);

        if ($pegawai->trashed()) {
            $pegawai->restore();
            $msg = "{$pegawai->name} berhasil direstore.";
        } else {
            $pegawai->delete();
            $msg = "{$pegawai->name} berhasil dipindahkan ke trash.";
        }

        return redirect()->back()->with('status', $msg);
    }

    public function cetakpdf($id)
    {
        $pegawai = \App\Pegawai::findOrFail($id);
        $cabang = \App\cabang::where('id', $pegawai['cabang'])->first();
        $kelamin = \App\jenkel::where('id', $pegawai['kelamin'])->first();
        $jabatan = \App\jabatan::where('id', $pegawai['jabatan'])->first();
        $agama = \App\Agama::where('id', $pegawai['agama'])->first();
        $kawin = \App\Kawin::where('id', $pegawai['status'])->first();
        $pendidikan = \App\pendidikan::where('id', $pegawai['pendidikan'])->first();
        $pangkat = \App\pangkat::where('id', $pegawai['pangkat'])->first();
        $cabang = \App\cabang::where('id', $pegawai['cabang'])->first();
        $spegawai = \App\statuspeg::where('id', $pegawai['spegawai'])->first();
        $keluarga = \App\keluarga::where('pegawai_id', [$pegawai['id']])->get();
        $datakel = [];
        $now = \Carbon\Carbon::now()->format('Y-m-d');
        $b_day = \Carbon\Carbon::parse($pegawai['tgllahir']);
        $umur = $b_day->diffinYears($now);

        $masa = \Carbon\carbon::parse($pegawai['tglmasuk']);
        $mkerja = $masa->diffinYears($now);

        $pensiun = $b_day->addYears(56)->format('Y-m-d');
        $hpensiun = \Carbon\carbon::parse($pensiun);

        $smkerja = $hpensiun->diff($now)->format('%y Tahun %m Bulan');
        foreach ($keluarga as $k) {
            $bday_kel = \Carbon\Carbon::parse($k['tgllahir']);
            $umurkel = $bday_kel->diffinYears($now);

            $datakel = [
                'id' => $k['id'],
                'nama' => $k['name'],
                'hub' => $k['hub'],
                'templahir' => $k['templahir'],
                'tgllahir' => $k['tgllahir'],
                'umurkel' => $umurkel,
                'alamat' => $k['alamat'],
            ];
        }

        $riwayatpendi = \App\riwayatpendi::where('pegawai_id', $pegawai['id'])->get();
        $datapend = [];
        foreach ($riwayatpendi as $pend) {
            $datapend = [
                'id' => $pend['id'],
                'name' => $pend['name'],
                'jurusan' => $pend['jurusan'],
                'pendidikan' => $pend['pendidikan'],
                'gelar' => $pend['gelar'],
                'thnlulus' => $pend['thnlulus'],
            ];
            //# code...
        }
        $riwayatkerja = \App\riwayatkerja::where('pegawai_id', $pegawai['id'])->get();
        $datakerja = [];
        foreach ($riwayatkerja as $kerja) {
            $datakerja = [
                'id' => $kerja['id'],
                'name' => $kerja['name'],
                'kantorcabang' => $kerja['kantorcabang'],
                'thnangkat' => $kerja['thnangkat'],
            ];
        }

        $tunis = $jabatan['tunis'];
        $tunak = $jabatan['tunak'];
        $tunjab = $jabatan['tunjab'];
        $tunpang = $jabatan['tunpang'];
        $tuncab = $cabang['tunjangan'];
        $gapok = $pangkat['gapok'];
        $tunjanganistri = $tunis * $gapok;
        $tunjangananak = $tunak * $gapok;
        $tuncabang = $tuncab * $gapok;
        $total = $gapok + $tunjanganistri + $tunjangananak + $tunpang + $tunjab + $tuncabang;

        $pelatihan = \App\pelatihan::where('pegawai_id', $pegawai['id'])->get();
        $datapelatihan = [];
        foreach ($pelatihan as $lat) {
            $datapelatihan = [
                'id' => $lat['id'],
                'name' => $lat['name'],
                'penyelenggara' => $lat['penyelenggara'],
                'thnlatih' => $lat['thnlatih'],
                'image' => $lat['image'],
            ];
        }

        return view('pegawai.cetak', ['pegawai' => $pegawai, 'cabang' => $cabang, 'kelamin' => $kelamin, 'jabatan' => $jabatan, 'umur' => $umur, 'agama' => $agama, 'kawin' => $kawin, 'pendidikan' => $pendidikan, 'pangkat' => $pangkat, 'cabang' => $cabang, 'keluarga' => $keluarga, 'umurkel' => $umurkel, 'masakerja' => $mkerja, 'riwayatpendi' => $riwayatpendi, 'riwayatkerja' => $riwayatkerja, 'tunjanganistri' => $tunjanganistri, 'tunjangananak' => $tunjangananak, 'tuncabang' => $tuncabang, 'total' => $total, 'pelatihan' => $pelatihan, 'smkerja' => $smkerja, 'spegawai' => $spegawai]);
        //return $pdf->stream();
    }
    public function ajaxsearch(Request $request)
    {
        $keyword = $request->get('q');

        $pegawai = \App\Pegawai::where('name', 'LIKE', "%$keyword%")->get();

        return $pegawai;
    }
    public function simpan(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'name' => 'required|min:5|max:100',
        ])->validate();
        //$jab = $request->get("jabatan");
        //$jabatan = \App\Jabatan::where("id",$jab)->first();
        //$atasan = $jabatan->atasan;

        $new_pegawai = new \App\Pegawai();
        $new_user = new \App\User();

        $new_pegawai->name = $request->get('name');
        $new_pegawai->nikpegawai = $request->get('nikpegawai');
        $new_pegawai->nikpenduduk = $request->get('nikpenduduk');
        $new_pegawai->templahir = $request->get('templahir');
        $new_pegawai->tgllahir = $request->get('tgllahir');
        $new_pegawai->kelamin = $request->get('jenkel');
        $new_pegawai->alamat = $request->get('alamat');
        $new_pegawai->agama = $request->get('agama');
        $new_pegawai->status = $request->get('status');
        $new_pegawai->pendidikan = $request->get('pendidikan');
        $new_pegawai->tglmasuk = $request->get('tglmasuk');
        $new_pegawai->spegawai = $request->get('spegawai');
        $new_pegawai->cabang = $request->get('cabang');
        $new_pegawai->jabatan = $request->get('jabatan');
        //$new_pegawai->atasan = $atasan;
        $new_pegawai->pangkat = $request->get('pangkat');
        $new_pegawai->email = $request->get('email');
        $new_pegawai->scuti = '12';
        $new_pegawai->npwp = $request->get('npwp');
        $new_pegawai->nohp = $request->get('nohp');
        $new_user->name = $request->get('name');
        $new_user->username = $request->get('name');
        $new_user->nikpegawai = $request->get('nikpegawai');
        $new_user->address = $request->get('alamat');
        $new_user->email = $request->get('email');
        $new_user->cabang = $request->get('cabang');
        $new_user->status = 'INACTIVE';

        $photo = $request->file('photo');
        if ($photo) {
            $photo_profile = $photo->store('pegawai-photo', 'public');
            $photo_user = $photo->store('avatars', 'public');
            $new_pegawai->photo = $photo_profile;
            $new_user->avatar = $photo_user;
        }
        $new_pegawai->created_by = \Auth::user()->id;
        $new_user->created_by = \Auth::user()->id;
        $new_pegawai->save();
        $new_user->save();

        return redirect()->route('pegawai.index')->with('status', 'Pegawai Berhasil Ditambahkan');
    }
    public function input()
    {
        $jenkel = \App\Jenkel::pluck('name', 'id');
        $agama = \App\Agama::pluck('name', 'id');
        $nikah = \App\Kawin::pluck('name', 'id');
        $pendidikan = \App\Pendidikan::pluck('name', 'id');
        $jabatan = \App\Jabatan::pluck('name', 'id');
        $pangkat = \App\Pangkat::pluck('name', 'id');
        $cabang = \App\Cabang::pluck('name', 'id');

        return view('pegawai.input', ['jenkel' => $jenkel, 'agama' => $agama, 'nikah' => $nikah, 'pendidikan' => $pendidikan, 'jabatan' => $jabatan, 'pangkat' => $pangkat, 'cabang' => $cabang]);
    }

    public function EditBerkala($id)
    {
        $pegawai = \App\Pegawai::findOrFail($id);

        return view('pegawai.berkala', ['pegawai' => $pegawai]);
    }

    public function UpdateBerkala(Request $request, $id)
    {
        $tglberkala = $request->get('tglberkala');
        $tglpangkat = $request->get('tglpangkat');
        $tunda = $request->get('tunda');
        $pegawai = \App\Pegawai::findorfail($id);

        $pegawai->tglberkala = $tglberkala;
        $pegawai->tglpangkat = $tglpangkat;
        $pegawai->tunda = $tunda;
        $pegawai->save();
        return redirect()->route('pegawai.editberkala', $pegawai)->with('status', 'Pegawai Berhasil Ditambahkan');
    }
    public function jadwalberkala()
    {
        return view('pegawai.jdberkala');
    }

    public function data()
    {
        $pegawai = \App\Pegawai::all();

        $databerkala = [];

        foreach ($pegawai as $x) {
            $tglpangkat = $x['tglpangkat'];
            $tglberkala = $x['tglberkala'];
            $tunda = $x['tunda'];

            $jdpangkat = \Carbon\carbon::parse($tglpangkat);
            $jdberkala = \Carbon\carbon::parse($tglberkala);
            $jdpang = $jdpangkat->addYears(4)->addmonths($tunda)->format('d-m-Y');
            $jdber = $jdberkala->addYears(2)->addmonths($tunda)->format('d-m-Y');
            $now = date('m-Y');
            $jdwlpangkat = $jdpangkat->format('m-Y');
            $jdwlberkala = $jdberkala->format('m-Y');

            $pangkat = \App\Pangkat::where('id', $x['pangkat'])->first();
            $npangkat = $pangkat['name'];
            $gol = $x['mkpang'];
            $cab = \App\Cabang::where('id', $x['cabang'])->first();
            $cabang = $cab['name'];
            //$jab = \App\Jabatan::where('id',$x['jabatan'])->first();
            //$jabatan = $jab['name'];

            if ($now == $jdwlberkala) {
                $row = [];
                $row['nama'] = $x['name'];
                $row['tunda'] = $tunda;
                $row['pangkat'] = $npangkat . '/' . $gol;
                $row['jdpang'] = $jdpang;
                $row['jdber'] = $jdber;
                $row['cabang'] = $cabang;
                //$row["jabatan"] = $jabatan;
                $row['now'] = $now;
                $databerkala[] = $row;
            }
        }
        return datatables()->of($databerkala)->addIndexColumn()->make(true);
    }

    public function berkalapangkat()
    {
        $pegawai = \App\Pegawai::all();

        $databerkala = [];

        foreach ($pegawai as $x) {
            $tglpangkat = $x['tglpangkat'];
            $tglberkala = $x['tglberkala'];
            $tunda = $x['tunda'];

            $jdpangkat = \Carbon\carbon::parse($tglpangkat);
            $jdberkala = \Carbon\carbon::parse($tglberkala);
            $jdpang = $jdpangkat->addYears(4)->addmonths($tunda)->format('Y-m-d');
            $jdber = $jdberkala->addYears(2)->addmonths($tunda)->format('Y-m-d');
            $now = date('m-Y');
            $jdwlpangkat = $jdpangkat->format('m-Y');
            $jdwlberkala = $jdberkala->format('m-Y');

            $pangkat = \App\Pangkat::where('id', $x['pangkat'])->first();
            $npangkat = $pangkat['name'];
            $gol = $x['mkpang'];
            $cab = \App\Cabang::where('id', $x['cabang'])->first();
            $cabang = $cab['name'];
            //$jab = \App\Jabatan::where('id',$x['jabatan'])->first();
            //$jabatan = $jab['name'];

            // if($now == $jdwlpangkat){
            $row = [];
            $row['nama'] = $x['name'];
            $row['tunda'] = $tunda;
            $row['pangkat'] = $npangkat . '/' . $gol;
            $row['jdpang'] = $jdpang;
            $row['jdber'] = $jdber;
            $row['cabang'] = $cabang;
            //$row["jabatan"] = $jabatan;
            $row['now'] = $now;
            $databerkala[] = $row;
            // }
        }
        return datatables()->of($databerkala)->addIndexColumn()->make(true);
    }
    public function listberkala()
    {
        return view('pegawai.kepangkatan');
    }
    public function listpangkat()
    {
        return view('pegawai.jdpangkat');
    }
    public function datapangkat()
    {
        $pegawai = \App\Pegawai::all();
        $databerkala = [];

        foreach ($pegawai as $x) {
            $tglpangkat = $x['tglpangkat'];
            $tglberkala = $x['tglberkala'];
            $tunda = $x['tunda'];
            $jdpangkat = \Carbon\carbon::parse($tglpangkat);
            $jdberkala = \Carbon\carbon::parse($tglberkala);
            $jdpang = $jdpangkat->addYears(4)->addmonths($tunda)->format('d-m-Y');
            $jdber = $jdberkala->addYears(2)->addmonths($tunda)->format('d-m-Y');
            $now = date('Y');
            $jdwlpangkat = $jdpangkat->format('Y');
            $jdwlberkala = $jdberkala->format('m-Y');

            $pangkat = \App\Pangkat::where('id', $x['pangkat'])->first();
            $npangkat = $pangkat['name'];
            $gol = $x['mkpang'];
            $cab = \App\Cabang::where('id', $x['cabang'])->first();
            $cabang = $cab['name'];
            //$jab = \App\Jabatan::where('id',$x['jabatan'])->first();
            //$jabatan = $jab['name'];

            if ($now == $jdwlpangkat) {
                $row = [];
                $row['nama'] = $x['name'];
                $row['tunda'] = $tunda;
                $row['pangkat'] = $npangkat . '/' . $gol;
                $row['jdpang'] = $jdpang;
                $row['jdber'] = $jdber;
                $row['cabang'] = $cabang;
                //$row["jabatan"] = $jabatan;
                $row['now'] = $now;
                $databerkala[] = $row;
            }
        }
        return datatables()->of($databerkala)->addIndexColumn()->make(true);
    }

    public function toggleActive($id)
    {
        $pegawai = \App\Pegawai::withTrashed()->findOrFail($id);

        $pegawai->status_active = !$pegawai->status_active;

        // kalau diaktifkan dari trash → restore
        if ($pegawai->status_active && $pegawai->trashed()) {
            $pegawai->restore();
        }

        $pegawai->save();

        $user = \App\User::where('pegawai_id', $id)->first();
        if ($user) {
            $user->status = $pegawai->status_active ? 'ACTIVE' : 'INACTIVE';
            $user->save();
        }

        $msg = $pegawai->status_active ? "{$pegawai->name} berhasil diaktifkan." : "{$pegawai->name} berhasil dinonaktifkan.";

        return back()->with('status', $msg);
    }
}
