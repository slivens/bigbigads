<?php

namespace App\Http\Controllers\Api;

use App\Api\Ad as ApiAd;
use App\Api\Publisher as ApiPublisher;

use DB;
use App\User;
use App\Asset;
use App\Order;
use App\Publisher;
use App\PublisherPlan;
use App\PublisherSubscribe;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Notification;
use App\Notifications\SubscribePublisher;

class PublisherController extends Controller
{
    

    public function getPublisher(Request $request, $facebook_id)
    {
        $publisher = ApiPublisher::getPublisher($request, $facebook_id);
        return response()->json($publisher, 200);
    }

    public function search(Request $request)
    {   
        $keywords = $request->input('keywords');
        $page = $request->input('page') ? $request->input('page') : 1;
        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;


        if ($page > 10 || $per_page != 10) {
            return response()->json([
                'code'    => '401000',
                'message' => 'No Permission Get More'
            ], 401);
        }
        
        $result = ApiPublisher::getPublishers($request, [
            'keywords' => $keywords,
        ], $page, $per_page);
        return response()->json($result, 200);
    }
}
