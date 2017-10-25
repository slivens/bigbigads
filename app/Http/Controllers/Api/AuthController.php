<?php

namespace App\Http\Controllers\Api;

use App\ActionLog;
use App\Jobs\LogAction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function token(Request $request)
    {
        $user = $request->user();
        $date = Carbon::now();

        $token = JWT::encode([
            'iat' => $date->timestamp,
            'exp' => $date->addYear()->timestamp,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email
            ]
        ], 'x-grit2017');

        if ($user) {
            return response()->json([
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'message' => 'Account not logged in.'
            ], 401);
        }
    }
}
