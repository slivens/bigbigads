<?php

namespace App\Http\Controllers\Api;

use App\Api\AdserAnalysis as ApiAdserAnalysis;

use DB;
use App\User;
use App\Asset;
use App\Order;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdserAnalysisController extends Controller
{
    public function show(Request $request, $facebook_id)
    {
        $adserAnalysis = ApiAdserAnalysis::getAdserAnalysis($facebook_id);
        return response()->json($adserAnalysis, 200);
    }

    public function getTopAds($facebook_id, $rateType)
    {
        $topAds = ApiAdserAnalysis::getAdserAdByDesc($facebook_id, $rateType);
        return response()->json($topAds, 200);
    }
}
