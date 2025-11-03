<?php

// app/Http/Controllers/DeviceTokenController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceToken;

class DeviceTokenController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string'
        ]);
        $user = $request->user();
        DeviceToken::updateOrCreate(
            [
                'token' => $request->token,
                'user_id' => $user->id,
                'platform' => $request->platform,
            ]
        );
        return response()->json(['success' => true]);
    }

    public function remove(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        DeviceToken::where('token', $request->token)->delete();
        return response()->json(['success' => true]);
    }
}
