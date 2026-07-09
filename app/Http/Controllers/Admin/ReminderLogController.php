<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReminderLog;
use Illuminate\Http\Request;

class ReminderLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->isAdmin(), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $logs = ReminderLog::with('house', 'payment')
            ->when($request->channel, fn ($q) => $q->where('channel', $request->channel))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('sent_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.reminder-logs.index', compact('logs'));
    }
}
