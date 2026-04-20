<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\peraturan;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $aktif = \Auth::user()->id;
        $user = \App\User::where('id', $aktif)->first();

        $internalJenis = trim((string) request()->get('internal_jenis', ''));
        $externalJenis = trim((string) request()->get('external_jenis', ''));
        $externalSubJenis = trim((string) request()->get('external_sub_jenis', ''));

        $internalPeraturan = peraturan::query()
            ->where(function ($query) {
                $query->where('kategori', 'internal')
                    ->orWhere(function ($fallback) {
                        $fallback->whereNull('kategori')
                            ->whereIn('jenis_surat', ['SK', 'SE']);
                    });
            })
            ->when($internalJenis !== '', function ($query) use ($internalJenis) {
                $query->where('jenis_surat', $internalJenis);
            })
            ->latest()
            ->limit(5) 
            ->get();

        $externalPeraturan = peraturan::query()
            ->where(function ($query) {
                $query->where('kategori', 'external')
                    ->orWhere(function ($fallback) {
                        $fallback->whereNull('kategori')
                            ->whereIn('jenis_surat', ['OJK', 'LPS', 'POJK', 'SEOJK', 'PADK']);
                    });
            })
            ->when($externalJenis !== '', function ($query) use ($externalJenis) {
                $query->where('jenis_surat', $externalJenis);
            })
            ->when($externalSubJenis !== '', function ($query) use ($externalSubJenis) {
                $query->where('jenis_ojk', $externalSubJenis);
            })
            ->latest()
            ->limit(5) 
            ->get();

        return view('home', [
            'user' => $user,
            'internalPeraturan' => $internalPeraturan,
            'externalPeraturan' => $externalPeraturan,
            'internalJenis' => $internalJenis,
            'externalJenis' => $externalJenis,
            'externalSubJenis' => $externalSubJenis,
        ]);
    }
}