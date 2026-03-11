<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrashController extends Controller
{
  public function trash()
{
    $deletedperaturan = \App\peraturan::onlyTrashed()
        ->latest('deleted_at')
        ->paginate(10);

    return view('peraturan.trash', ['peraturan' => $deletedperaturan]);
}
}
