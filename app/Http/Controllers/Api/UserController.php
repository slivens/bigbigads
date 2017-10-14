<?php

namespace App\Http\Controllers\Api;

use App\ActionLog;
use App\Jobs\LogAction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getCurrentUser(Request $request)
    {
        $user = $request->user();

        dispatch(new LogAction(ActionLog::ACTION_MOBILE_USER_CURRENT, json_encode($request->all()), '', $user->id, $request->ip()));

        return response()->json([
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'avatar'    => $user->avatar,
            'user_hash' => hash_hmac('sha256', $user->email, 'sh7oS9Q3bk0m-Vs3pEeUjCOMKGjoyjf1bVcTfCiY'),
        ], 200);
    }
}
