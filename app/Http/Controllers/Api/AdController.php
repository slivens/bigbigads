<?php

namespace App\Http\Controllers\Api;

use App\Api\Ad as ApiAd;

use App\ActionLog;
use App\Jobs\LogAction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdController extends Controller
{
    public function getAd(Request $request, $event_id)
    {
        $user = $request->user();

        $ad = ApiAd::getAd($event_id);
        if (!$ad) {
            return response()->json([
                'code'    => 404003,
                'message' => 'The ad does not exist'
            ], 404);
        }

        dispatch(new LogAction(ActionLog::ACTION_MOBILE_AD_ANALYSIS, json_encode($request->all()), '', $user->id, $request->ip()));

        return response()->json($ad, 200);
    }
}
