<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Pegawai;
class DirbisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware(function($request, $next){
        if(gate::allows('DIRUT')) return $next($request);
        abort(403,'Anda tidak memiliki hak Akses');
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
            ->where('cabang',$idcabang)
            //->where('jabatan','in',$jabpeg)
            ->paginate(10);
        } else {
            $datapegawai = \App\Pegawai::with('jabatan','cabang')
            ->where('cabang',$idcabang)
            //->whereIN('jabatan',array($jabpeg))
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

      return view ('dirbis.pegawai',['pegawai'=>$data]);
    }
    public function profile()
    {
        $iduser = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id',$iduser)->first();
        $cabang = \App\cabang::where('id',$pegawai['cabang'])->first();
        $kelamin = \App\jenkel::where('id',$pegawai['kelamin'])->first();
        $jabatan = \App\jabatan::where('id',$pegawai['jabatan'])->first();
        $agama = \App\Agama::where('id',$pegawai['agama'])->first();
        $kawin = \App\Kawin::where('id',$pegawai['status'])->first();
        $pendidikan = \App\pendidikan::where('id',$pegawai['pendidikan'])->first();
        $pangkat = \App\pangkat::where('id',$pegawai['pangkat'])->first();
        $cabang= \App\cabang::where('id',$pegawai['cabang'])->first();
        $keluarga = \App\keluarga::where('pegawai_id',[$pegawai['id']])->get();
         $datakel=[];   
        $now= \Carbon\Carbon::now()->format('Y-m-d');
         $b_day = \Carbon\Carbon::parse($pegawai['tgllahir']);
            $umur =$b_day->diffinYears($now);

          $masa = \Carbon\carbon::parse($pegawai['tglmasuk']);
          $mkerja = $masa->diffinYears($now);
        foreach ($keluarga as $k) {
        $bday_kel = \Carbon\Carbon::parse($k['tgllahir']);
        $umurkel = $bday_kel->diffinYears($now);
          
         $datakel[]=[
           'id'=> $k['id'],
            'name'=>$k['name'],
            'hub'=>$k['hubungan'],
            'templahir'=>$k['templahir'],
            'tgllahir' => $k['tgllahir'],
            'umurkel'=>$umurkel,
            'alamat'=>$k['alamat']
          ];

          }

          $riwayatpendi = \App\riwayatpendi::where('pegawai_id',$pegawai['id'])->get();
          $datapend=[];
          foreach ($riwayatpendi as $pend) {
            $datapend[]=[
              'id'=> $pend['id'],
              'name'=>$pend['name'],
              'pendidikan' =>$pend['pendidikan'],
              'gelar'=>$pend['gelar'],
              'thnlulus'=>$pend['thnlulus']
            ];
            //# code...
          }
          $riwayatkerja = \App\riwayatkerja::where('pegawai_id',$pegawai['id'])->get();
          $datakerja=[];
          foreach ($riwayatkerja as $kerja) {
            $datakerja[]=[
              'id'=>$kerja['id'],
              'name'=>$kerja['name'],
              'kantorcabang'=>$kerja['kantorcabang'],
              'thnangkat'=>$kerja['thnangkat']
            ];
          }

          $tunis = $jabatan['tunis'];
          $tunak = $jabatan['tunak'];
          $tunjab = $jabatan['tunjab'];
          $tunpang = $jabatan['tunpang'];
          $tuncab = $cabang['tunjangan'];
          $gapok = $pangkat['gapok'];
          $tunjanganistri = $tunis * $gapok; 
          $tunjangananak = $tunak *$gapok;
          $tuncabang = $tuncab*$gapok;
          $total = $gapok+$tunjanganistri+$tunjangananak+$tunpang+$tunjab+$tuncabang;

          $pelatihan = \App\pelatihan::where('pegawai_id',$pegawai['id'])->get();
          $datapelatihan=[];
          foreach ($pelatihan as $lat) {
            $datapelatihan[]=[
              'id'=>$lat['id'],
              'name'=>$lat['name'],
              'penyelenggara'=>$lat['penyelenggara'],
              'thnlatih'=>$lat['thnlatih'],
              'image'=>$lat['image']
            ];
          }

        return view('dirbis.profile',['pegawai'=>$pegawai,'cabang'=>$cabang,'kelamin'=>$kelamin,'jabatan'=>$jabatan,'umur'=>$umur,'agama'=>$agama,'kawin'=>$kawin,'pendidikan'=>$pendidikan,'pangkat'=>$pangkat,'cabang'=>$cabang,'keluarga'=>$datakel,'masakerja'=>$mkerja, 'riwayatpendi'=>$datapend,'riwayatkerja'=>$datakerja,'tunjanganistri'=>$tunjanganistri,'tunjangananak'=>$tunjangananak,'tuncabang'=>$tuncabang,'total'=>$total,'pelatihan'=>$pelatihan]);
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    }
    public function detailpegawai($id){
        $pegawai = \App\Pegawai::findorfail($id);
        $cabang = \App\cabang::where('id',$pegawai['cabang'])->first();
        $kelamin = \App\jenkel::where('id',$pegawai['kelamin'])->first();
        $jabatan = \App\jabatan::where('id',$pegawai['jabatan'])->first();
        $agama = \App\Agama::where('id',$pegawai['agama'])->first();
        $kawin = \App\Kawin::where('id',$pegawai['status'])->first();
        $pendidikan = \App\pendidikan::where('id',$pegawai['pendidikan'])->first();
        $pangkat = \App\pangkat::where('id',$pegawai['pangkat'])->first();
        $cabang= \App\cabang::where('id',$pegawai['cabang'])->first();
        $keluarga = \App\keluarga::where('pegawai_id',[$pegawai['id']])->get();
         $datakel=[];   
        $now= \Carbon\Carbon::now()->format('Y-m-d');
         $b_day = \Carbon\Carbon::parse($pegawai['tgllahir']);
            $umur =$b_day->diffinYears($now);

          $masa = \Carbon\carbon::parse($pegawai['tglmasuk']);
          $mkerja = $masa->diffinYears($now);
        foreach ($keluarga as $k) {
        $bday_kel = \Carbon\Carbon::parse($k['tgllahir']);
        $umurkel = $bday_kel->diffinYears($now);
          
         $datakel[]=[
           'id'=> $k['id'],
            'name'=>$k['name'],
            'hub'=>$k['hubungan'],
            'templahir'=>$k['templahir'],
            'tgllahir' => $k['tgllahir'],
            'umurkel'=>$umurkel,
            'alamat'=>$k['alamat']
          ];

          }

          $riwayatpendi = \App\riwayatpendi::where('pegawai_id',$pegawai['id'])->get();
          $datapend=[];
          foreach ($riwayatpendi as $pend) {
            $datapend[]=[
              'id'=> $pend['id'],
              'name'=>$pend['name'],
              'pendidikan' =>$pend['pendidikan'],
              'gelar'=>$pend['gelar'],
              'thnlulus'=>$pend['thnlulus']
            ];
            //# code...
          }
          $riwayatkerja = \App\riwayatkerja::where('pegawai_id',$pegawai['id'])->get();
          $datakerja=[];
          foreach ($riwayatkerja as $kerja) {
            $datakerja[]=[
              'id'=>$kerja['id'],
              'name'=>$kerja['name'],
              'kantorcabang'=>$kerja['kantorcabang'],
              'thnangkat'=>$kerja['thnangkat']
            ];
          }

          $tunis = $jabatan['tunis'];
          $tunak = $jabatan['tunak'];
          $tunjab = $jabatan['tunjab'];
          $tunpang = $jabatan['tunpang'];
          $tuncab = $cabang['tunjangan'];
          $gapok = $pangkat['gapok'];
          $tunjanganistri = $tunis * $gapok; 
          $tunjangananak = $tunak *$gapok;
          $tuncabang = $tuncab*$gapok;
          $total = $gapok+$tunjanganistri+$tunjangananak+$tunpang+$tunjab+$tuncabang;

          $pelatihan = \App\pelatihan::where('pegawai_id',$pegawai['id'])->get();
          $datapelatihan=[];
          foreach ($pelatihan as $lat) {
            $datapelatihan[]=[
              'id'=>$lat['id'],
              'name'=>$lat['name'],
              'penyelenggara'=>$lat['penyelenggara'],
              'thnlatih'=>$lat['thnlatih'],
              'image'=>$lat['image']
            ];
          }

        return view('dirbis.detailpegawai',['pegawai'=>$pegawai,'cabang'=>$cabang,'kelamin'=>$kelamin,'jabatan'=>$jabatan,'umur'=>$umur,'agama'=>$agama,'kawin'=>$kawin,'pendidikan'=>$pendidikan,'pangkat'=>$pangkat,'cabang'=>$cabang,'keluarga'=>$datakel,'masakerja'=>$mkerja, 'riwayatpendi'=>$datapend,'riwayatkerja'=>$datakerja,'tunjanganistri'=>$tunjanganistri,'tunjangananak'=>$tunjangananak,'tuncabang'=>$tuncabang,'total'=>$total,'pelatihan'=>$pelatihan]);
    }
     public function permohonancuti(){
        $user = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id',$user)->first();

        return view('dirbis.permohonancuti',['pega'=>$pegawai]);
    
    }
     public function cutidirut(){
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
        
        return view('dirbis.cutikadiv',['orderc'=>$data]);
   
    }

    public function mintacuti(Request $request){
        $awal = $request->get('tglawal');
        $akhir = $request->get('tglakhir');
        $awlc = \Carbon\Carbon::parse($awal);
        $akhirc= \Carbon\Carbon::parse($akhir);
        $jmlcuti = $awlc->diffinDays($akhirc);
        $user_id = \Auth::user()->pegawai_id;
        $peg = \App\pegawai::where('id',$user_id)->first();
        $jabpeg = $peg->jabatan;
        $jabatan = \App\Jabatan::where('id',$jabpeg)->first();
        $jabatasan = $jabatan->atasan;
        $jabket = \App\jabatan::where('id',$jabtatasan)->first();
        $jabketat = $jabatan->atasan; 

        $new_cuti  = new \App\ordercuti;
        $new_cuti->user_id = \Auth::user()->id;
        $new_cuti->cabang = \Auth::user()->cabang;
        $new_cuti->pegawai_id = $request->get('idpeg');
        $new_cuti->jmlcuti = $jmlcuti;
        $new_cuti->tglawal = $awal;
        $new_cuti->tglakhir = $akhir;
        $new_cuti->alasan = $request->get('alasan');
        $new_cuti->status = 'SUBMIT';
        $new_cuti->otoatasan = $jabatasan;
        $newcuti->statatasan = 'SUBMIT';
        $new_cuti->diketatasan = $jabatasan;
        $new_cuti->statdiket = 'SUBMIT';
        $new_cuti->save();

        return redirect()->route('dirbis.permohonancuti')->with('status','Permohonan Berhasil Diinput');
    }
    public function tolakcuti(){
        $user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($user){
                            $query->where('id','$user');})
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
        
        return view('dirbis.tolakcuti',['orderc'=>$data]);

    }
     public function setujucuti(){
        $user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($user){
                            $query->where('id','$user');})
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
        
        return view('dirbis.setujucuti',['orderc'=>$data]);

    }
    public function cutiindex(Request $request){

        $idcabang = \Auth::user()->cabang;
        $idpeg = \Auth::user()->pegawai_id;
        $peg = \App\pegawai::where('id',$idpeg)->first();
        $jabpeg = $peg->jabatan;
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where([
                            ['status',"LIKE","SUBMIT"],
                            ['statasan',"like","SUBMIT"],
                            ['otoatasan',$jabpeg],
                            //['cabang',$idcabang]
                          ])
                        ->orwhere([
                            ['status',"LIKE","SUBMIT"],
                            ['statasan',"like","DISETUJUI"],
                            ['diketatasan',$jabpeg],
                            //['cabang',$idcabang]
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
        
        return view('dirbis.cutiindex',['orderc'=>$data]);
    
    }
    public function setuju($id){
        $ordercuti = \App\ordercuti::findorFail($id);
        $pegawai = \App\pegawai::where('id',$ordercuti['pegawai_id'])->first();

        $ambilcuti = $ordercuti->jmlcuti;
        $scuti = $pegawai->scuti;
        $sisacuti = $scuti-$ambilcuti;
        $ordercuti->status = 'DISETUJUI';
        $ordercuti->statdiket = 'DISETUJUI';

        $pegawai->scuti = $sisacuti;


        $ordercuti->save();
        $pegawai->save();
        return redirect()->route('dirbis.cutiindex')->with('status','Data Cuti Successfully Updated');

    }
    public function tolak($id){
        $ordercuti = \App\ordercuti::findorFail($id);
        $pegawai = \App\pegawai::where('id',$ordercuti['pegawai_id'])->first();

        //$ambilcuti = $ordercuti->jmlcuti;
       // $scuti = $pegawai->scuti;
        //$sisacuti = $scuti-$ambilcuti;
        $ordercuti->status = 'DITOLAK';
        $ordercuti->statdiket='DITOLAK';
        //$pegawai->scuti = $sisacuti;


        $ordercuti->save();
        //$pegawai->save();
        return redirect()->route('dirbis.cutiindex')->with('status','Data Cuti Successfully Updated');
    }
    public function cutisetuju(Request $request){
        $idcabang = \Auth::user()->cabang;
        $idpeg = \Auth::user()->pegawai_id;
        $peg = \App\pegawai::where('id',$idpeg)->first();
        $jabpeg = $peg->jabatan;
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DISETUJUI")
                        ->where('diketatasan',$jabpeg)
                        ->where('cabang',$idcabang)

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
        
        return view('dirbis.cutisetuju',['orderc'=>$data]);
    }
    public function cutitolak (Request $request){
        $idcabang = \Auth::user()->cabang;
        $idpeg = \Auth::user()->pegawai_id;
        $peg = \App\pegawai::where('id',$idpeg)->first();
        $jabpeg = $peg->jabatan;
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::with('pegawai','cabang')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DITOLAK")
                        ->where('diketatasan',$jabpeg)
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
        
        return view('dirbis.cutitolak',['orderc'=>$data]);
    }
   
     public function peraturan(Request $request){
        $peraturan = \App\peraturan::paginate(10);
        $filterkeyword = $request->get('name');


        if($filterkeyword){
            $peraturan = \App\peraturan::where("name", "LIKE", "%$filterkeyword%")->paginate(10);
        }
        
        
        return view('dirbis.peraturan',['peraturan'=>$peraturan]);
    }
    public function permohonandownload($id){
        $peraturan = \App\peraturan::findorfail($id);
        $idpeg=\Auth::user()->pegawai_id;
        $uscab = \Auth::user()->cabang;
        $pegawai = \App\pegawai::where('id',$idpeg)->first();
        $cab = \App\cabang::where('id',$uscab)->first();

        return view('dirbis.permohonandownload',['peraturan'=>$peraturan,'pegawai'=>$pegawai,'cabang'=>$cab]);
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
       return redirect()->route('dirbis.peraturan')->with('status','Permintaan akan diproses');
   }
   
    public function statusatur(){
        $user = \Auth::user()->id;
        $orderatur = \App\orderatur::where('user_id',$user)->get();

        $data=[];
        foreach ($orderatur as $order) {
        $pegawai = \App\pegawai::where('id',$order['pegawai_id'])->first();
        $namapeg = $pegawai['name'];
        $peraturan = \App\peraturan::where('id',$order['peraturan_id'])->first();
        $namaper = $peraturan['name'];
        $tglsk = $peraturan['tglsk'];
        $nosk = $peraturan['nosk'];

        $data[]=[
          'idatur' => $order['peraturan_id'],
          'name' => $order['name'],
          'nosk' => $nosk,
          'tglsk'=> $tglsk,
          'namepeg'=> $namapeg,
          'namepr' => $namaper,
          'ket' => $order['ket'],
          'status'=>$order['status'],
          'tglminta'=> $order['created_at']
        ];
    }
        return view ('dirbis.statusatur',['orderatur' => $data]);
        
   }
   public function showatur($id)
    {
        $peraturan = \App\peraturan::findorFail($id);
        $time = \Carbon\Carbon::now()->translatedFormat('d/m-Y');

        //$pdf = PDF::loadview('peraturan.show',['peraturan'=>$peraturan]);
        //return $pdf->stream();
        //exit(0);

        return view('dirbis.showatur',['peraturan'=>$peraturan,'time'=>$time]);
    }
    public function show_pdf($id)
    {
        $peraturan = \App\peraturan::findorFail($id);
        $time = \Carbon\Carbon::now()->translatedFormat('d/m-Y');

        //$pdf = PDF::loadview('peraturan.show',['peraturan'=>$peraturan]);
        //return $pdf->stream();
        //exit(0);

        return view('dirbis.show_pdf',['peraturan'=>$peraturan,'time'=>$time]);
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

      return view ('dirbis.pegawairotasi',['pegawai'=>$data]);
    }
    public function mintarotasi($id){
      $peg = \App\pegawai::findorfail($id);
      $pegjab = $peg->jabatan;
      $jab = \App\jabatan::where('id',$pegjab)->first();
      $jabatan = $jab->name;
      $jabrot = \App\Jabatan::where('kantor','cabang')->where('name','LIKE','%staff%')->pluck('name','id');
      //$jabkan = $jabrot->name;

      return view ('dirbis.permohonanrotasi',['pegawai'=>$peg,'jabatan'=>$jabatan,'jabrot'=>$jabrot]);
    }
    public function inputrotasi(Request $request){
        $idpeg = $request->get('idpeg');
        $peg = \App\pegawai::where('id',$idpeg)->first();

        $rotasi = new \App\mutasi;
        $rotasi->pegawai_id = $idpeg;
        $rotasi->cabang = $peg->cabang;
        $rotasi->jabatan = $request->get('jabatan');
        $rotasi->jenis = 'ROTASI';
        $rotasi->created_by = \Auth::user()->id;
        $rotasi->save();

        return redirect()->route('dirbis.pegawairotasi')->with('status','Permohonan Sudah Dinput');

    }

    public function datarotasi(Request $request){
      //public function rotasiindex(Request $request){
        $cabang = \Auth::user()->cabang;
        $name = $request->get('name');
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        //->where("status","like","SUBMIT")
                        ->where([
                                ['status','SUBMIT'],
                                ['cabang',$cabang],
                                ['jenis','Rotasi'],
                                ['dirbis','SUBMIT']
                               
                              ])
                        ->orwhere([
                                    ['status','SUBMIT'],
                                    ['jenis','Mutasi'],
                                    ['dirbis','SUBMIT']
                                    

                                ])
                        ->orwhere([
                                    ['status','SUBMIT'],
                                    ['jenis','Promosi'],
                                    ['dirbis','SUBMIT']
                                  ])
                        ->orwhere([
                                    ['status','SUBMIT'],
                                    ['jenis','Demosi'],
                                    ['dirbis','SUBMIT']])
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
        return view ('dirbis.rotasiindex',['mutasi'=>$data]);
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
        return view ('dirbis.setujurotasi',['mutasi'=>$data]);
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
        return view ('dirbis.tolakrotasi',['mutasi'=>$data]);
    }
    public function rotasisetuju($id){
        $mutasi = \App\mutasi::findorFail($id);
        $pegawai = \App\Pegawai::where('id',$mutasi['pegawai_id'])->first();
        $jabatan = \App\Jabatan::where('id',$mutasi['jabatan'])->first();
        $atasan = $jabatan->atasan;
        $cabang = \App\Cabang::where('id',$mutasi['cabang'])->first();
        $riwayatkerja = new \App\riwayatkerja;
        $now= \Carbon\Carbon::now()->format('Y');

        $mutasi->dirbis = 'DISETUJUI';

        $mutasi->save();
        //$riwayatkerja->save();
        //$pegawai->save();

        return redirect()->route('dirbis.rotasiindex')->with('status','Data Rotasi Disetujui');

    }
    public function rotasitolak($id){
        $mutasi = \App\mutasi::findorFail($id);
        $mutasi->dirbis = 'DITOLAK';
        $mutasi->save();

        return redirect()->route('dirbis.rotasiindex')->with('status','Data Rotasi Ditolak');

    }
    public function datamutasi(Request $request){
      //public function rotasiindex(Request $request){
        $cabang = \Auth::user()->cabang;
        $name = $request->get('name');
        $mutasi = \App\mutasi::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","SUBMIT")
                        ->where('jenis','Mutasi')
                        ->orwhere('jenis','Promosi')
                        ->orwhere('jenis','Demosi')
                        ->orwhere('jenis','Rotasi')
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
        return view ('dirbis.rotasiindex',['mutasi'=>$data]);
    }
    public function mutasipangkat(Request $request)
      {
        $name = $request->get('name');
        $mutasipangkat = \App\mutasipangkat::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where([['status','SUBMIT'],
                                ['dirbis','SUBMIT'],
                      ])->get();
        $data=[];
        $now= \Carbon\Carbon::now()->format('Y-m-d');
        
        foreach ($mutasipangkat as $mp) {
        $pegawai = \App\Pegawai::where('id',$mp['pegawai_id'])->first();
        $namapeg= $pegawai->name;
        $idpeg = $pegawai->id;
        $masuk = \Carbon\Carbon::parse($pegawai['tglmasuk']);
        $mkerja =$masuk->diffinYears($now);
        
        $pangsb = \App\Pangkat::where('id',$pegawai['pangkat'])->first();
        $pangkatseb = $pangsb->name;
        $pangm = \App\Pangkat::where('id',$mp['pangkat'])->first();
        $pangkat = $pangm->name;

        $data[]=[
            "id" => $mp['id'], 
            "pegawai_id" => $idpeg,
            "namapeg" => $namapeg,
            "mkerja" => $mkerja,
            "pangseb" => $pangkatseb,
            "pangkat" => $pangkat,
            "jenis" => $mp['jenis']
        ];
        }

        
      return view ('dirbis.mutasipangkat',['mutasipangkat'=>$data]);
    }
    public function setujupangkat($id){

        $mpangkat = \App\mutasipangkat::findorFail($id);
        $peg = \App\Pegawai::where('id',$mpangkat['pegawai_id'])->first();

        $mpangkat->dirbis = 'SETUJU';
        

        $mpangkat->save();
        

        return redirect()->route('dirbis.mutasipangkat')->with('status','Permohonan Mutasi Pangkat Berhasil Disetujui');
    }
    public function tolakpangkat($id){
        $mpangkat = \App\mutasipangkat::findorFail($id);
        $mpangkat->dirbis = 'TOLAK';
        $mpangkat->save();
        return redirect()->route('dirbis.mutasipangkat')->with('status','Permohonan Mutasi Pangkat Ditolak');

    }
    public function pangkatsetuju(Request $request){
        $name = $request->get('name');
        $mutasipangkat = \App\mutasipangkat::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DISETUJUI")->get();
        $data=[];
        $now= \Carbon\Carbon::now()->format('Y-m-d');
        
        foreach ($mutasipangkat as $mp) {
        $pegawai = \App\Pegawai::where('id',$mp['pegawai_id'])->first();
        $namapeg= $pegawai->name;
        $idpeg = $pegawai->id;
        $masuk = \Carbon\Carbon::parse($pegawai['tglmasuk']);
        $mkerja =$masuk->diffinYears($now);
        
        $pangsb = \App\Pangkat::where('id',$pegawai['pangkat'])->first();
        $pangkatseb = $pangsb->name;
        $pangm = \App\Pangkat::where('id',$mp['pangkat'])->first();
        $pangkat = $pangm->name;

        $data[]=[
            "id" => $mp['id'], 
            "pegawai_id" => $idpeg,
            "namapeg" => $namapeg,
            "mkerja" => $mkerja,
            "pangseb" => $pangkatseb,
            "pangkat" => $pangkat,
            "jenis" => $mp['jenis']
        ];
        }
        return view('dirbis.pangkatsetuju',['mutasipangkat'=>$data]);

    }
    public function pangkattolak(Request $request)
    {
        $name = $request->get('name');
        $mutasipangkat = \App\mutasipangkat::with('pegawai')
                        ->wherehas('pegawai', function($query) use ($name){
                            $query->where('name','LIKE',"%$name%");})
                        ->where("status","like","DITOLAK")->get();
        $data=[];
        $now= \Carbon\Carbon::now()->format('Y-m-d');
        
        foreach ($mutasipangkat as $mp) {
        $pegawai = \App\Pegawai::where('id',$mp['pegawai_id'])->first();
        $namapeg= $pegawai->name;
        $idpeg = $pegawai->id;
        $masuk = \Carbon\Carbon::parse($pegawai['tglmasuk']);
        $mkerja =$masuk->diffinYears($now);
        
        $pangsb = \App\Pangkat::where('id',$pegawai['pangkat'])->first();
        $pangkatseb = $pangsb->name;
        $pangm = \App\Pangkat::where('id',$mp['pangkat'])->first();
        $pangkat = $pangm->name;

        $data[]=[
            "id" => $mp['id'], 
            "pegawai_id" => $idpeg,
            "namapeg" => $namapeg,
            "mkerja" => $mkerja,
            "pangseb" => $pangkatseb,
            "pangkat" => $pangkat,
            "jenis" => $mp['jenis']
        ];
        }
        return view('dirbis.pangkattolak',['mutasipangkat'=>$data]);
    }
}
