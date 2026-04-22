<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(string $id)
    {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
