<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
class ordercutiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $query = \App\ordercuti::with(['pegawai', 'cabang']);
        if ($request->filled('name')) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%');
            });
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tglawal', $request->tanggal);
        }

        if ($request->filled('jenis')) {
            $query->where('jeniscuti', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $ordercuti = $query->orderBy('created_at', 'DESC')->paginate(10);
        $data = [];
        foreach ($ordercuti as $cuti) {
            $data[] = [
                'id' => $cuti->id,
                'namapeg' => $cuti->pegawai->name ?? '-',
                'namacab' => $cuti->cabang->name ?? '-',
                'tglmohon' => $cuti->created_at,
                'jmlcuti' => $cuti->jmlcuti,
                'tglawal' => $cuti->tglawal,
                'tglakhir' => $cuti->tglakhir,
                'alasan' => $cuti->alasan,
                'jenis' => $cuti->jeniscuti,
                'status' => $cuti->status,
                'otosdm' => $cuti->otosdm,
                'statdiket' => $cuti->statdiket,
            ];
        }

        return view('ordercuti.indexcuti', [
            'orderc' => $data,
            'ordercuti' => $ordercuti,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id', $user)->first();

        return view('ordercuti.create', ['pegawai' => $peg]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $jeniscuti = $request->get('jeniscuti');
        $awal = $request->get('tglawal');
        $akhir = $request->get('tglakhir');

        $awlc = \Carbon\Carbon::parse($awal);
        $akhirc = \Carbon\Carbon::parse($akhir);
        $jmlcuti = $awlc->diffinDays($akhirc);
        $user_id = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id', $user_id)->first();
        $jabpeg = $peg->jabatan;
        $jabatan = \App\Jabatan::where('id', $jabpeg)->first();
        $jabatasan = $jabatan->atasan;
        $jabket = \App\jabatan::where('id', $jabatasan)->first();
        $jabketat = $jabket->atasan;
        $useradm = \App\User::where('roles', 'ADMIN')->first();
        $adm = $useradm->pegawai_id;

        if ($jeniscuti == 'Cuti Wajib') {
            $new_cuti = new \App\ordercuti();
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jmlcuti = $jmlcuti;
            $new_cuti->tglawal = $awal;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->status = 'SUBMIT';
            $new_cuti->otoatasan = $jabatasan;
            $new_cuti->statasan = 'SUBMIT';
            $new_cuti->diketatasan = $jabketat;
            $new_cuti->statdiket = 'SUBMIT';
            $new_cuti->otosdm = 'ADMIN';
            $new_cuti->statsdm = 'SUBMIT';
        } elseif ($jeniscuti == 'Cuti Lainnya') {
            $new_cuti = new \App\ordercuti();
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jmlcuti = $jmlcuti;
            $new_cuti->tglawal = $awal;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->status = 'SUBMIT';
            $new_cuti->otoatasan = $jabatasan;
            $new_cuti->statasan = 'SUBMIT';
            $new_cuti->diketatasan = $jabketat;
            $new_cuti->statdiket = 'SUBMIT';
        } else {
            $new_cuti = new \App\ordercuti();
            $new_cuti->user_id = \Auth::user()->id;
            $new_cuti->cabang = \Auth::user()->cabang;
            $new_cuti->pegawai_id = $request->get('idpeg');
            $new_cuti->jmlcuti = $jmlcuti;
            $new_cuti->tglawal = $awal;
            $new_cuti->tglakhir = $akhir;
            $new_cuti->alasan = $request->get('alasan');
            $new_cuti->jeniscuti = $jeniscuti;
            $new_cuti->status = 'SUBMIT';
            $new_cuti->otoatasan = $jabatasan;
            $new_cuti->statasan = 'SUBMIT';
            $new_cuti->diketatasan = $jabketat;
            $new_cuti->statdiket = 'SUBMIT';
        }

        $new_cuti->save();

        return redirect()->route('ordercuti.index')->with('status', 'Permohonan Berhasil Diinput');
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
        $ordercuti = \App\ordercuti::findorFail($id);
        $user = $ordercuti->pegawai_id;
        $peg = \App\Pegawai::where('id', $user)->first();

        return view('ordercuti.editcutiwajib', ['pegawai' => $peg, 'ordercuti' => $ordercuti]);
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
        $ordercuti = \App\ordercuti::findorFail($id);
        $jeniscuti = $request->get('jeniscuti');
        $awal = $request->get('tglawal');
        $akhir = $request->get('tglakhir');

        $awlc = \Carbon\Carbon::parse($awal);
        $akhirc = \Carbon\Carbon::parse($akhir);
        $jmlcuti = $awlc->diffinDays($akhirc);
        $user_id = \Auth::user()->pegawai_id;

        $useradm = \App\User::where('roles', 'ADMIN')->first();
        $adm = $useradm->pegawai_id;

        $ordercuti->tglawal = $awal;
        $ordercuti->tglakhir = $akhir;
        $ordercuti->jmlcuti = 3;
        $ordercuti->save();
        return redirect()->route('ordercuti.index')->with('status', 'Permohonan Berhasil diperbaharui');
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

    public function setuju($id)
    {
        $ordercuti = \App\ordercuti::findorFail($id);
        $pegawai = \App\Pegawai::where('id', $ordercuti['pegawai_id'])->first();

        $ambilcuti = $ordercuti->jmlcuti;
        $scuti = $pegawai->scuti;
        $sisacuti = $scuti - $ambilcuti;
        $ordercuti->status = 'DISETUJUI';
        $pegawai->scuti = $sisacuti;

        $ordercuti->save();
        $pegawai->save();
        return redirect()->route('ordercuti.index')->with('status', 'Data Cuti Successfully Updated');
    }
    public function tolak($id)
    {
        $ordercuti = \App\ordercuti::findorFail($id);
        $pegawai = \App\Pegawai::where('id', $ordercuti['pegawai_id'])->first();

        //$ambilcuti = $ordercuti->jmlcuti;
        // $scuti = $pegawai->scuti;
        //$sisacuti = $scuti-$ambilcuti;
        $ordercuti->status = 'DITOLAK';
        //$pegawai->scuti = $sisacuti;

        $ordercuti->save();
        //$pegawai->save();
        return redirect()->route('ordercuti.index')->with('status', 'Data Cuti Successfully Updated');
    }
    public function disetujui(Request $request)
    {
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
            ->wherehas('pegawai', function ($query) use ($name) {
                $query->where('name', 'LIKE', "%$name%");
            })
            ->where('status', 'like', 'DISETUJUI')
            ->get();
        $data = [];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                'id' => $cuti['id'],
                'namapeg' => $namapeg,
                'tglmohon' => $cuti['created_at'],
                'jmlcuti' => $cuti['jmlcuti'],
                'tglawal' => $cuti['tglawal'],
                'tglakhir' => $cuti['tglakhir'],
                'alasan' => $cuti['alasan'],
                'namacab' => $namacab,
                'status' => $cuti['status'],
            ];
        }

        return view('ordercuti.datasetuju', ['orderc' => $data]);
    }

    public function ditolak(Request $request)
    {
        $name = $request->get('name');
        $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
            ->wherehas('pegawai', function ($query) use ($name) {
                $query->where('name', 'LIKE', "%$name%");
            })
            ->where('status', 'like', 'DITOLAK')
            ->get();
        $data = [];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                'id' => $cuti['id'],
                'namapeg' => $namapeg,
                'tglmohon' => $cuti['created_at'],
                'jmlcuti' => $cuti['jmlcuti'],
                'tglawal' => $cuti['tglawal'],
                'tglakhir' => $cuti['tglakhir'],
                'alasan' => $cuti['alasan'],
                'namacab' => $namacab,
                'status' => $cuti['status'],
            ];
        }

        return view('ordercuti.datatolak', ['orderc' => $data]);
    }
    public function cutiwajib()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id', $user)->first();

        return view('ordercuti.cutiwajib', ['pegawai' => $peg]);
    }
    public function cutilainnya()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id', $user)->first();

        return view('ordercuti.cutilainnya', ['pegawai' => $peg]);
    }
    public function indexcutiwajib(Request $request)
    {
        $role = \Auth::user()->roles;
        $name = $request->get('name');
        $date = $request->get('tanggal');
        if ($name) {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
                ->wherehas('pegawai', function ($query) use ($name) {
                    $query->where('name', 'LIKE', "%$name%");
                })
                ->where('otosdm', 'LIKE', "$role")
                ->orderby('status', 'DESC')
                ->paginate(10);
        } elseif ($date) {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')

                ->where('otosdm', 'LIKE', "$role")
                ->where('tglawal', 'LIKE', "%$date%")
                ->orderby('id', 'DESC')
                ->paginate(10);
        } else {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')

                ->where('otosdm', 'LIKE', "$role")
                ->orderby('id', 'DESC')
                ->paginate(10);
        }
        $data = [];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                'id' => $cuti['id'],
                'namapeg' => $namapeg,
                'tglmohon' => $cuti['created_at'],
                'jmlcuti' => $cuti['jmlcuti'],
                'tglawal' => $cuti['tglawal'],
                'tglakhir' => $cuti['tglakhir'],
                'alasan' => $cuti['alasan'],
                'jenis' => $cuti['jeniscuti'],
                'namacab' => $namacab,
                'status' => $cuti['status'],
                'otosdm' => $cuti['otosdm'],
                'statdiket' => $cuti['statdiket'],
            ];
        }

        return view('ordercuti.indexcutiwajib', ['orderc' => $data, 'indexcuti' => $ordercuti]);
    }
    public function indexcuti(Request $request)
    {
        $name = $request->get('name');
        $date = $request->get('tanggal');
        $id_user = \Auth::user()->pegawai_id;
        if ($name) {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
                ->wherehas('pegawai', function ($query) use ($name) {
                    $query->where('name', 'LIKE', "%$name%");
                })

                ->paginate(10);
        } elseif ($date) {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
                ->where('tglawal', 'LIKE', "%$date%")
                ->peginate(10);
        } else {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
            ->paginate(10);
        }

        $data = [];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                'id' => $cuti['id'],
                'namapeg' => $namapeg,
                'tglmohon' => $cuti['created_at'],
                'jmlcuti' => $cuti['jmlcuti'],
                'tglawal' => $cuti['tglawal'],
                'tglakhir' => $cuti['tglakhir'],
                'alasan' => $cuti['alasan'],
                'jenis' => $cuti['jeniscuti'],
                'namacab' => $namacab,
                'status' => $cuti['status'],
                'otosdm' => $cuti['otosdm'],
                'statdiket' => $cuti['statdiket'],
            ];
        }

        return view('ordercuti.indexcuti', ['orderc' => $data, 'ordercuti' => $ordercuti]);
    }
    public function indexcutilainnya(Request $request)
    {
        $name = $request->get('name');
        $date = $request->get('tanggal');

        if ($name) {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
                ->wherehas('pegawai', function ($query) use ($name) {
                    $query->where('name', 'LIKE', "%$name%");
                })
                ->where('jeniscuti', 'LIKE', 'Cuti Lainnya')
                ->paginate(10);
        } elseif ($date) {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
                ->where('tglawal', 'LIKE', "%$date%")
                ->where('jeniscuti', 'LIKE', 'Cuti Lainnya')
                ->paginate(10);
        } else {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')->where('jeniscuti', 'LIKE', 'Cuti Lainnya')->paginate(10);
        }
        $data = [];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                'id' => $cuti['id'],
                'namapeg' => $namapeg,
                'tglmohon' => $cuti['created_at'],
                'jmlcuti' => $cuti['jmlcuti'],
                'tglawal' => $cuti['tglawal'],
                'tglakhir' => $cuti['tglakhir'],
                'alasan' => $cuti['alasan'],
                'jenis' => $cuti['jeniscuti'],
                'namacab' => $namacab,
                'status' => $cuti['status'],
            ];
        }

        return view('ordercuti.indexcutilainnya', ['orderc' => $data, 'ordercuti' => $ordercuti]);
    }
    public function indexcutitahunan(Request $request)
    {
        $name = $request->get('name');
        $date = $request->get('tanggal');
        if ($name) {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
                ->wherehas('pegawai', function ($query) use ($name) {
                    $query->where('name', 'LIKE', "%$name%");
                })
                ->where('jeniscuti', 'LIKE', 'Cuti Tahunan')
                ->paginate(10);
        } elseif ($date) {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
                ->where('tglawal', 'LIKE', "%$date%")
                ->where('jeniscuti', 'LIKE', 'Cuti Tahunan')
                ->paginate(10);
        } else {
            $ordercuti = \App\ordercuti::with('pegawai', 'cabang')->where('jeniscuti', 'LIKE', 'Cuti Tahunan')->paginate(10);
        }

        $data = [];
        foreach ($ordercuti as $cuti) {
            //$order=\App\ordercuti::where('status','SUBMIT');
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'];

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'];

            //$jmlcuti = $pegawai->scuti;
            //$pcuti = $cuti->jmlcuti;
            //$sisacuti = $jmlcuti-$pcuti;

            $data[] = [
                'id' => $cuti['id'],
                'namapeg' => $namapeg,
                'tglmohon' => $cuti['created_at'],
                'jmlcuti' => $cuti['jmlcuti'],
                'tglawal' => $cuti['tglawal'],
                'tglakhir' => $cuti['tglakhir'],
                'alasan' => $cuti['alasan'],
                'jenis' => $cuti['jeniscuti'],
                'namacab' => $namacab,
                'status' => $cuti['status'],
            ];
        }

        return view('ordercuti.indexcutitahunan', ['orderc' => $data, 'ordercuti' => $ordercuti]);
    }
}
