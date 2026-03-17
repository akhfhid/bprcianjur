<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\OrderCutiNotificationController;
use Illuminate\Support\Facades\DB; 
use DataTables;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (gate::allows('USER')) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki hak akses');
        });
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function profile()
    {
        $iduser = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id', $iduser)->first();
        
        if (!$pegawai) return redirect()->back()->with('error', 'Data pegawai tidak ditemukan');

        $cabang = \App\Cabang::where('id', $pegawai['cabang'])->first();
        $kelamin = \App\Jenkel::where('id', $pegawai['kelamin'])->first();
        $jabatan = \App\Jabatan::where('id', $pegawai['jabatan'])->first();
        $agama = \App\Agama::where('id', $pegawai['agama'])->first();
        $kawin = \App\Kawin::where('id', $pegawai['status'])->first();
        $pendidikan = \App\Pendidikan::where('id', $pegawai['pendidikan'])->first();
        $pangkat = \App\Pangkat::where('id', $pegawai['pangkat'])->first();
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

        $tunpang = $jabatan['tunpang'] ?? 0;
        $jmlkeluarga = $jumlahnikah + $jumlahanak;
        $tunis = $jabatan['tunis'] ?? 0;
        
        if ($statpegawai == 1) {
            $pangan = 0;
        } else {
            if ($jmlkeluarga > 3) {
                $pangan = $tunpang * 4;
            } elseif ($jmlkeluarga <= 3) {
                $pangan = $tunpang * ($jumlahnikah + $jumlahanak + 1);
            }
        }

        $tunak = $jabatan['tunak'] ?? 0;
        $tunjab = $gaji['jabatan'] ?? 0;
        if ($statpegawai == 3) {
            $tuncab = $tunkin['tunjangan'] ?? 0;
        } else {
            $tuncab = 0;
        }

        $fungsi = $gaji['fungsi'] ?? 0;
        $gapokpeg = $gapok['gapok'] ?? 0;
        $bpjsks = $gaji['bpjsks'] ?? 0;
        $bpjstk = $gaji['bpjstk'] ?? 0;
        $pensiun = $jabatan['pensiun'] ?? 0;
        
        if ($statpegawai == 3) {
            $tunpen = $pensiun * $gapokpeg;
        } else {
            $tunpen = 0;
        }
        $pph = $gaji['pph'] ?? 0;
        
        if ($statpegawai == 1) {
            $tunjanganistri = 0;
        } else {
            $tunjanganistri = $tunis * $gapokpeg * $jumlahnikah;
        }

        if ($jumlahanak <= 2) {
            $tunjangananak = $tunak * $gapokpeg * $jumlahanak;
        } elseif ($jumlahanak > 2) {
            $tunjangananak = $tunak * $gapokpeg * 2;
        } else {
             $tunjangananak = 0;
        }
        
        if ($statpegawai != 3) {
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
            $statpeg = $statuspeg['name'] ?? '-';

            $dataangkat[] = [
                'id' => $angkat['id'],
                'status' => $statpeg,
                'tglangkat' => $angkat['tglangkat'],
                'nosk' => $angkat['nosk'],
            ];
        }
        
        $riwayatsanksi = \App\riwayatsanksi::where('id_peg', [$pegawai['id']])->paginate(10);
        $datasanksi = [];

        foreach ($riwayatsanksi as $rsanksi) {
            $sanksipegawai = \App\sanksi::where('id', [$rsanksi['sanksi']])->first();
            $sankpeg = $sanksipegawai['name'] ?? '-';

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

        return view('staff.profile', [
            'pegawai' => $pegawai, 'cabang' => $cabang, 'kelamin' => $kelamin, 
            'jabatan' => $jabatan, 'umur' => $umur, 'agama' => $agama, 
            'kawin' => $kawin, 'pendidikan' => $pendidikan, 'pangkat' => $pangkat, 
            'keluarga' => $datakel, 'masakerja' => $mkerja, 'riwayatpendi' => $datapend, 
            'riwayatkerja' => $datakerja, 'tunjanganistri' => $tunjanganistri, 
            'tunjangananak' => $tunjangananak, 'tuncabang' => $tuncabang, 'total' => $total, 
            'pelatihan' => $pelatihan, 'ppensiun' => $ppensiun, 'smkerja' => $smkerja, 
            'spegawai' => $spegawai, 'dataangkat' => $dataangkat, 'datasanksi' => $datasanksi, 
            'bpjstk' => $bpjstk, 'bpjsks' => $bpjsks, 'pensiun' => $pensiun, 'pph' => $pph, 
            'fungsi' => $fungsi, 'gapokpeg' => $gapokpeg, 'gapok' => $gapok, 
            'tglpangkat' => $tglpangkat, 'tglberkala' => $tglberkala, 'tunda' => $tunda, 
            'jdpang' => $jdpang, 'jdber' => $jdber, 'jumlahanak' => $jumlahanak, 
            'pangan' => $pangan, 'tunpen' => $tunpen, 'tunjab' => $tunjab
        ]);
    }

    public function cuti()
    {
        $id_user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::with('pegawai', 'cabang')
            ->wherehas('pegawai', function ($query) use ($id_user) {
                $query->where('id', $id_user);
            })
            ->get();
        $data = [];
        foreach ($ordercuti as $cuti) {
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'] ?? '-';

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'] ?? '-';

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

        return view('staff.cuti', ['orderc' => $data]);
    }

    public function permohonancuti()
    {
        $user = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id', $user)->first();

        return view('staff.permohonancuti', ['pega' => $pegawai]);
    }

    public function mintacuti(Request $request)
    {
        $awal = $request->get('tglawal');
        $akhir = $request->get('tglakhir');
        $jeniscuti = $request->get('jeniscuti');
        $awlc = \Carbon\Carbon::parse($awal);
        $akhirc = \Carbon\Carbon::parse($akhir);
        $jmlcuti = $awlc->diffInDays($akhirc) + 1;
        $user_id = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::findOrFail($user_id);

        $saldoBaru = $peg->scuti; 

        if ($jeniscuti != 'Cuti Wajib') {
            if ($peg->scuti < $jmlcuti) {
                return back()->withErrors('Permohonan Gagal: Sisa cuti Anda tidak mencukupi. Sisa: ' . $peg->scuti);
            }
            $saldoBaru = $peg->scuti - $jmlcuti;
            
            $peg->scuti = $saldoBaru;
            $peg->save();
        }

        $jabatan = \App\Jabatan::where('id', $peg->jabatan)->first();
        $jabatasan = $jabatan->atasan ?? null;
        $jabket = \App\jabatan::where('id', $jabatasan)->first();
        $jabketat = $jabket->atasan ?? null;

        $new_cuti = new \App\ordercuti();
        $new_cuti->user_id = \Auth::user()->id;
        $new_cuti->cabang = \Auth::user()->cabang;
        $new_cuti->pegawai_id = $user_id;
        $new_cuti->jmlcuti = $jmlcuti;
        $new_cuti->tglawal = $awal;
        $new_cuti->tglakhir = $akhir;
        $new_cuti->jeniscuti = $jeniscuti;
        $new_cuti->alasan = $request->get('alasan');

        $new_cuti->otoatasan = $jabatasan;
        $new_cuti->diketatasan = $jabketat;
        $new_cuti->otosdm = 'ADMIN';

        if ($jeniscuti == 'Cuti Wajib') {
            $new_cuti->status = 'DISETUJUI';
            $new_cuti->statasan = 'DISETUJUI';
            $new_cuti->statdiket = 'DISETUJUI';
            $new_cuti->statsdm = 'DISETUJUI';
        } else {
            $new_cuti->status = 'SUBMIT';
            $new_cuti->statasan = 'SUBMIT';
            $new_cuti->statdiket = 'SUBMIT';
            $new_cuti->statsdm = 'SUBMIT';
        }

        $new_cuti->save();
        try {
    $pegawai = \App\Pegawai::find($new_cuti->pegawai_id);

    if ($pegawai) {
        $jabatanPemohon = \App\Jabatan::find($pegawai->jabatan);

        if ($jabatanPemohon && $jabatanPemohon->atasan) {
            $atasan1 = \App\Pegawai::where('jabatan', $jabatanPemohon->atasan)
                ->where('status_active', 1)
                ->first();

            if ($atasan1) {
                \App\Helpers\WhatsAppHelper::sendCutiNotificationAtasan1(
                    $pegawai,
                    $new_cuti,
                    $atasan1
                );
            }
        }
    }
} catch (\Exception $e) {
    \Log::error('Gagal Notif Submit Cuti', array(
        'order_id' => $new_cuti->id,
        'error' => $e->getMessage(),
    ));
}

        return redirect()
            ->route('staff.cuti')
            ->with('status', 'Permohonan Berhasil Diinput. Sisa Cuti Anda: ' . $saldoBaru);
    }

    // public function rejectCuti($id)
    // {
    //     $cuti = \App\ordercuti::findOrFail($id);

    //     if ($cuti->status == 'DITOLAK') {
    //         return back()->with('error', 'Cuti sudah ditolak sebelumnya.');
    //     }

    //     DB::transaction(function () use ($cuti) {
    //         $cuti->status = 'DITOLAK';
    //         $cuti->save();

    //         // Kembalikan saldo jika bukan cuti wajib
    //         if ($cuti->jeniscuti != 'Cuti Wajib') {
    //             $pegawai = \App\Pegawai::find($cuti->pegawai_id);
    //             if ($pegawai) {
    //                 $pegawai->scuti = $pegawai->scuti + $cuti->jmlcuti;
    //                 $pegawai->save();
    //             }
    //         }
    //     });

    //     return back()->with('status', 'Cuti telah ditolak dan saldo dikembalikan ke pegawai.');
    // }

    public function cutisetuju()
    {
        $user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::where([['pegawai_id', $user], ['status', 'like', 'DISETUJUI']])->get();
        $data = [];
        foreach ($ordercuti as $cuti) {
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'] ?? '-';

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'] ?? '-';

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

        return view('staff.cutisetuju', ['orderc' => $data]);
    }

    public function cutitolak()
    {
        $user = \Auth::user()->pegawai_id;
        $ordercuti = \App\ordercuti::where([['pegawai_id', $user], ['status', 'like', 'DITOLAK']])->get();
        $data = [];
        foreach ($ordercuti as $cuti) {
            $pegawai = \App\Pegawai::where('id', $cuti['pegawai_id'])->first();
            $namapeg = $pegawai['name'] ?? '-';

            $cabang = \App\Cabang::where('id', $cuti['cabang'])->first();
            $namacab = $cabang['name'] ?? '-';

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

        return view('staff.cutitolak', ['orderc' => $data]);
    }


    public function peraturan(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\peraturan::query();
            $kategori = $request->get('kategori');
            
            if ($kategori == 'internal') {
                $query->whereIn('jenis_surat', ['SK', 'SE']);
            } elseif ($kategori == 'external') {
                $query->whereIn('jenis_surat', ['OJK', 'LPS']);
            }

            $jenis_surat = $request->get('jenis_surat');
            if ($jenis_surat && $jenis_surat != 'all') {
                $query->where('jenis_surat', $jenis_surat);
            }

            $sub_jenis = $request->get('jenis_ojk');
            if ($sub_jenis && $sub_jenis != 'all') {
                $query->where('jenis_ojk', $sub_jenis); 
            }

            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    $btn = '<div class="action-col">';
                    $btn .= '<a href="' . route('staff.showatur', $data->id) . '" class="action-btn view" title="Detail"><i class="fas fa-eye"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('jenis_ojk', function ($data) {
                    return $data->sub_jenis ?? '-';
                })
                ->editColumn('tglsk', function ($data) {
                    return $data->tglsk ? \Carbon\Carbon::parse($data->tglsk)->format('d/m/Y') : '-';
                })
                ->rawColumns(['action']) 
                ->make(true);
        }

        return view('staff.peraturan');
    }

    public function permohonandownload($id)
    {
        $peraturan = \App\peraturan::findorfail($id);
        $idpeg = \Auth::user()->pegawai_id;
        $uscab = \Auth::user()->cabang;
        $pegawai = \App\Pegawai::where('id', $idpeg)->first();
        $cab = \App\cabang::where('id', $uscab)->first();

        return view('staff.permohonandownload', ['peraturan' => $peraturan, 'pegawai' => $pegawai, 'cabang' => $cab]);
    }

    public function mintadownload(Request $request)
    {
        $orderatur = new \App\orderatur();

        $orderatur->peraturan_id = $request->get('idperaturan');
        $orderatur->pegawai_id = $request->get('idpeg');
        $orderatur->cabang_id = $request->get('cabang');
        $orderatur->ket = $request->get('ket');
        $orderatur->user_id = \Auth::user()->id;
        $orderatur->status = 'SUBMIT';

        $orderatur->save();
        return redirect()->route('staff.peraturan')->with('status', 'Permintaan akan diproses');
    }

    public function statusatur()
    {
        $user = \Auth::user()->id;
        $orderatur = \App\orderatur::where('user_id', $user)->get();

        $data = [];
        foreach ($orderatur as $order) {
            $pegawai = \App\Pegawai::where('id', $order['pegawai_id'])->first();
            $namapeg = $pegawai['name'] ?? '-'; // Handle null

            $peraturan = \App\peraturan::where('id', $order['peraturan_id'])->first();
            
            // Handle jika peraturan sudah dihapus
            if ($peraturan) {
                $namaper = $peraturan['name'];
                $tglsk = $peraturan['tglsk'];
                $nosk = $peraturan['nosk'];
            } else {
                $namaper = 'Dihapus';
                $tglsk = '-';
                $nosk = '-';
            }

            $data[] = [
                'idatur' => $order['peraturan_id'],
                'name' => $order['name'],
                'nosk' => $nosk,
                'tglsk' => $tglsk,
                'namepeg' => $namapeg,
                'namepr' => $namaper,
                'ket' => $order['ket'],
                'status' => $order['status'],
                'tglminta' => $order['created_at'],
            ];
        }
        return view('staff.statusatur', ['orderatur' => $data]);
    }

    public function showatur($id)
    {
        $peraturan = \App\peraturan::findorFail($id);
        $time = \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i:s');
        $new_loguser = new \App\loguser();
        $user = \Auth::user()->pegawai_id;
        $pegawai = \App\Pegawai::where('id', $user)->first();
        
        if($pegawai) {
            $new_loguser->nampeg = $pegawai->name;
            $new_loguser->keterangan = $peraturan->name;
            $new_loguser->save();
        }

        return view('staff.showatur', ['peraturan' => $peraturan, 'time' => $time]);
    }

    public function show_pdf($id)
    {
        $peraturan = \App\peraturan::findorFail($id);
        $time = \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i:s');

        return view('staff.show_pdf', ['peraturan' => $peraturan, 'time' => $time]);
    }

    public function cutiwajib()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id', $user)->first();

        return view('staff.cutiwajib', ['pegawai' => $peg]);
    }

    public function cutilainnya()
    {
        $user = \Auth::user()->pegawai_id;
        $peg = \App\Pegawai::where('id', $user)->first();

        return view('staff.cutilainnya', ['pegawai' => $peg]);
    }
}