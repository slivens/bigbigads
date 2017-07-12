<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WordpressController extends Controller
{
    function getTrackId (Request $request) {
        if (Auth::user()) {
            $user = User::find(Auth::user()->id);
            $affiliates = $user->affiliates;

            // 返回用户第一个 affiliate 的 track
            $track = $affiliates[0] ? $affiliates[0]->track : '';
        } else {
            $track = '';
        }

        return 'var track_id = \'' . $track . '\';';
    }

    function trackNotice (Request $request) {
        // $url = $request->input('url');
        // $track = $request->input('track');

        // return response()->json([
        //     'url' => $url,
        //     'track' => $track
        // ]);
    }
}
