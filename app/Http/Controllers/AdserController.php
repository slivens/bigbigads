<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\LogAction;
use App\ActionLog;
use Log;
use App\Jobs\LogAbnormalAction;
use App\Api\Adser as ApiAdser;

class AdserController extends Controller
{
    static public function getPublishers(Request $request)
    {
        $keywords = $request->input('keywords');
        $page = $request->input('page') ? $request->input('page') : 1;
        $limit = $request->input('limit') ? $request->input('limit') : 10;

        $result = ApiAdser::getPublishers(['keywords' => $keywords,], $page, $limit);

        return response()->json($result, 200);
    }

    static public function getPublisher(Request $request, $facebookId)
    {
        $result = ApiAdser::getPublisher($facebookId);

        if (!$result) {
            return response()->json([
                'code'    => -1,
                'message' => 'The adser does not exist'
            ]);
        }
        
        return response()->json($result, 200);
    }
}
