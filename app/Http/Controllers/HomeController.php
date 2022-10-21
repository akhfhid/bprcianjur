<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\peraturan;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('cekstatus');
        $this->middleware('auth');
      
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $aktif = \Auth::user()->id;
        $user = \App\User::where('id',$aktif)->first();


        $peraturan = peraturan::latest()->limit(5)->get();
        

        return view('home',['user'=>$user,'peraturan'=>$peraturan]);
    }
}
