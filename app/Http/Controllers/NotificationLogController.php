<?php

namespace App\Http\Controllers;

use App\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NotificationLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::allows('ADMIN')) {
                return $next($request);
            }

            abort(403, 'Anda tidak memiliki hak akses');
        });
    }

    /**
     * Tampilkan daftar notification log khusus admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = NotificationLog::query()->orderBy('created_at', 'desc');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('recipient_name', 'like', '%'.$keyword.'%')
                    ->orWhere('recipient_email', 'like', '%'.$keyword.'%')
                    ->orWhere('recipient_phone', 'like', '%'.$keyword.'%')
                    ->orWhere('subject', 'like', '%'.$keyword.'%')
                    ->orWhere('message', 'like', '%'.$keyword.'%')
                    ->orWhere('error_message', 'like', '%'.$keyword.'%')
                    ->orWhere('reference_type', 'like', '%'.$keyword.'%')
                    ->orWhere('reference_id', 'like', '%'.$keyword.'%');
            });
        }

        $logs = $query->paginate(25)->appends($request->all());

        $baseStats = NotificationLog::query();
        if ($request->filled('date_from')) {
            $baseStats->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $baseStats->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total' => (clone $baseStats)->count(),
            'success' => (clone $baseStats)->where('status', 'success')->count(),
            'error' => (clone $baseStats)->where('status', 'error')->count(),
            'peraturan' => (clone $baseStats)->where('category', 'peraturan')->count(),
            'cuti' => (clone $baseStats)->where('category', 'cuti')->count(),
        ];

        return view('notification_logs.index', compact('logs', 'stats'));
    }
}

