<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReminderLog;
use Illuminate\Http\Request;

class ReminderLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reminder_logs,view']);
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
