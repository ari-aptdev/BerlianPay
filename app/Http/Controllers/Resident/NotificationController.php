<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markRead(Request $request)
    {
        $request->user()->residentNotifications()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->noContent();
    }
}
