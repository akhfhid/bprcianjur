<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Pegawai;
use Carbon\Carbon;
class KepatuhanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware(function($request, $next){
        if(gate::allows('PATUH')) return $next($request);
        abort(403,'Anda tidak memiliki hak akses');
        });
    }
    public function index()
    {
        //
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
        //
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
        //
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
        //
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
    public function indexpegawai(Request $request){
        $idcabang = \Auth::user()->cabang;
        $datpeg = \Auth::user()->pegawai_id;
        $pegdiv = \App\Pegawai::where('id',$datpeg)->first();
        $jabdiv = $pegdiv->jabatan;
        $jab = \App\Jabatan::where('atasan',$jabdiv)->first();
        $jabpeg = $jab->id;


        $filterkeyword = $request->get('keyword');

        if($filterkeyword){
            $datapegawai = \App\Pegawai::with('jabatan','cabang')->where("name","LIKE", "%$filterkeyword%")
                ->where([['cabang',$idcabang],
                    ['atasan2',$jabdiv]])
                ->orwhere([['cabang',$idcabang],
                    ['atasan1',$jabdiv]])
                ->paginate(10);
        } else {
            $datapegawai = \App\Pegawai::with('jabatan','cabang')
                ->where([['cabang',$idcabang],
                    ['atasan2',$jabdiv]])
                ->orwhere([['cabang',$idcabang],
                    ['atasan1',$jabdiv]])
                ->paginate(10);

        }


        $data = [];
        $now= \Carbon\Carbon::now()->format('Y-m-d');

        foreach ($datapegawai as $x) {
            $b_day = \Carbon\Carbon::parse($x['tgllahir']);
            $umur =$b_day->diffinYears($now);

            $masuk = \Carbon\Carbon::parse($x['tglmasuk']);
            $mkerja =$masuk->diffinYears($now);

            $peg = \App\Jabatan::where('id',$x['jabatan'])->first();
            $namajab = $peg['name'];

            $cab = \App\Cabang::where('id',$x['cabang'])->first();
            $namacab = $cab['name'];

            $pang = \App\Pangkat::where('id',$x['pangkat'])->first();
            $pangkat = $pang['name'];

            $statspeg = \App\statuspeg::where("id",$x["spegawai"])->first();
            $status= $statspeg["name"];

            $data[] = [
                "id"=>$x['id'],
                "name"=>$x['name'],
                "umur"=>$umur,
                "mkerja"=>$mkerja,
                "photo"=>$x['photo'],
                "nikpegawai"=>$x['nikpegawai'],
                "status"=>$status,
                "pangkat"=>$pangkat,
                "jabatan"=>$namajab,
                "cabang"=>$namacab,


            ];

        }

      return view ('kepatuhan.pegawai',['pegawai'=>$data]);
    }

    public function detailpegawai($id){

        $pegawai = \App\Pegawai::findorfail($id);
        $cabang = \App\Cabang::where("id",$pegawai["cabang"])->first();
        $kelamin = \App\Jenkel::where("id",$pegawai["kelamin"])->first();
        $jabatan = \App\Jabatan::where("id",$pegawai["jabatan"])->first();
        $agama = \App\Agama::where("id",$pegawai["agama"])->first();
        $kawin = \App\Kawin::where("id",$pegawai["status"])->first();
        $pendidikan = \App\Pendidikan::where("id",$pegawai["pendidikan"])->first();
        $pangkat = \App\Pangkat::where("id",$pegawai["pangkat"])->first();
        $cabang= \App\Cabang::where("id",$pegawai["cabang"])->first();
        $tunkin = \App\Cabang::where("id",$pegawai['tuncab'])->first();
        $spegawai = \App\statuspeg::where("id",$pegawai["spegawai"])->first();
        $statuspegawai = \App\statuspeg::pluck("name","id");
        $statpegawai = $pegawai['spegawai'];
        $gaji = \App\gaji::where('idpeg',$pegawai['id'])->first();
        $keluarga = \App\keluarga::where("pegawai_id",[$pegawai["id"]])->get();
        $anak = \App\keluarga::where('pegawai_id',$pegawai['id'])->where('hubungan','Anak')->get();
        $jumlahanak = count($anak);
        $nikah = \App\Keluarga::where([
            ['pegawai_id',$pegawai['id']],
            ['hubungan','Istri']
        ])->orwhere([['pegawai_id',$pegawai['id']],
            ['hubungan','Suami']])->get();
        $jumlahnikah = count($nikah);
        $datakel=[];
        $now= \Carbon\Carbon::now()->format("Y-m-d");
        $b_day = \Carbon\Carbon::parse($pegawai["tgllahir"]);
        $umur =$b_day->diff($now)->format('%y Tahun %m Bulan');

        $masa = \Carbon\carbon::parse($pegawai["tglmasuk"]);
        $mkerja = $masa->diff($now)->format('%y Tahun %m Bulan');

        $ppensiun = $b_day->addYears(56)->format('Y-m-d');
        $hpensiun = \Carbon\carbon::parse($ppensiun);
        $smkerja = $hpensiun->diff($now)->format('%y Tahun %m Bulan');
        //$smkeria=$pensiun-$mkerja;
        foreach ($keluarga as $k) {
            $bday_kel = \Carbon\Carbon::parse($k["tgllahir"]);
            $umurkel = $bday_kel->diffinYears($now);

            $datakel[]=[
                "id"=> $k["id"],
                "name"=>$k["name"],
                "hub"=>$k["hubungan"],
                "templahir"=>$k["templahir"],
                "tgllahir" => $k["tgllahir"],
                "umurkel"=>$umurkel,
                "alamat"=>$k["alamat"]
            ];

        }

        $riwayatpendi = \App\riwayatpendi::where("pegawai_id",$pegawai["id"])->get();
        $datapend=[];
        foreach ($riwayatpendi as $pend) {
            $datapend[]=[
                "id"=> $pend["id"],
                "name"=>$pend["name"],
                "jurusan"=>$pend["jurusan"],
                "pendidikan" =>$pend["pendidikan"],
                "gelar"=>$pend["gelar"],
                "thnlulus"=>$pend["thnlulus"]
            ];
            //# code...
        }
        $riwayatkerja = \App\riwayatkerja::where("pegawai_id",$pegawai["id"])->get();
        $datakerja=[];
        foreach ($riwayatkerja as $kerja) {
            $awal = \Carbon\Carbon::parse($kerja["tglawal"]);
            $akhir = \Carbon\Carbon::parse ($kerja["tglakhir"]);
            $periode = $awal->diff($akhir)->format('%y Tahun %m Bulan');
            $datakerja[]=[
                "id"=>$kerja["id"],
                "name"=>$kerja["name"],
                "kantorcabang"=>$kerja["kantorcabang"],
                "tglawal"=>$kerja["tglawal"],
                "tglakhir" => $kerja["tglakhir"],
                "periode"=>$periode,
            ];
        }
        $pangkatpeg = $pegawai['pangkat'];
        $mkpangpeg = $pegawai['mkpang'];
        $gapok = \App\berkala::where([['idpang',"LIKE",$pangkatpeg],
            ['gol',"LIKE",$mkpangpeg]])->first();

        $tunpang = $jabatan['tunpang'];
        $jmlkeluarga = $jumlahnikah+$jumlahanak;
        $tunis = $jabatan['tunis'];
        if ($statpegawai == 1){
            $pangan = 0;
        } else {
            if ($jmlkeluarga > 3) {
                $pangan = $tunpang * 4;
            } elseif ($jmlkeluarga <= 3){
                $pangan = $tunpang * ($jumlahnikah+$jumlahanak+1);}

        }

        $tunak = $jabatan["tunak"];
        $tunjab = $gaji["jabatan"];
        if ($statpegawai == 3){
            $tuncab = $tunkin["tunjangan"];}
        else{
            $tuncab = 0;
        }


        $fungsi = $gaji["fungsi"];
        $gapokpeg = $gapok["gapok"];
        $bpjsks = $gaji["bpjsks"];
        $bpjstk = $gaji["bpjstk"];
        $pensiun = $jabatan['pensiun'];
        if ($statpegawai ==3){
            $tunpen = $pensiun * $gapokpeg;}
        else{
            $tunpen = 0;
        }
        $pph = $gaji['pph'];
        if ($statpegawai == 1){
            $tunjanganistri = 0;
        }else{
            $tunjanganistri = $tunis * $gapokpeg *$jumlahnikah;

        }

        if ($jumlahanak <= 2) {
            $tunjangananak = $tunak *$gapokpeg*$jumlahanak;
        } elseif ($jumlahanak > 2) {
            $tunjangananak = $tunak *$gapokpeg*2;
        }elseif ($statpegawai !=3){
            $tunjangananak = 0;
        }


        $tuncabang = $tuncab*$gapokpeg;
        $total =$gapokpeg+$tunjanganistri+$tunjangananak+$pangan+$tunjab+$tuncabang+$bpjstk+$bpjsks+$pph+$fungsi+$tunpen;

        $pelatihan = \App\pelatihan::where("pegawai_id",$pegawai["id"])->get();
        $datapelatihan=[];
        foreach ($pelatihan as $lat) {
            $datapelatihan[]=[
                "id"=>$lat["id"],
                "name"=>$lat["name"],
                "penyelenggara"=>$lat["penyelenggara"],
                "thnlatih"=>$lat["thnlatih"],
                "image"=>$lat["image"]
            ];
        }

        $riwayatangkat = \App\riwayatangkat::where('pegawai_id',[$pegawai['id']])->paginate(10);
        $dataangkat=[];

        foreach ($riwayatangkat as $angkat) {
            $statuspeg = \App\statuspeg::where('id',[$angkat['status']])->first();
            $statpeg = $statuspeg['name'];

            $dataangkat[]=[
                "id"=>$angkat['id'],
                "status"=>$statpeg,
                "tglangkat"=>$angkat['tglangkat'],
                "nosk"=>$angkat['nosk']
            ];
        }
        $sanksi = \App\sanksi::pluck("name","id");
        $riwayatsanksi = \App\riwayatsanksi::where('id_peg',[$pegawai['id']])->paginate(10);
        $datasanksi=[];

        foreach ($riwayatsanksi as $rsanksi) {
            $sanksipegawai = \App\sanksi::where('id', [$rsanksi['sanksi']])->first();
            $sankpeg = $sanksipegawai['name'];

            $datasanksi[] = [
                "id" => $rsanksi['id'],
                "sanksipeg" => $sankpeg,
                "tglsanksi" => $rsanksi['tglsanksi'],
                "nosanksi" => $rsanksi['nosanksi'],
                "ket" => $rsanksi['ket']
            ];
        }

        $tglpangkat = $pegawai['tglpangkat'];
        $tglberkala = $pegawai['tglberkala'];
        $tunda = $pegawai['tunda'];
        $jdpangkat = \Carbon\carbon::parse($tglpangkat);
        $jdberkala = \Carbon\carbon::parse($tglberkala);

        $jdpang = $jdpangkat->addYears(4)->addmonths($tunda)->toDateString();
        $jdber = $jdberkala->addYears(2)->addmonths($tunda)->toDateString();




        return view("kepatuhan.detailpegawai",["pegawai"=>$pegawai,"cabang"=>$cabang,"kelamin"=>$kelamin,
            "jabatan"=>$jabatan,"umur"=>$umur,"agama"=>$agama,"kawin"=>$kawin,"pendidikan"=>$pendidikan,
            "pangkat"=>$pangkat,"cabang"=>$cabang,"keluarga"=>$datakel,"masakerja"=>$mkerja,
            "riwayatpendi"=>$datapend,"riwayatkerja"=>$datakerja,"tunjanganistri"=>$tunjanganistri,
            "tunjangananak"=>$tunjangananak,"tuncabang"=>$tuncabang,"total"=>$total,
            "pelatihan"=>$pelatihan,"ppensiun"=>$ppensiun,"smkerja"=>$smkerja,
            "spegawai"=>$spegawai,'dataangkat'=>$dataangkat,'datasanksi'=>$datasanksi,'bpjstk'=>$bpjstk,
            'bpjsks'=>$bpjsks,'pensiun'=>$pensiun,'pph'=>$pph,'fungsi'=>$fungsi,'gapokpeg'=>$gapokpeg,'gapok'=>$gapok,'tglpangkat'=>$tglpangkat,'tglberkala'=>$tglberkala,'tunda'=>$tunda,'jdpang'=>$jdpang,'jdber'=>$jdber,'jumlahanak'=>$jumlahanak,'pangan'=>$pangan,'tunpen'=>$tunpen,'tunjab'=>$tunjab]);

       }
    public function editpegawai($id){
        $pegawai = \App\Pegawai::findOrFail($id);
        $jenkel = \App\Jenkel::pluck('name','id');
        $agama = \App\Agama::pluck('name','id');
        $status = \App\Kawin::pluck('name','id');
        $pendidikan = \App\Pendidikan::pluck('name','id');
        $kantor = \App\Cabang::pluck('name','id');
        $jabatan = \App\Jabatan::pluck('name','id');
        $pangkat = \App\Pangkat::pluck('name','id');

        $jenkelpegawai = \App\Jenkel::where('id',$pegawai['kelamin'])->first();
        $kel = $jenkelpegawai->name;

        $agamapegawai = \App\agama::where('id',$pegawai['agama'])->first();
        $ag = $agamapegawai->name;

        $statuspegawai = \App\kawin::where('id',$pegawai['status'])->first();
        $stat=$statuspegawai->name;

        $pendidikanpegawai = \App\Pendidikan::where('id',$pegawai['pendidikan'])->first();
        $pend=$pendidikanpegawai->name;

        $kantorpegawai = \App\Cabang::where('id',$pegawai['cabang'])->first();
        $kant=$kantorpegawai->name;

        $jabatanpegawai = \App\Jabatan::where('id',$pegawai['jabatan'])->first();
        $jab=$jabatanpegawai->name;

        $pangkatpegawai = \App\Pangkat::where('id',$pegawai['pangkat'])->first();
        $pang=$pangkatpegawai->name;

        return view('kepatuhan.editpegawai',['pegawai' => $pegawai, 'jenkel' => $jenkel,'agama'=>$agama,'status'=>$status, 'pendidikan'=>$pendidikan, 'kantor'=>$kantor,'jabatan'=>$jabatan,'pangkat'=>$pangkat, 'kel'=>$kel,'ag'=>$ag,'stat'=>$stat, 'pend'=>$pend,'kant'=>$kant,'jab'=>$jab, 'pang'=>$pang]);
    }

    public function updatepegawai(Request $request, $id){
        $pegawai = \App\Pegawai::findOrFail($id);
        $iduser = \App\user::where('pegawai_id',$pegawai['id'])->first();
            //$userid = $iduser->user_id;

        //$user = \App\User::findOrFail($userid);

        $pegawai->name = $request->get('name');
        $iduser->name = $request->get('name');
        $iduser->username = $request->get('name');
        $pegawai->nikpegawai = $request->get('nikpegawai');
        $iduser->nikpegawai = $request->get('nikpegawai');
        $pegawai->email= $request->get('email');
        $iduser->email = $request->get('email');
        $pegawai->nikpenduduk = $request->get('nikpenduduk');
        $pegawai->templahir = $request->get('templahir');
        $pegawai->tgllahir = $request->get('tgllahir');
        $pegawai->kelamin = $request->get('jenkel');
        $pegawai->alamat = $request->get('alamat');
        $pegawai->agama = $request->get('agama');
        $pegawai->status = $request->get('status');
        $pegawai->pendidikan = $request->get('pendidikan');

        $newphoto = $request->file('photo');
        $newavatar = $request->file('photo');

        if($newphoto){
            if($pegawai->photo && file_exists(storage_path('app/public/'.$pegawai->photo))){
                \Storage::delete('public/'.$pegawai->photo);
            }
           $new_photo_path = $newphoto->store('pegawai-photo','public');
           $pegawai->photo = $new_photo_path;
       }
       if($newavatar){
        if($iduser->avatar && file_exists(storage_path('app/public/'.$iduser->avatar))){
                \Storage::delete('public/'.$iduser->avatar);
                 $new_avatar_path = $newphoto->store('avatars','public');
            $iduser->avatar = $new_avatar_path;}
            }



           $pegawai->updated_by = \Auth::user()->id;
           $pegawai->save();
           $iduser->updated_by = \Auth::user()->id;
           $iduser->save();

           return redirect()->route('kepatuhan.pegawai')->with('status','Data Pribadi Pegawai Berhasil Diupdate');
    }
    public function cutiindex(Request $request){

        $idcabang = \Auth::user()->cabang;
        $idpeg = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$idpeg)->first();
        $jabpeg = $peg->jabatan;
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
            ->wherehas('pegawai', function($query) use ($name){
                $query->where('name','LIKE',"%$name%");})
            ->where([
                ['status',"LIKE","SUBMIT"],
                ['statasan',"like","SUBMIT"],
                ['otoatasan',$jabpeg],
                ['cabang',$idcabang]
            ])
            ->orwhere([
                ['status',"LIKE","SUBMIT"],
                ['statasan',"like","DISETUJUI"],
                ['statdiket',"like","SUBMIT"],
                ['diketatasan',$jabpeg],
                ['cabang',$idcabang]
            ])

            ->where('cabang',$idcabang)->get();        $data=[];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id',$cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id',$cuti['cabang'])->first();
            $namacab=$cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                "id" => $cuti['id'],
                "namapeg" => $namapeg,
                "tglmohon"=> $cuti['created_at'],
                "jmlcuti" => $cuti['jmlcuti'],
                "tglawal" => $cuti['tglawal'],
                "tglakhir" => $cuti['tglakhir'],
                "alasan" => $cuti['alasan'],
                "namacab"=>$namacab,
                "status"=>$cuti['status'],
                "statasan"=>$cuti['statasan']
            ];

        }

        return view('kepatuhan.cutiindex',['orderc'=>$data]);

    }

    public function cutisetuju(Request $request){
        $idcabang = \Auth::user()->cabang;
        $idpeg = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$idpeg)->first();
        $jabpeg = $peg->jabatan;
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
            ->wherehas('pegawai', function($query) use ($name){
                $query->where('name','LIKE',"%$name%");})
            ->where([
                ['status',"LIKE","DISETUJUI"],
                ['statasan',"like","DISETUJUI"],
                ['otoatasan',$jabpeg],
                ['cabang',$idcabang]
            ])
            ->orwhere([
                ['status',"LIKE","DISETUJUI"],
                ['statasan',"like","DISETUJUI"],
                ['statdiket',"like","DISETUJUI"],
                ['diketatasan',$jabpeg],
                ['cabang',$idcabang]
            ])

            ->where('cabang',$idcabang)->get();
        $data=[];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id',$cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id',$cuti['cabang'])->first();
            $namacab=$cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                "id" => $cuti['id'],
                "namapeg" => $namapeg,
                "tglmohon"=> $cuti['created_at'],
                "jmlcuti" => $cuti['jmlcuti'],
                "tglawal" => $cuti['tglawal'],
                "tglakhir" => $cuti['tglakhir'],
                "alasan" => $cuti['alasan'],
                "namacab"=>$namacab,
                "status"=>$cuti['status']
            ];

        }
        return view('kepatuhan.cutisetuju',['orderc'=>$data]);
    }
    public function cutitolak (Request $request){
        $idcabang = \Auth::user()->cabang;
        $idpeg = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$idpeg)->first();
        $jabpeg = $peg->jabatan;
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
            ->wherehas('pegawai', function($query) use ($name){
                $query->where('name','LIKE',"%$name%");})
            ->where([
                ['status',"LIKE","DITOLAK"],
                ['otoatasan',$jabpeg],
                ['cabang',$idcabang]
            ])
            ->orwhere([
                ['status',"LIKE","DITOLAK"],

                ['diketatasan',$jabpeg],
                ['cabang',$idcabang]
            ])

            ->where('cabang',$idcabang)->get();
        $data=[];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id',$cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id',$cuti['cabang'])->first();
            $namacab=$cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                "id" => $cuti['id'],
                "namapeg" => $namapeg,
                "tglmohon"=> $cuti['created_at'],
                "jmlcuti" => $cuti['jmlcuti'],
                "tglawal" => $cuti['tglawal'],
                "tglakhir" => $cuti['tglakhir'],
                "alasan" => $cuti['alasan'],
                "namacab"=>$namacab,
                "status"=>$cuti['status']
            ];

        }

        return view('kepatuhan.cutitolak',['orderc'=>$data]);
    }
    public function permohonancuti(){
        $user = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id',$user)->first();

        return view('kepatuhan.permohonancuti',['pega'=>$pegawai]);

    }
    public function mintacuti(Request $request){
        $awal = $request->get('tglawal');
        $akhir = $request->get('tglakhir');
        $jeniscuti = $request->get('jeniscuti');
        $awlc = \Carbon\Carbon::parse($awal);
        $akhirc= \Carbon\Carbon::parse($akhir);
        $jumlahcuti = $awlc->diffinDays($akhirc);
        $user_id = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$user_id)->first();
        $jabpeg = $peg->jabatan;
        $jabatan = \App\Jabatan::where('id',$jabpeg)->first();
        $jabatasan = $jabatan->atasan;
        $jabket = \App\Jabatan::where('id',$jabatasan)->first();
        $jabketat = $jabatan->atasan;
        $jmlcuti = $jumlahcuti+1;
        if ($jeniscuti == "Cuti Tahunan") {
            $new_cuti  = new \App\ordercuti;
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jmlcuti = $jmlcuti;
            $new_cuti->tglawal = $awal;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->status = 'SUBMIT';
            $new_cuti->otoatasan = $jabatasan;
            $new_cuti->statasan = 'SUBMIT';
            $new_cuti->diketatasan = $jabketat;
            $new_cuti->statdiket = 'SUBMIT';
        } elseif ($jeniscuti == "Cuti Lainnya") {
            $new_cuti  = new \App\ordercuti;
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jmlcuti = $jmlcuti;
            $new_cuti->tglawal = $awal;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->status = 'SUBMIT';
            $new_cuti->otoatasan = $jabatasan;
            $new_cuti->statasan = 'SUBMIT';
            $new_cuti->diketatasan = $jabketat;
            $new_cuti->statdiket = 'SUBMIT';
        }else {
            $new_cuti  = new \App\ordercuti;
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jmlcuti = 5;
            $new_cuti->tglawal = $awal;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->status = 'SUBMIT';
            $new_cuti->otoatasan = $jabatasan;
            $new_cuti->statasan = 'SUBMIT';
            $new_cuti->diketatasan = $jabketat;
            $new_cuti->statdiket = 'SUBMIT';
            $new_cuti->otosdm = "ADMIN";
            $new_cuti->statsdm = "SUBMIT";
        }


        $new_cuti->save();



        return redirect()->route('kepatuhan.cutikepatuhan')->with('status','Permohonan Berhasil Diinput');
    }
    public function cutikepatuhan(){
        $id_user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($id_user){
                            $query->where('id',$id_user);})
                        ->get();
        $data=[];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id',$cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id',$cuti['cabang'])->first();
            $namacab=$cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                "id" => $cuti['id'],
                "namapeg" => $namapeg,
                "tglmohon"=> $cuti['created_at'],
                "jmlcuti" => $cuti['jmlcuti'],
                "tglawal" => $cuti['tglawal'],
                "tglakhir" => $cuti['tglakhir'],
                "alasan" => $cuti['alasan'],
                "namacab"=>$namacab,
                "status"=>$cuti['status']
            ];
             }

        return view('kepatuhan.cutikepatuhan',['orderc'=>$data]);

    }
    public function tolakcuti(){
        $user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::where('pegawai_id',$user)
                        ->where("status","like","DITOLAK")->get();
        $data=[];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id',$cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id',$cuti['cabang'])->first();
            $namacab=$cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                "id" => $cuti['id'],
                "namapeg" => $namapeg,
                "tglmohon"=> $cuti['created_at'],
                "jmlcuti" => $cuti['jmlcuti'],
                "tglawal" => $cuti['tglawal'],
                "tglakhir" => $cuti['tglakhir'],
                "alasan" => $cuti['alasan'],
                "namacab"=>$namacab,
                "status"=>$cuti['status']
            ];

        }

        return view('kepatuhan.tolakcuti',['orderc'=>$data]);

    }
    public function setujucuti(){
        $user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::where('pegawai_id',$user)
                        ->where("status","like","DISETUJUI")->get();
        $data=[];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id',$cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id',$cuti['cabang'])->first();
            $namacab=$cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                "id" => $cuti['id'],
                "namapeg" => $namapeg,
                "tglmohon"=> $cuti['created_at'],
                "jmlcuti" => $cuti['jmlcuti'],
                "tglawal" => $cuti['tglawal'],
                "tglakhir" => $cuti['tglakhir'],
                "alasan" => $cuti['alasan'],
                "namacab"=>$namacab,
                "status"=>$cuti['status']
            ];

        }

        return view('kepatuhan.setujucuti',['orderc'=>$data]);

    }
   public function setuju($id){
        $ordercuti = \App\ordercuti::findorFail($id);
        $pegawai = \App\Pegawai::where('id',$ordercuti['pegawai_id'])->first();
        $statasan = $ordercuti->statasan;
        $stadiket = $ordercuti->statdiket;
        $ambilcuti = $ordercuti->jmlcuti;
        $jeniscuti = $ordercuti->jeniscuti;
        $scuti = $pegawai->scuti;
        $sisacuti = $scuti - $ambilcuti;
        if($statasan == "SUBMIT"){
            if ($jeniscuti == "Cuti Wajib") {
                $ordercuti->statasan = 'DISETUJUI';
                $ordercuti->statdiket = 'DISETUJUI';
            $ordercuti->save();

        } elseif ($jeniscuti == "Cuti Tahunan") {
            $ordercuti->status = 'DISETUJUI';
            $ordercuti->statasan = 'DISETUJUI';
            $ordercuti->statdiket = 'DISETUJUI';
            $pegawai->scuti = $sisacuti;
            $ordercuti->save();
            $pegawai->save();
        } else {

            $ordercuti->status = 'DISETUJUI';
            $ordercuti->statasan = 'DISETUJUI';
            $ordercuti->statdiket = 'DISETUJUI';
            $ordercuti->save();
        }
        }else{
            if ($jeniscuti == "Cuti Wajib"){
                $ordercuti->statdiket = 'DISETUJUI';
                $ordercuti->save();

            } elseif ($jeniscuti == "Cuti Tahunan") {
                $ordercuti->status = 'DISETUJUI';
                $ordercuti->statdiket = 'DISETUJUI';
                $pegawai->scuti = $sisacuti;
                $ordercuti->save();
                $pegawai->save();
            } else {

                $ordercuti->status = 'DISETUJUI';
                $ordercuti->statdiket = 'DISETUJUI';
                $ordercuti->save();
            }

        }

        $ordercuti->save();
        $pegawai->save();
        return redirect()->route('kepatuhan.cutiindex')->with('status','Data Cuti Successfully Updated');

    }
    public function tolak($id){
        $ordercuti = \App\ordercuti::findorFail($id);
        $pegawai = \App\Pegawai::where('id',$ordercuti['pegawai_id'])->first();

        //$ambilcuti = $ordercuti->jmlcuti;
       // $scuti = $pegawai->scuti;
        //$sisacuti = $scuti-$ambilcuti;
        $ordercuti->status = 'DITOLAK';
        $ordercuti->statasan='DITOLAK';
        //$pegawai->scuti = $sisacuti;


        $ordercuti->save();
        //$pegawai->save();
        return redirect()->route('kepatuhan.cutiindex')->with('status','Data Cuti Successfully Updated');
    }

    public function rotasipegawai(Request $request){

    $idcabang = \Auth::user()->cabang;
        $filterkeyword = $request->get('keyword');
        //$jabstaff = \App\Jabatan::where('name','like','%Staff%');
        //$jabid = $jabstaff->id;
        if($filterkeyword){
            $datapegawai = \App\Pegawai::with('jabatan','cabang')->where("name","LIKE", "%$filterkeyword%")->where('cabang',$idcabang)->get();
        } else {
            $datapegawai = \App\Pegawai::with('jabatan','cabang')
                            ->where('cabang',$idcabang)
                            ->where('jabatan','>','10')
                            ->get();
        }


      $data = [];
        $now= \Carbon\Carbon::now()->format('Y-m-d');

      foreach ($datapegawai as $x) {
        $b_day = \Carbon\Carbon::parse($x['tgllahir']);
        $umur =$b_day->diffinYears($now);

        $masuk = \Carbon\Carbon::parse($x['tglmasuk']);
        $mkerja =$masuk->diffinYears($now);

        $peg = \App\Jabatan::where('id',$x['jabatan'])->first();
        $namajab = $peg['name'];

        $cab = \App\Cabang::where('id',$x['cabang'])->first();
        $namacab = $cab['name'];

        $pang = \App\Pangkat::where('id',$x['pangkat'])->first();
        $pangkat = $pang['name'];

        $data[] = [
            "id"=>$x['id'],
          "name"=>$x['name'],
            "umur"=>$umur,
            "mkerja"=>$mkerja,
            "photo"=>$x['photo'],
            "nikpegawai"=>$x['nikpegawai'],
            "status"=>$x['spegawai'],
            "pangkat"=>$pangkat,
            "jabatan"=>$namajab,
            "cabang"=>$namacab,


        ];

      }
      // return $data;

      return view ('kepatuhan.pegawairotasi',['pegawai'=>$data]);
    }
    public function mintarotasi($id){
      $peg = \App\Pegawai::findorfail($id);
      $pegjab = $peg->jabatan;
      $jab = \App\Jabatan::where('id',$pegjab)->first();
      $jabatan = $jab->name;
      $jabrot = \App\Jabatan::where('kantor','cabang')->where('name','LIKE','%staff%')->pluck('name','id');
      //$jabkan = $jabrot->name;

      return view ('kepatuhan.permohonanrotasi',['pegawai'=>$peg,'jabatan'=>$jabatan,'jabrot'=>$jabrot]);
    }
    public function inputrotasi(Request $request){
        $idpeg = $request->get('idpeg');
        $peg = \App\Pegawai::where('id',$idpeg)->first();

        $rotasi = new \App\mutasi;
        $rotasi->pegawai_id = $idpeg;
        $rotasi->cabang = $peg->cabang;
        $rotasi->jabatan = $request->get('jabatan');
        $rotasi->jenis = 'ROTASI';
        $rotasi->created_by = \Auth::user()->id;
        $rotasi->save();

        return redirect()->route('kepatuhan.pegawairotasi')->with('status','Permohonan Sudah Dinput');

    }

    public function datarotasi(Request $request){
      $name = $request->get('name');
      $cabang = \Auth::user()->cabang;
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","SUBMIT")->where('cabang',$cabang)->get();
        $data=[];
        foreach ($mutasi as $m) {
        $pegawai = \App\Pegawai::where('id',$m['pegawai_id'])->first();
            $idpeg = $pegawai['id'];
            $namapeg = $pegawai['name'];
            $cabangpeg = $pegawai['cabang'];
            $jabatanpeg = $pegawai['jabatan'];
        $cabang = \App\Cabang::where('id',$cabangpeg)->first();
            $namacab=$cabang['name'];
        $jabatan = \App\Jabatan::where('id',$jabatanpeg)->first();
            $namajab=$jabatan['name'];
        $cabangmut = \App\Cabang::where('id',$m['cabang'])->first();
            $namacabmut=$cabangmut['name'];
        $jabatanmut = \App\Jabatan::where('id',$m['jabatan'])->first();
            $namajabmut=$jabatanmut['name'];

            $data[]=[
                'id' => $m['id'],
                'pegawai_id' => $idpeg,
                "name" => $namapeg,
                "cabseb"=>$namacab,
                "cabmut"=>$namacabmut,
                "jabseb"=>$namajab,
                "jabmut"=>$namajabmut,
                "jenis"=>$m['jenis'],
                "status"=>$m['status']

            ];
        }
        return view ('kepatuhan.datarotasi',['mutasi'=>$data]);
    }

    public function setujurotasi(Request $request){
      $name = $request->get('name');
      $cabang = \Auth::user()->cabang;
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DISETUJUI")->where('cabang',$cabang)->get();
        $data=[];
        foreach ($mutasi as $m) {
        $pegawai = \App\Pegawai::where('id',$m['pegawai_id'])->first();
            $idpeg = $pegawai['id'];
            $namapeg = $pegawai['name'];
            $cabangpeg = $pegawai['cabang'];
            $jabatanpeg = $pegawai['jabatan'];
        $cabang = \App\Cabang::where('id',$cabangpeg)->first();
            $namacab=$cabang['name'];
        $jabatan = \App\Jabatan::where('id',$jabatanpeg)->first();
            $namajab=$jabatan['name'];
        $cabangmut = \App\Cabang::where('id',$m['cabang'])->first();
            $namacabmut=$cabangmut['name'];
        $jabatanmut = \App\Jabatan::where('id',$m['jabatan'])->first();
            $namajabmut=$jabatanmut['name'];

            $data[]=[
                'id' => $m['id'],
                'pegawai_id' => $idpeg,
                "name" => $namapeg,
                "cabseb"=>$namacab,
                "cabmut"=>$namacabmut,
                "jabseb"=>$namajab,
                "jabmut"=>$namajabmut,
                "jenis"=>$m['jenis'],
                "status"=>$m['status']

            ];
        }
        return view ('kepatuhan.setujurotasi',['mutasi'=>$data]);
    }
    public function tolakrotasi(Request $request){
      $name = $request->get('name');
      $cabang = \Auth::user()->cabang;
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DITOLAK")->where('cabang',$cabang)->get();
        $data=[];
        foreach ($mutasi as $m) {
        $pegawai = \App\Pegawai::where('id',$m['pegawai_id'])->first();
            $idpeg = $pegawai['id'];
            $namapeg = $pegawai['name'];
            $cabangpeg = $pegawai['cabang'];
            $jabatanpeg = $pegawai['jabatan'];
        $cabang = \App\Cabang::where('id',$cabangpeg)->first();
            $namacab=$cabang['name'];
        $jabatan = \App\Jabatan::where('id',$jabatanpeg)->first();
            $namajab=$jabatan['name'];
        $cabangmut = \App\Cabang::where('id',$m['cabang'])->first();
            $namacabmut=$cabangmut['name'];
        $jabatanmut = \App\Jabatan::where('id',$m['jabatan'])->first();
            $namajabmut=$jabatanmut['name'];

            $data[]=[
                'id' => $m['id'],
                'pegawai_id' => $idpeg,
                "name" => $namapeg,
                "cabseb"=>$namacab,
                "cabmut"=>$namacabmut,
                "jabseb"=>$namajab,
                "jabmut"=>$namajabmut,
                "jenis"=>$m['jenis'],
                "status"=>$m['status']

            ];
        }
        return view ('kepatuhan.tolakrotasi',['mutasi'=>$data]);
    }
    public function profile()
    {
        $iduser = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id',$iduser)->first();
        $cabang = \App\Cabang::where("id",$pegawai["cabang"])->first();
        $kelamin = \App\Jenkel::where("id",$pegawai["kelamin"])->first();
        $jabatan = \App\Jabatan::where("id",$pegawai["jabatan"])->first();
        $agama = \App\Agama::where("id",$pegawai["agama"])->first();
        $kawin = \App\Kawin::where("id",$pegawai["status"])->first();
        $pendidikan = \App\Pendidikan::where("id",$pegawai["pendidikan"])->first();
        $pangkat = \App\Pangkat::where("id",$pegawai["pangkat"])->first();
        $cabang= \App\Cabang::where("id",$pegawai["cabang"])->first();
        $tunkin = \App\Cabang::where("id",$pegawai['tuncab'])->first();
        $spegawai = \App\statuspeg::where("id",$pegawai["spegawai"])->first();
        $statuspegawai = \App\statuspeg::pluck("name","id");
        $statpegawai = $pegawai['spegawai'];
        $gaji = \App\gaji::where('idpeg',$pegawai['id'])->first();
        $keluarga = \App\keluarga::where("pegawai_id",[$pegawai["id"]])->get();
        $anak = \App\keluarga::where('pegawai_id',$pegawai['id'])->where('hubungan','Anak')->get();
        $jumlahanak = count($anak);
        $nikah = \App\Keluarga::where([
            ['pegawai_id',$pegawai['id']],
            ['hubungan','Istri']
        ])->orwhere([['pegawai_id',$pegawai['id']],
            ['hubungan','Suami']])->get();
        $jumlahnikah = count($nikah);
        $datakel=[];
        $now= \Carbon\Carbon::now()->format("Y-m-d");
        $b_day = \Carbon\Carbon::parse($pegawai["tgllahir"]);
        $umur =$b_day->diff($now)->format('%y Tahun %m Bulan');

        $masa = \Carbon\carbon::parse($pegawai["tglmasuk"]);
        $mkerja = $masa->diff($now)->format('%y Tahun %m Bulan');

        $ppensiun = $b_day->addYears(56)->format('Y-m-d');
        $hpensiun = \Carbon\carbon::parse($ppensiun);

        $smkerja = $hpensiun->diff($now)->format('%y Tahun %m Bulan');
        //$smkeria=$pensiun-$mkerja;
        foreach ($keluarga as $k) {
            $bday_kel = \Carbon\Carbon::parse($k["tgllahir"]);
            $umurkel = $bday_kel->diffinYears($now);

            $datakel[]=[
                "id"=> $k["id"],
                "name"=>$k["name"],
                "hub"=>$k["hubungan"],
                "templahir"=>$k["templahir"],
                "tgllahir" => $k["tgllahir"],
                "umurkel"=>$umurkel,
                "alamat"=>$k["alamat"]
            ];

        }

        $riwayatpendi = \App\riwayatpendi::where("pegawai_id",$pegawai["id"])->get();
        $datapend=[];
        foreach ($riwayatpendi as $pend) {
            $datapend[]=[
                "id"=> $pend["id"],
                "name"=>$pend["name"],
                "jurusan"=>$pend["jurusan"],
                "pendidikan" =>$pend["pendidikan"],
                "gelar"=>$pend["gelar"],
                "thnlulus"=>$pend["thnlulus"]
            ];
            //# code...
        }
        $riwayatkerja = \App\riwayatkerja::where("pegawai_id",$pegawai["id"])->get();
        $datakerja=[];
        foreach ($riwayatkerja as $kerja) {
            $awal = \Carbon\Carbon::parse($kerja["tglawal"]);
            $akhir = \Carbon\Carbon::parse ($kerja["tglakhir"]);
            $periode = $awal->diff($akhir)->format('%y Tahun %m Bulan');
            $datakerja[]=[
                "id"=>$kerja["id"],
                "name"=>$kerja["name"],
                "kantorcabang"=>$kerja["kantorcabang"],
                "tglawal"=>$kerja["tglawal"],
                "tglakhir" => $kerja["tglakhir"],
                "periode"=>$periode,
            ];
        }
        $pangkatpeg = $pegawai['pangkat'];
        $mkpangpeg = $pegawai['mkpang'];
        $gapok = \App\berkala::where([['idpang',"LIKE",$pangkatpeg],
            ['gol',"LIKE",$mkpangpeg]])->first();

        $tunpang = $jabatan['tunpang'];
        $jmlkeluarga = $jumlahnikah+$jumlahanak;
        $tunis = $jabatan['tunis'];
        if ($statpegawai == 1){
            $pangan = 0;
        } else {
            if ($jmlkeluarga > 3) {
                $pangan = $tunpang * 4;
            } elseif ($jmlkeluarga <= 3){
                $pangan = $tunpang * ($jumlahnikah+$jumlahanak+1);}

        }

        $tunak = $jabatan["tunak"];
        $tunjab = $gaji["jabatan"];
        if ($statpegawai == 3){
            $tuncab = $tunkin["tunjangan"];}
        else{
            $tuncab = 0;
        }


        $fungsi = $gaji["fungsi"];
        $gapokpeg = $gapok["gapok"];
        $bpjsks = $gaji["bpjsks"];
        $bpjstk = $gaji["bpjstk"];
        $pensiun = $jabatan['pensiun'];
        if ($statpegawai ==3){
            $tunpen = $pensiun * $gapokpeg;}
        else{
            $tunpen = 0;
        }
        $pph = $gaji['pph'];
        if ($statpegawai == 1){
            $tunjanganistri = 0;
        }else{
            $tunjanganistri = $tunis * $gapokpeg *$jumlahnikah;

        }

        if ($jumlahanak <= 2) {
            $tunjangananak = $tunak *$gapokpeg*$jumlahanak;
        } elseif ($jumlahanak > 2) {
            $tunjangananak = $tunak *$gapokpeg*2;
        }elseif ($statpegawai !=3){
            $tunjangananak = 0;
        }


        $tuncabang = $tuncab*$gapokpeg;
        $total =$gapokpeg+$tunjanganistri+$tunjangananak+$pangan+$tunjab+$tuncabang+$bpjstk+$bpjsks+$pph+$fungsi+$tunpen;

        $pelatihan = \App\pelatihan::where("pegawai_id",$pegawai["id"])->get();
        $datapelatihan=[];
        foreach ($pelatihan as $lat) {
            $datapelatihan[]=[
                "id"=>$lat["id"],
                "name"=>$lat["name"],
                "penyelenggara"=>$lat["penyelenggara"],
                "thnlatih"=>$lat["thnlatih"],
                "image"=>$lat["image"]
            ];
        }

        $riwayatangkat = \App\riwayatangkat::where('pegawai_id',[$pegawai['id']])->paginate(10);
        $dataangkat=[];

        foreach ($riwayatangkat as $angkat) {
            $statuspeg = \App\statuspeg::where('id',[$angkat['status']])->first();
            $statpeg = $statuspeg['name'];

            $dataangkat[]=[
                "id"=>$angkat['id'],
                "status"=>$statpeg,
                "tglangkat"=>$angkat['tglangkat'],
                "nosk"=>$angkat['nosk']
            ];
        }
        $sanksi = \App\sanksi::pluck("name","id");
        $riwayatsanksi = \App\riwayatsanksi::where('id_peg',[$pegawai['id']])->paginate(10);
        $datasanksi=[];

        foreach ($riwayatsanksi as $rsanksi) {
            $sanksipegawai = \App\sanksi::where('id', [$rsanksi['sanksi']])->first();
            $sankpeg = $sanksipegawai['name'];

            $datasanksi[] = [
                "id" => $rsanksi['id'],
                "sanksipeg" => $sankpeg,
                "tglsanksi" => $rsanksi['tglsanksi'],
                "nosanksi" => $rsanksi['nosanksi'],
                "ket" => $rsanksi['ket']
            ];
        }

        $tglpangkat = $pegawai['tglpangkat'];
        $tglberkala = $pegawai['tglberkala'];
        $tunda = $pegawai['tunda'];
        $jdpangkat = \Carbon\carbon::parse($tglpangkat);
        $jdberkala = \Carbon\carbon::parse($tglberkala);

        $jdpang = $jdpangkat->addYears(4)->addmonths($tunda)->toDateString();
        $jdber = $jdberkala->addYears(2)->addmonths($tunda)->toDateString();




        return view("kepatuhan.profile",["pegawai"=>$pegawai,"cabang"=>$cabang,"kelamin"=>$kelamin,
            "jabatan"=>$jabatan,"umur"=>$umur,"agama"=>$agama,"kawin"=>$kawin,"pendidikan"=>$pendidikan,
            "pangkat"=>$pangkat,"cabang"=>$cabang,"keluarga"=>$datakel,"masakerja"=>$mkerja,
            "riwayatpendi"=>$datapend,"riwayatkerja"=>$datakerja,"tunjanganistri"=>$tunjanganistri,
            "tunjangananak"=>$tunjangananak,"tuncabang"=>$tuncabang,"total"=>$total,
            "pelatihan"=>$pelatihan,"ppensiun"=>$ppensiun,"smkerja"=>$smkerja,
            "spegawai"=>$spegawai,'dataangkat'=>$dataangkat,'datasanksi'=>$datasanksi,'bpjstk'=>$bpjstk,
            'bpjsks'=>$bpjsks,'pensiun'=>$pensiun,'pph'=>$pph,'fungsi'=>$fungsi,'gapokpeg'=>$gapokpeg,'gapok'=>$gapok,'tglpangkat'=>$tglpangkat,'tglberkala'=>$tglberkala,'tunda'=>$tunda,'jdpang'=>$jdpang,'jdber'=>$jdber,'jumlahanak'=>$jumlahanak,'pangan'=>$pangan,'tunpen'=>$tunpen,'tunjab'=>$tunjab]);


        /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    }
     public function peraturan(Request $request){
        $peraturan = \App\peraturan::paginate(10);
        $filterkeyword = $request->get('name');


        if($filterkeyword){
            $peraturan = \App\peraturan::where("name", "LIKE", "%$filterkeyword%")->paginate(10);
        }


        return view('kepatuhan.peraturan',['peraturan'=>$peraturan]);
    }
    public function permohonandownload($id){
        $peraturan = \App\peraturan::findorfail($id);
        $idpeg=\Auth::user()->pegawai_id;
        $uscab = \Auth::user()->cabang;
        $pegawai = \App\Pegawai::where('id',$idpeg)->first();
        $cab = \App\Cabang::where('id',$uscab)->first();

        return view('kepatuhan.permohonandownload',['peraturan'=>$peraturan,'pegawai'=>$pegawai,'cabang'=>$cab]);
    }
    public function mintadownload(Request $request){
       $orderatur = new \App\orderatur;

       $orderatur->peraturan_id = $request->get('idperaturan');
       $orderatur->pegawai_id = $request->get('idpeg');
       $orderatur->cabang_id  = $request->get('cabang');
       $orderatur->ket = $request->get('ket');
       $orderatur->user_id = \Auth::user()->id;
       $orderatur->status = "SUBMIT";

       $orderatur->save();
       return redirect()->route('kepatuhan.peraturan')->with('status','Permintaan akan diproses');
   }
   public function statusatur(){
        //$user = \Auth::user()->id;
        $orderatur = \App\orderatur::where('status','SUBMIT')->paginate(10);

        $data=[];
        foreach ($orderatur as $order) {
        $pegawai = \App\Pegawai::where('id',$order['pegawai_id'])->first();
        $namapeg = $pegawai['name'];
        $cabang = \App\Cabang::where('id',$order['cabang_id'])->first();
        $namecab = $cabang['name'];
        $peraturan = \App\peraturan::where('id',$order['peraturan_id'])->first();
        $namaper = $peraturan['name'];
        $tglsk = $peraturan['tglsk'];
        $nosk = $peraturan['nosk'];


        $data[]=[
          'id' => $order['id'],
          'name' => $order['name'],
          'nosk' => $nosk,
          'tglsk'=> $tglsk,
          'namepeg'=> $namapeg,
          'namecab' => $namecab,
          'namepr' => $namaper,
          'ket' => $order['ket'],
          'status'=>$order['status'],
          'tglminta'=> $order['created_at']
        ];}
        return view ('kepatuhan.statusatur',['orderatur' => $data]);

   }
   public function setujuatur($id){
        $order = \App\orderatur::findorfail($id);

        $order->status = 'SETUJU';
        $order->save();

        return redirect()->route('kepatuhan.statusatur')->with('status','Permintaan Telah Disetujui');
   }

   public function tolakatur($id){
        $order = \App\orderatur::findorfail($id);

        $order->status = 'TOLAK';
        $order->save();
        return redirect()->route('kepatuhan.statusatur')->with('status','Permintaan Telah Ditolak');
   }
   public function setujudata(){
        //$user = \Auth::user()->id;
        $orderatur = \App\orderatur::where('status','SETUJU')->paginate(10);

        $data=[];
        foreach ($orderatur as $order) {
        $pegawai = \App\Pegawai::where('id',$order['pegawai_id'])->first();
        $namapeg = $pegawai['name'];
        $cabang = \App\Cabang::where('id',$order['cabang_id'])->first();
        $namecab = $cabang['name'];
        $peraturan = \App\peraturan::where('id',$order['peraturan_id'])->first();
        $namaper = $peraturan['name'];
        $tglsk = $peraturan['tglsk'];
        $nosk = $peraturan['nosk'];


        $data[]=[
          'id' => $order['id'],
          'name' => $order['name'],
          'nosk' => $nosk,
          'tglsk'=> $tglsk,
          'namepeg'=> $namapeg,
          'namecab' => $namecab,
          'namepr' => $namaper,
          'ket' => $order['ket'],
          'status'=>$order['status'],
          'tglminta'=> $order['created_at']
        ];}
        return view ('kepatuhan.setujuatur',['orderatur' => $data,'peraturan'=>$orderatur]);

   }
