<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ordercuti;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function index()
    {
        $cutis = ordercuti::with(['user', 'pegawai', 'cabang'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('cuti.index', compact('cutis'));
    }

    public function show($id)
    {
        $cuti = ordercuti::with(['user', 'pegawai', 'cabang'])->findOrFail($id);

        return view('cuti.show', compact('cuti'));
    }

    public function edit($id)
    {
        $cuti = ordercuti::with(['user', 'pegawai'])->findOrFail($id);

        return view('cuti.edit', compact('cuti'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tglawal'  => 'required|date',
            'tglakhir' => 'required|date|after_or_equal:tglawal',
            'alasan'   => 'required|string',
        ]);

        $cuti = ordercuti::findOrFail($id);

        $awal  = Carbon::parse($request->tglawal);
        $akhir = Carbon::parse($request->tglakhir);

        $cuti->tglawal  = $request->tglawal;
        $cuti->tglakhir = $request->tglakhir;
        $cuti->jmlcuti  = $awal->diffInDays($akhir) + 1;
        $cuti->alasan   = $request->alasan;

        $cuti->save();

        return redirect()
            ->route('cuti.index')
            ->with('status', 'Data cuti berhasil diperbarui');
    }
}
