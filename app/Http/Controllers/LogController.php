<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\loguser;
use DataTables;

class LogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = loguser::query();
            if ($request->tahun) {
                $data->whereYear('created_at', $request->tahun);
            }
            if ($request->bulan) {
                $data->whereMonth('created_at', $request->bulan);
            }
            if ($request->periode) {
                switch ($request->periode) {
                    case 'quarter_1': // Jan–Mar
                        $data->whereBetween('created_at', [date('Y-01-01 00:00:00'), date('Y-03-31 23:59:59')]);
                        break;

                    case 'quarter_2': // Apr–Jun
                        $data->whereBetween('created_at', [date('Y-04-01 00:00:00'), date('Y-06-30 23:59:59')]);
                        break;

                    case 'quarter_3': // Jul–Sep
                        $data->whereBetween('created_at', [date('Y-07-01 00:00:00'), date('Y-09-30 23:59:59')]);
                        break;

                    case 'quarter_4': // Oct–Dec
                        $data->whereBetween('created_at', [date('Y-10-01 00:00:00'), date('Y-12-31 23:59:59')]);
                        break;
                    case 'yearly':
                        $data->whereYear('created_at', now()->year);
                        break;
                }
            }
            if ($request->start_date && $request->end_date) {
                $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            $data = $data->latest()->get();

            return DataTables::of($data)
                ->editColumn('created_at', function ($d) {
                    return $d->created_at->format('Y-m-d H:i:s');
                })
                ->make(true);
        }

        return view('loguser.index');
    }
}
