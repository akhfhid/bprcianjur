<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class gajiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!\Gate::allows('ADMIN') && !\Gate::allows('ADMIN_SDM')) {
                abort(403, 'Unauthorized Action');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $new_gaji = new \App\gaji;

        $pegawai = $request->get('idpeg');
        $bpjsks = $request->get('bpjsks');
        $bpjstk = $request->get('bpjstk');
        $jabatan = $request->get('jabatan');
        $pph = $request->get('pph21');
        $fungsi = $request->get('fungsi');

        $new_gaji->idpeg = $pegawai;
        $new_gaji->bpjsks = $bpjsks;
        $new_gaji->bpjstk = $bpjstk;
        $new_gaji->jabatan = $jabatan;
        $new_gaji->pph = $pph;
        $new_gaji->fungsi = $fungsi;
        $new_gaji->created_by = \Auth::user()->id;
        $new_gaji->save();

        // Update pangkat and mkpang on pegawais table
        $peg = \App\Pegawai::findOrFail($pegawai);
        if ($request->has('pangkat')) {
            $peg->pangkat = $request->get('pangkat');
        }
        if ($request->has('mkpang')) {
            $peg->mkpang = $request->get('mkpang');
        }
        $peg->save();

        return redirect()->route('gaji.list',$pegawai)->with('status','Data Tunjangan Berhasil Ditambahkan');
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
        $gaji = \App\gaji::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$gaji['idpeg'])->first();

        $pangkats = \App\Pangkat::all();
        $berkalas = \App\berkala::all();
        
        $cabang = \App\Cabang::where('id', $pegawai['cabang'])->first();
        $jabatan = \App\Jabatan::where('id', $pegawai['jabatan'])->first();
        if (!empty($pegawai->is_custom_tuncab)) {
            $tunkin = ['tunjangan' => (float)$pegawai->custom_tuncab_val];
        } else {
            $tunkin = \App\Cabang::where('id', $pegawai['tuncab'])->first();
        }
        $spegawai = \App\statuspeg::where('id', $pegawai['spegawai'])->first();
        
        $anak = \App\keluarga::where('pegawai_id', $pegawai['id'])->where('hubungan', 'Anak')->get();
        $jumlahanak = count($anak);

        $nikah = \App\Keluarga::where([['pegawai_id', $pegawai['id']], ['hubungan', 'Istri']])
            ->orwhere([['pegawai_id', $pegawai['id']], ['hubungan', 'Suami']])
            ->get();
        $jumlahnikah = count($nikah);

        return view ('gaji.edit',[
            'gaji' => $gaji,
            'pegawai' => $pegawai,
            'pangkats' => $pangkats,
            'berkalas' => $berkalas,
            'cabang' => $cabang,
            'jabatan' => $jabatan,
            'tunkin' => $tunkin,
            'spegawai' => $spegawai,
            'jumlahanak' => $jumlahanak,
            'jumlahnikah' => $jumlahnikah,
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
        $gaji =\App\gaji::findOrFail($id);
        $pegawai = $request->get('idpeg');
        $bpjsks = $request->get('bpjsks');
        $bpjstk = $request->get('bpjstk');
        $jabatan = $request->get('jabatan');
        $pph = $request->get('pph21');
        $fungsi = $request->get('fungsi');
        
        $gaji->idpeg = $pegawai;
        $gaji->bpjsks = $bpjsks;
        $gaji->bpjstk = $bpjstk;
        $gaji->jabatan = $jabatan;
        $gaji->pph = $pph;
        $gaji->fungsi = $fungsi;
        $gaji->created_by = \Auth::user()->id;
        $gaji->save();

        // Update pangkat and mkpang on pegawais table
        $peg = \App\Pegawai::findOrFail($pegawai);
        if ($request->has('pangkat')) {
            $peg->pangkat = $request->get('pangkat');
        }
        if ($request->has('mkpang')) {
            $peg->mkpang = $request->get('mkpang');
        }
        $peg->save();

        return redirect()->route('gaji.list',$pegawai)->with('status','Data Tunjangan Berhasil Diperbaharui');
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

    public function tambah($id)
    {
        $pegawai = \App\Pegawai::findOrFail($id);
        $pangkats = \App\Pangkat::all();
        $berkalas = \App\berkala::all();
        
        $cabang = \App\Cabang::where('id', $pegawai['cabang'])->first();
        $jabatan = \App\Jabatan::where('id', $pegawai['jabatan'])->first();
        if (!empty($pegawai->is_custom_tuncab)) {
            $tunkin = ['tunjangan' => (float)$pegawai->custom_tuncab_val];
        } else {
            $tunkin = \App\Cabang::where('id', $pegawai['tuncab'])->first();
        }
        $spegawai = \App\statuspeg::where('id', $pegawai['spegawai'])->first();
        
        $anak = \App\keluarga::where('pegawai_id', $pegawai['id'])->where('hubungan', 'Anak')->get();
        $jumlahanak = count($anak);

        $nikah = \App\Keluarga::where([['pegawai_id', $pegawai['id']], ['hubungan', 'Istri']])
            ->orwhere([['pegawai_id', $pegawai['id']], ['hubungan', 'Suami']])
            ->get();
        $jumlahnikah = count($nikah);

        return view('gaji.create',[
            'pegawai' => $pegawai,
            'pangkats' => $pangkats,
            'berkalas' => $berkalas,
            'cabang' => $cabang,
            'jabatan' => $jabatan,
            'tunkin' => $tunkin,
            'spegawai' => $spegawai,
            'jumlahanak' => $jumlahanak,
            'jumlahnikah' => $jumlahnikah,
        ]);
    }
    public function list($id){
        $pegawai = \App\Pegawai::findorfail($id);
        $gaji = \App\gaji::where('idpeg',[$pegawai['id']])->get();
        return view('gaji.index',['pegawai'=>$pegawai, 'gaji'=>$gaji]);
    }
    public function deletePermanent($id){
        $gaji= \App\gaji::findOrFail($id);
        $pegawai = \App\Pegawai::where('id',$gaji['idpeg'])->first();
        $gaji->forcedelete();
        return redirect()->route('gaji.list',$pegawai)->with('status','Data Tunjangan Berhasil Dihapus');
    }
}
