<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Pegawai;
class PincabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware(function($request, $next){
        if(gate::allows('PINCAB')) return $next($request);
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
        $filterkeyword = $request->get('keyword');
        if($filterkeyword){
            $datapegawai = \App\Pegawai::with('jabatan','cabang')->where("name","LIKE", "%$filterkeyword%")->where('cabang',$idcabang)->get();
        } else {
            $datapegawai = \App\Pegawai::with('jabatan','cabang')->where('cabang',$idcabang)->get();
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
      // return $data;

      return view ('pincab.pegawai',['pegawai'=>$data]);
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




        return view("pincab.profile",["pegawai"=>$pegawai,"cabang"=>$cabang,"kelamin"=>$kelamin,
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




        return view("pincab.detailpegawai",["pegawai"=>$pegawai,"cabang"=>$cabang,"kelamin"=>$kelamin,
            "jabatan"=>$jabatan,"umur"=>$umur,"agama"=>$agama,"kawin"=>$kawin,"pendidikan"=>$pendidikan,
            "pangkat"=>$pangkat,"cabang"=>$cabang,"keluarga"=>$datakel,"masakerja"=>$mkerja,
            "riwayatpendi"=>$datapend,"riwayatkerja"=>$datakerja,"tunjanganistri"=>$tunjanganistri,
            "tunjangananak"=>$tunjangananak,"tuncabang"=>$tuncabang,"total"=>$total,
            "pelatihan"=>$pelatihan,"ppensiun"=>$ppensiun,"smkerja"=>$smkerja,
            "spegawai"=>$spegawai,'dataangkat'=>$dataangkat,'datasanksi'=>$datasanksi,'bpjstk'=>$bpjstk,
            'bpjsks'=>$bpjsks,'pensiun'=>$pensiun,'pph'=>$pph,'fungsi'=>$fungsi,'gapokpeg'=>$gapokpeg,'gapok'=>$gapok,'tglpangkat'=>$tglpangkat,'tglberkala'=>$tglberkala,'tunda'=>$tunda,'jdpang'=>$jdpang,'jdber'=>$jdber,'jumlahanak'=>$jumlahanak,'pangan'=>$pangan,'tunpen'=>$tunpen,'tunjab'=>$tunjab]);


    }
     public function permohonancuti(){
        $user = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id',$user)->first();

        return view('pincab.permohonancuti',['pega'=>$pegawai]);

    }
     public function cutipincab(){
        $id_user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($id_user){
                            $query->where('id',$id_user);})
                        ->where("status","like","SUBMIT")->get();
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

        return view('pincab.cutipincab',['orderc'=>$data]);

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
        $jabket = \App\jabatan::where('id',$jabatasan)->first();
        $jabketat = $jabket->atasan;
        $jmlcuti = $jumlahcuti+1;

        if($jeniscuti == "Cuti Wajib"){
            $new_cuti  = new \App\ordercuti;
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->jmlcuti = 3;
            $new_cuti->tglawal = $awal;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->status = "SUBMIT";
            $new_cuti->otoatasan = "$jabatasan";
            $new_cuti->statasan = "SUBMIT";
            $new_cuti->diketatasan = "$jabketat";
            $new_cuti->statdiket = "SUBMIT";
            $new_cuti->otosdm = "ADMIN";
            $new_cuti->statsdm = "SUBMIT";
            }elseif($jeniscuti== "Cuti Lainnya"){
            $new_cuti  = new \App\ordercuti;
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jmlcuti = $jmlcuti;
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->tglawal = $awal;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->status = 'SUBMIT';
            $new_cuti->otoatasan = "$jabatasan";
            $new_cuti->statasan = "SUBMIT";
            $new_cuti->diketatasan = "$jabketat";
            $new_cuti->statdiket = 'SUBMIT';
            }else{
            $new_cuti  = new \App\ordercuti;
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jmlcuti = $jmlcuti;
            $new_cuti->tglawal = $awal;
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->status = 'SUBMIT';
            $new_cuti->otoatasan = "$jabatasan";
            $new_cuti->statasan = "SUBMIT";
            $new_cuti->diketatasan = "$jabketat";
            $new_cuti->statdiket = 'SUBMIT';
            }
            $new_cuti->save();

        return redirect()->route('pincab.cutipincab')->with('status','Permohonan Berhasil Diinput');
    }
    public function tolakcuti(){
        $user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($user){
                            $query->where('id',$user);})
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

        return view('pincab.tolakcuti',['orderc'=>$data]);

    }
     public function setujucuti(){
        $user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($user){
                            $query->where('id',$user);})
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

        return view('pincab.setujucuti',['orderc'=>$data]);

    }
    public function cutiindex(Request $request){

        $idcabang = \Auth::user()->cabang;
        $idpeg = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$idpeg)->first();
        $jabpeg = $peg->jabatan;
        //$cabpeg = $peg->cabang;
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::where([['status',"LIKE","SUBMIT"],
                                ['otoatasan',$jabpeg],
                                ["statasan","like","SUBMIT"],
                                ['cabang',$idcabang]
                                ])
                        ->orwhere([['status',"LIKE","SUBMIT"],
                               ["statasan","like","DISETUJUI"],
                               ["statdiket","like","SUBMIT"],
                             ['diketatasan',$jabpeg],['cabang',$idcabang]])
                        //->where('cabang',$idcabang)
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
                "status"=>$cuti['status'],
                "statasan"=>$cuti['statasan'],
                "statdiket"=>$cuti['statdiket']
            ];

        }

        return view('pincab.cutiindex',['orderc'=>$data]);

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

        return redirect()->route('pincab.cutiindex')->with('status','Data Cuti Successfully Updated');

    }
    public function tolak($id){
        $ordercuti = \App\ordercuti::findorFail($id);
        $pegawai = \App\Pegawai::where('id',$ordercuti['pegawai_id'])->first();

        //$ambilcuti = $ordercuti->jmlcuti;
       // $scuti = $pegawai->scuti;
        //$sisacuti = $scuti-$ambilcuti;
        $ordercuti->status = 'DITOLAK';
        $ordercuti->statdiket='DITOLAK';
        //$pegawai->scuti = $sisacuti;


        $ordercuti->save();
        //$pegawai->save();
        return redirect()->route('pincab.cutiindex')->with('status','Data Cuti Successfully Updated');
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

        return view('pincab.cutisetuju',['orderc'=>$data]);
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

        return view('pincab.cutitolak',['orderc'=>$data]);
    }
    public function rotasiindex(Request $request){
        $cabang = \Auth::user()->cabang;
        $name = $request->get('name');
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","SUBMIT")
                        ->where('cabang',$cabang)
                        ->get();
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
        return view ('pincab.rotasiindex',['mutasi'=>$data]);
    }
     public function rotasisetuju($id){
        $mutasi = \App\mutasi::findorFail($id);
        $pegawai = \App\Pegawai::where('id',$mutasi['pegawai_id'])->first();
        $jabatan = \App\Jabatan::where('id',$mutasi['jabatan'])->first();
        $atasan1 = $jabatan->atasan;
        $jabatasan1 = \App\Jabatan::where('id',$jabatan)->first();
        $atasan2 = $jabatasan1->atasan;
        $jabatasan2 = \App\Jabatan::where('id',$atasan1)->first();
        $cabang = \App\Cabang::where('id',$mutasi['cabang'])->first();
        $riwayatkerja = new \App\riwayatkerja;
        $now= \Carbon\Carbon::now()->format('Y');

        $mutasi->status = 'DISETUJUI';
        $pegawai->jabatan =$mutasi->jabatan;
        $pegawai->cabang = $mutasi->cabang;
        $pegawai->atasan1 = $atasan1;
        $pegawai->atasan2 = $atasan2;
        $riwayatkerja->name = $jabatan->name;
        $riwayatkerja->kantorcabang = $mutasi->cabang;
        $riwayatkerja->pegawai_id = $mutasi->pegawai_id;
        $riwayatkerja->thnangkat = $now;
        $riwayatkerja->created_by = \Auth::user()->id;

        $mutasi->save();
        $riwayatkerja->save();
        $pegawai->save();

        return redirect()->route('pincab.rotasiindex')->with('status','Data Rotasi Disetujui');

    }
    public function rotasitolak($id){
        $mutasi = \App\mutasi::findorFail($id);
        $mutasi->status = 'DITOLAK';
        $mutasi->save();

        return redirect()->route('pincab.rotasiindex')->with('status','Data Rotasi Ditolak');

    }

    public function rotasidisetujui(Request $request){
        $cabang = \Auth::user()->cabang;
        $name = $request->get('name');
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DISETUJUI")
                        ->where('cabang',$cabang)
                        ->get();
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
        return view ('pincab.rotasisetuju',['mutasi'=>$data]);
    }
    public function rotasiditolak(Request $request){
        $cabang = \Auth::user()->cabang;
        $name = $request->get('name');
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DITOLAK")
                        ->where('cabang',$cabang)
                        ->get();
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
        return view ('pincab.rotasitolak',['mutasi'=>$data]);
    }
     public function peraturan(Request $request){
        $peraturan = \App\peraturan::paginate(10);
        $filterkeyword = $request->get('name');


        if($filterkeyword){
            $peraturan = \App\peraturan::where("name", "LIKE", "%$filterkeyword%")->paginate(10);
        }


        return view('pincab.peraturan',['peraturan'=>$peraturan]);
    }
    public function permohonandownload($id){
        $peraturan = \App\peraturan::findorfail($id);
        $idpeg=\Auth::user()->pegawai_id;
        $uscab = \Auth::user()->cabang;
        $pegawai = \App\Pegawai::where('id',$idpeg)->first();
        $cab = \App\Cabang::where('id',$uscab)->first();

        return view('pincab.permohonandownload',['peraturan'=>$peraturan,'pegawai'=>$pegawai,'cabang'=>$cab]);
    }
    public function mintadownload(Request $request){
       $idperaturan = $request->get('idperaturan');
       $orderatur = new \App\orderatur;

       $orderatur->peraturan_id = $idperaturan;
       $orderatur->pegawai_id = $request->get('idpeg');
       $orderatur->cabang_id  = $request->get('cabang');
       $orderatur->ket = $request->get('ket');
       $orderatur->user_id = \Auth::user()->id;
       $orderatur->status = "SUBMIT";
       $orderatur->print="f";
       $orderatur->save();
       $new_loguser = new \App\loguser;
        $user = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id',$user)->first();
        $peraturan = \App\peraturan::where('id',$idperaturan)->first();
        $new_loguser->nampeg = $pegawai->name;
        $new_loguser->jenis ='Permintaan Data';
        $new_loguser->keterangan = $peraturan->name;
        //$new_loguser->waktu = $time;
        $new_loguser->save();
       return redirect()->route('pincab.peraturan')->with('status','Permintaan akan diproses');
   }

