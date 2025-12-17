<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class PangkatController extends Controller
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
        $pangkat = \App\Pangkat::paginate(10);
        $filterKeyword = $request->get('name');

        if ($filterKeyword) {
            $pangkat = \App\Pangkat::where('name', 'LIKE', "%$filterKeyword%")->paginate(10);
        }

        return view('pangkat.index', ['pangkat' => $pangkat]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pendidikan = \App\Pendidikan::pluck('name', 'id');

        return view('pangkat.create', ['pendidikan' => $pendidikan]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $min = $request->get('pendmin');
        $max = $request->get('pendmax');

        $new_pangkat = new \App\Pangkat();
        $new_pangkat->name = $name;
        $new_pangkat->pendmin = $min;
        $new_pangkat->pendmax = $max;
        $new_pangkat->created_by = \Auth::user()->id;
        $new_pangkat->save();

        return redirect()->route('pangkat.index')->with('status', 'Pangkat Succesfully Created');
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
        $pangkat_to_edit = \App\Pangkat::findOrFail($id);
        $pendidikan = \App\Pendidikan::pluck('name', 'id');

        return view('pangkat.edit', ['pangkat' => $pangkat_to_edit, 'pendidikan' => $pendidikan]);
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
        $name = $request->get('name');
        $min = $request->get('pendmin');
        $max = $request->get('pendmax');

        $pangkat = \App\Pangkat::findOrFail($id);
        $pangkat->name = $name;
        $pangkat->pendmin = $min;
        $pangkat->pendmax = $max;

        $pangkat->updated_by = \Auth::user()->id;
        $pangkat->save();
        return redirect()
            ->route('pangkat.index', [$id])
            ->with('status', 'Pangkat Succesfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pangkat = \App\Pangkat::findOrFail($id);

        $pangkat->delete();

        return redirect()->route('pangkat.index')->with('status', 'Pangkat Succesfully moved to Trash');
    }

    public function trash()
    {
        $deleted_pangkat = \App\Pangkat::onlyTrashed()->paginate(10);

        return view('pangkat.trash', ['pangkat' => $deleted_pangkat]);
    }

    public function restore($id)
    {
        $pangkat = \App\Pangkat::withTrashed()->findOrFail($id);
        if ($pangkat->trashed()) {
            $pangkat->restore();
        } else {
            return redirect()->route('pangkat.index')->with('status', 'Pangkat is not in trash');
        }
        return redirect()->route('pangkat.index')->with('status', 'Pangkat Successfully Restored');
    }

    public function deletePermanent($id)
    {
        $pangkat = \App\Pangkat::withTrashed()->findOrFail($id);

        if (!$pangkat->trashed()) {
            return redirect()->route('pangkat.index')->with('status', 'Cannot Delete Permanent Active Pangkat');
        } else {
            $pangkat->forceDelete();

            return redirect()->route('pangkat.index')->with('status', 'Pangkat Permanently Deleted');
        }
    }

    public function ajaxsearch(Request $request)
    {
        $keyword = $request->get('q');

        $pangkat = \App\Pangkat::where('name', 'LIKE', "%$keyword%")
            ->distinct()
            ->get();

        return $pangkat;
    }
}
