<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class riwayatangkatController extends Controller
{
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
        $status = $request->get('speg');
        $idpeg = $request->get('idpeg');
        $tglangkat = $request->get('tglangkat');
        $nosk = $request->get('nosk');
        $new_riwayatangkat = new \App\riwayatangkat();
        $pegawai = \App\Pegawai::where('id', $idpeg)->first();
        $new_riwayatangkat->status = $status;
        $new_riwayatangkat->tglangkat = $tglangkat;
        $new_riwayatangkat->nosk = $nosk;
        $new_riwayatangkat->pegawai_id = $idpeg;
        $new_riwayatangkat->created_by = \Auth::user()->id;
        if ($status == '2') {
            $pegawai->tglangkat = $tglangkat;
        }
        $pegawai->save();
        $new_riwayatangkat->save();
        return redirect()->route('riwayatangkat.list', $request->get('idpeg'))->with('status', 'data riwayat kepegawaian berhasil');
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
        $riwayatangkat = \App\riwayatangkat::findorfail($id);
        $statuspegawai = \App\statuspeg::pluck('name', 'id');
        $pegawai = \App\Pegawai::where('id', $riwayatangkat['pegawai_id'])->first();
        $spegawai = \App\statuspeg::where('id', [$riwayatangkat['status']])->first();
        $statpeg = $spegawai['name'];

        return view('riwayatangkat.edit', ['riwayatangkat' => $riwayatangkat, 'statuspegawai' => $statuspegawai, 'pegawai' => $pegawai, 'spegawai' => $spegawai, 'statpeg' => $statpeg]);
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
        $status = $request->get('speg');
        $idpeg = $request->get('idpeg');
        $tglangkat = $request->get('tglangkat');
        $nosk = $request->get('nosk');
        $riwayatangkat = \App\riwayatangkat::findorfail($id);
        $pegawai = \App\Pegawai::where('id', $idpeg)->first();
        $riwayatangkat->status = $status;
        $riwayatangkat->tglangkat = $tglangkat;
        $riwayatangkat->nosk = $nosk;
        //$riwayatangkat->pegawai_id = $idpeg;
        $riwayatangkat->created_by = \Auth::user()->id;
        if ($status == '2') {
            $pegawai->tglangkat = $tglangkat;
        }
        $pegawai->save();
        $riwayatangkat->save();
        return redirect()->route('riwayatangkat.list', $request->get('idpeg'))->with('status', 'data riwayat kepegawaian berhasil');
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
    public function list($id)
    {
        $pegawai = \App\Pegawai::findorfail($id);
        $statuspegawai = \App\statuspeg::pluck('name', 'id');
        $riwayatangkat = \App\riwayatangkat::where('pegawai_id', [$pegawai['id']])->paginate(10);
        $dataangkat = [];

        foreach ($riwayatangkat as $angkat) {
            $spegawai = \App\statuspeg::where('id', [$angkat['status']])->first();
            $statpeg = $spegawai['name'];

            $dataangkat[] = [
                'id' => $angkat['id'],
                'status' => $statpeg,
                'tglangkat' => $angkat['tglangkat'],
                'tglmasuk'=> $angkat['tglmasuk'],
                'nosk' => $angkat['nosk'],
            ];
        }

        return view('riwayatangkat.index', ['dataangkat' => $dataangkat, 'pegawai' => $pegawai]);
    }
    public function tambah($id)
    {
        $pegawai = \App\Pegawai::findorfail($id);
        $spegawai = \App\statuspeg::pluck('name', 'id');
        return view('riwayatangkat.create', ['pegawai' => $pegawai, 'spegawai' => $spegawai]);
    }
    public function deletePermanent($id)
    {
        $riwayatangkat = \App\riwayatangkat::findOrFail($id);
        $pegawai = \App\Pegawai::where('id', $riwayatangkat['pegawai_id'])->first();
        $riwayatangkat->forcedelete();
        return redirect()->route('riwayatangkat.list', $pegawai)->with('status', 'Data Pegawai Successfully Deleted');
    }
}