public function statusatur()
{
    $userId = \Auth::user()->id;

    $orders = \App\orderatur::with(['peraturan', 'pegawai'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

    $orders->getCollection()->transform(function ($order) {
        return [
            'idatur'   => $order->peraturan_id,
            'nosk'     => optional($order->peraturan)->nosk,
            'tglsk'    => optional($order->peraturan)->tglsk,
            'namepeg'  => optional($order->pegawai)->name,
            'namepr'   => optional($order->peraturan)->name,
            'ket'      => $order->ket,
            'status'   => $order->status,
            'print'    => $order->print,  
            'tglminta' => $order->created_at
        ];
    });

    // 3. Kirim variabel $orders ke view
    return view('pincab.statusatur', ['orderatur' => $orders]);
}
  public function showatur($id)
{
    $peraturan = \App\peraturan::findOrFail($id);
    $time = \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i:s');

    $order = \App\orderatur::where('peraturan_id',$id)
                ->where('user_id',\Auth::user()->id)
                ->first();

    $new_loguser = new \App\loguser;
    $pegawai = \App\Pegawai::where('id',\Auth::user()->pegawai_id)->first();
    $new_loguser->nampeg = $pegawai->name;
    $new_loguser->jenis ='Lihat Dokumen';
    $new_loguser->keterangan = $peraturan->name;
    $new_loguser->save();

    return view('pincab.showatur',[
        'peraturan'=>$peraturan,
        'time'=>$time,
        'order'=>$order 
    ]);
}
public function print_pdf($id)
{
    $userId = \Auth::user()->id;
    $order = \App\orderatur::where('peraturan_id', $id)
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->first();

    if (!$order) {
        return redirect()->route('pincab.peraturan')
            ->with('status', 'Anda belum meminta dokumen ini');
    }

    $status = strtoupper(trim($order->status));

    if (!in_array($status, ["SETUJU", "DISETUJUI", "APPROVE"])) {
        return redirect()->back()
            ->with('status', 'Dokumen belum disetujui');
    }

    if ($order->print == "t") {
        return redirect()->back()
            ->with('status', 'Dokumen sudah pernah di print. Silakan minta akses ulang.');
    }

    $peraturan = \App\peraturan::findOrFail($id);
    $time = \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i:s');

    return view('pincab.show_pdf', [
        'peraturan' => $peraturan,
        'time' => $time,
        'order' => $order // Kirim ID order ke view agar mudah dipanggil AJAX
    ]);
}

public function update_status_print(Request $request)
{
    $order = \App\orderatur::find($request->order_id);
    if($order) {
        $order->print = "t";
        $order->save();

        $peraturan = \App\peraturan::find($order->peraturan_id);
        $pegawai = \App\Pegawai::where('id', \Auth::user()->pegawai_id)->first();
        
        $new_loguser = new \App\loguser;
        $new_loguser->nampeg = $pegawai->name;
        $new_loguser->jenis = 'Print';
        $new_loguser->keterangan = $peraturan->name;
        $new_loguser->save();

        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 404);
}
    public function cutiwajib()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$user)->first();

        return view('pincab.cutiwajib',['pegawai'=>$peg]);
    }
    public function cutilainnya()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id',$user)->first();

        return view('pincab.cutilainnya',['pegawai'=>$peg]);
    }
}
