<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WordpressController extends Controller
{
    function getTrackId () {
        if (Auth::user()) {
            $user = User::find(Auth::user()->id);
            $aff = $user->aff;
            $track = $aff ? $aff->track : '';
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
