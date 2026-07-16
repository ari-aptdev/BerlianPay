<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:settings,view'])->only(['edit']);
        $this->middleware(['auth', 'permission:settings,edit'])->only(['update']);
    }

    public function edit()
    {
        $settings = [
            'due_date' => Setting::get('due_date', 10),
            'reminder_h_minus' => Setting::get('reminder_h_minus', 3),
            'followup_dates' => Setting::get('followup_dates', '20,28'),
            'email_reminder_enabled' => Setting::get('email_reminder_enabled', '1'),
            'wa_reminder_enabled' => Setting::get('wa_reminder_enabled', '0'),
            'session_timeout_minutes' => Setting::get('session_timeout_minutes', 30),
        ];

        return view('admin.settings.reminder', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'due_date' => ['required', 'integer', 'between:1,28'],
            'reminder_h_minus' => ['required', 'integer', 'between:0,10'],
            'followup_dates' => ['required', 'string'],
            'email_reminder_enabled' => ['boolean'],
            'wa_reminder_enabled' => ['boolean'],
            'session_timeout_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, is_bool($value) ? (string) (int) $value : $value);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