public function tolakdata(){
        //$user = \Auth::user()->id;
        $orderatur = \App\orderatur::where('status','TOLAK')->paginate(10);

        $data=[];
        foreach ($orderatur as $order) {
        $pegawai = \App\Pegawai::where('id',$order['pegawai_id'])->first();
        $namapeg = $pegawai['name'];
        $cabang = \App\Cabang::where('id',$order['cabang_id'])->first();
        $namecab = $cabang['name'];
        $peraturan = \App\peraturan::where('id',$order['peraturan_id'])->first();
        $namaper = $peraturan['name'];
        $tglsk = $peraturan['tglsk'];
        $nosk = $peraturan['nosk'];


        $data[]=[
          'id' => $order['id'],
          'name' => $order['name'],
          'nosk' => $nosk,
          'tglsk'=> $tglsk,
          'namepeg'=> $namapeg,
          'namecab' => $namecab,
          'namepr' => $namaper,
          'ket' => $order['ket'],
          'status'=>$order['status'],
          'tglminta'=> $order['created_at']
        ];}
        return view ('kepatuhan.tolakatur',['orderatur' => $data,'peraturan'=>$orderatur]);

   }
    public function loguser(Request $request)
    {
        $logs = $this->getFilteredLogs($request)->paginate(15);
        return view('kepatuhan.loguser', compact('logs'));
    }

    public function exportLoguser(Request $request)
    {
        $logs = $this->getFilteredLogs($request)->get();
        $fileName = 'log_pegawai_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Pegawai', 'Jenis', 'Keterangan', 'Waktu Akses', 'Mulai', 'Selesai', 'Durasi (Menit)']);

            foreach ($logs as $index => $log) {
                $duration = '-';
                if (!is_null($log->active_seconds)) {
                    $duration = floor($log->active_seconds / 60) . " menit";
                }

                fputcsv($file, [
                    $index + 1,
                    $log->nampeg ?? '-',
                    $log->jenis ?? '-',
                    $log->keterangan ?? '-',
                    $log->waktu_akses ? Carbon::parse($log->waktu_akses)->format('d/m/Y H:i:s') : '-',
                    $log->mulai ? Carbon::parse($log->mulai)->format('d/m/Y H:i:s') : '-',
                    $log->selesai ? Carbon::parse($log->selesai)->format('d/m/Y H:i:s') : '-',
                    $duration
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getFilteredLogs(Request $request)
    {
        $keyword = trim((string) $request->get('keyword'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $filterMonth = $request->get('filter_month'); // YYYY-MM
        $jenis = $request->get('jenis');
        $combinedQuery = null;
        if ($filterMonth) {
            try {
                $date = Carbon::createFromFormat('Y-m', $filterMonth);
                $startDate = $date->startOfMonth()->format('Y-m-d');
                $endDate = $date->endOfMonth()->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }

        if (\Schema::hasTable('logusers')) {
            $logAksesQuery = \DB::table('logusers as lu')
                ->select([
                    'lu.nampeg as nampeg',
                    \DB::raw("COALESCE(NULLIF(lu.jenis, ''), 'Akses') as jenis"),
                    \DB::raw("CASE 
                        WHEN lu.jenis = 'Print' THEN 'Cetak Dokumen'
                        WHEN lu.jenis = 'Lihat Dokumen' THEN 'Lihat Detail'
                        WHEN lu.jenis = 'Permintaan Data' THEN 'Permintaan Data Peraturan'
                        ELSE 'Log Akses'
                    END as sub_jenis"),
                    \DB::raw('lu.keterangan as keterangan'),
                    \DB::raw('lu.created_at as waktu_akses'),
                    \DB::raw('NULL as mulai'),
                    \DB::raw('NULL as selesai'),
                    \DB::raw('NULL as active_seconds'),
                ]);

            if ($keyword) {
                $logAksesQuery->where(function ($q) use ($keyword) {
                    $q->where('lu.nampeg', 'like', "%{$keyword}%")
                      ->orWhere('lu.keterangan', 'like', "%{$keyword}%")
                      ->orWhere('lu.jenis', 'like', "%{$keyword}%");
                });
            }

            if ($startDate) $logAksesQuery->whereDate('lu.created_at', '>=', $startDate);
            if ($endDate) $logAksesQuery->whereDate('lu.created_at', '<=', $endDate);

            $combinedQuery = $logAksesQuery;
        }

        if (\Schema::hasTable('peraturan_view_sessions')) {
            $aktivitasPeraturanQuery = \DB::table('peraturan_view_sessions as pvs')
                ->leftJoin('peraturans as p', 'p.id', '=', 'pvs.peraturan_id')
                ->leftJoin('pegawais as peg', 'peg.id', '=', 'pvs.pegawai_id')
                ->select([
                    \DB::raw('COALESCE(peg.name, "-") as nampeg'),
                    \DB::raw("'Lihat Dokumen' as jenis"),
                    \DB::raw("'Durasi Baca Aktif' as sub_jenis"),
                    \DB::raw('COALESCE(p.name, "-") as keterangan'),
                    \DB::raw('pvs.started_at as waktu_akses'),
                    \DB::raw('pvs.started_at as mulai'),
                    \DB::raw('pvs.ended_at as selesai'),
                    \DB::raw('pvs.active_seconds as active_seconds'),
                ]);

            if ($keyword) {
                $aktivitasPeraturanQuery->where(function ($q) use ($keyword) {
                    $q->where('peg.name', 'like', "%{$keyword}%")
                      ->orWhere('p.name', 'like', "%{$keyword}%");
                });
            }

            if ($startDate) $aktivitasPeraturanQuery->whereDate('pvs.started_at', '>=', $startDate);
            if ($endDate) $aktivitasPeraturanQuery->whereDate('pvs.started_at', '<=', $endDate);

            if ($combinedQuery) {
                $combinedQuery = $combinedQuery->unionAll($aktivitasPeraturanQuery);
            } else {
                $combinedQuery = $aktivitasPeraturanQuery;
            }
        }

        if ($combinedQuery) {
            return \DB::query()
                ->fromSub($combinedQuery, 'logs')
                ->orderByDesc('waktu_akses')
                ->when($jenis, function ($q) use ($jenis) {
                    $q->where('jenis', $jenis);
                });
        }
        return \DB::table('logusers')->whereRaw('1 = 0');
    }

    public function cutiwajib()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$user)->first();

        return view('kepatuhan.cutiwajib',['pegawai'=>$peg]);
    }
    public function cutilainnya()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$user)->first();

        return view('kepatuhan.cutilainnya',['pegawai'=>$peg]);
    }
}
