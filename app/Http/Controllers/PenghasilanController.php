<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\berkala;

class PenghasilanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct(){
        $this->middleware(function($request, $next){
       if (Gate::allows('ADMIN') || Gate::allows('ADMIN_SDM')|| Gate::allows('STAFF_SDM')) {
                return $next($request);
            }

            abort(403, 'Anda tidak memiliki hak akses');
        });
    }
    public function index(Request $request)
    {
        $cabang = \App\Cabang::pluck("name","id");
        $kantor = $request->get("cabang");
        if($kantor){
        $pegawai=\App\Pegawai::where('cabang',"LIKE",$kantor)->paginate(10);
        }else{
        $pegawai = \App\Pegawai::paginate(10);
        }

      $datapenghasilan=[];
          
        foreach($pegawai as $x){
            $cab = \App\Cabang::where('id',$x['cabang'])->first();
            $jabatan = \App\Jabatan::where('id',$x['jabatan'])->first();
            $gaji = \App\gaji::where('idpeg',$x['id'])->first();
            $pang = \App\Pangkat::where('id',$x['pangkat'])->first();
            $pangkat = $pang['name'];
            $idpang = $pang['id'];
            $gol = \App\berkala::where('idpang',$idpang)->where('gol',$x['mkpang'])->first();
            $gapok = $gol['gapok'];
            //$pangkat= $x['pangkat'];
            //$mkpangpeg = $x['mkpang'];
            //$pokok = \App\berkala::where('idpang',$pangkat)->
                                  //  where('gol',$mkpangpeg)->first();

           
            //$gapokpeg = $pokok['gapok'];

         
            $tunjab = $jabatan["tunjab"];
            $tunis= $jabatan["tunis"];
            $tunak = $jabatan["tunak"];
            $tunpang = $jabatan["tunpang"];
            $umak = $jabatan["umak"];
            $fungsi = $jabatan["fungsi"];
            $tuncab = $cab["tunjangan"];
            $bpjstk = $gaji['bpjstk'];
            $bpjsks = $gaji["bpjsks"];
            $pensiun = $gaji["pensiun"];
            $pph = $gaji["pph"];
            $tunjanganistri = $tunis * $gapok;
            $tunjangananak = $tunak *$gapok;
            $tuncabang = $tuncab*$gapok; 
            $total =$gapok+$tunjanganistri+$tunjangananak+$tunpang+$tunjab+$tuncabang+$bpjstk+$bpjsks+$pensiun+$pph+$fungsi;
            $datapenghasilan[]=[
            "id"=>$x['id'],
            "nama"=> $x['name'],
            "cabang"=> $cab['name'],
            "jabatan"=> $jabatan['name'],
            "pangkat"=>$pangkat,
            "gapok"=>$gapok,
                "tunjab"=>$tunjab,
                "tunis"=>$tunjanganistri,
                "tunak"=>$tunjangananak,
                "tunpang"=>$tunpang,
                "umak"=>$umak,
                "fungsi"=>$fungsi,
               "tuncab"=>$tuncabang,
                "bpjstk"=>$bpjstk,
                "bpjsks"=>$bpjsks,
                "pensiun"=>$pensiun,
               "pph"=>$pph,
                "total"=>$total

            ];
       }
       
        return view("penghasilan.index",["datapenghasilan"=>$datapenghasilan,"pegawai"=>$pegawai,"gaji"=>$gaji]);

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
   

}
