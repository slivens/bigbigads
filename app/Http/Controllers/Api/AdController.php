<?php

namespace App\Http\Controllers\Api;

use App\Api\Ad as ApiAd;

use App\Ad;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdController extends Controller
{
    public function getAd(Request $request, $event_id)
    {
        $user = $request->user();

        $ad = ApiAd::getAd($request, $event_id);
        if (!$ad) {
            return response()->json([
                'code'    => 404003,
                'message' => 'The ad does not exist'
            ], 404);
        }
        
        return response()->json($ad, 200);
    }
}
