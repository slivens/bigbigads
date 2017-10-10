<?php

namespace App\Http\Controllers\Api;

use App\Api\Ad as ApiAd;
use App\Api\Owner as ApiOwner;

use App\ActionLog;
use App\Jobs\LogAction;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OwnerController extends Controller
{
    public function getOwners(Request $request)
    {
        $user = $request->user();
        $keywords = $request->input('keywords');
        $page = $request->input('page') ? $request->input('page') : 1;
        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;

        // 页数限制和每页数量限制
        if ($page > 30 || $per_page != 10) {
            return response()->json([
                'code'    => '401000',
                'message' => 'No Permission Get More'
            ], 401);
        }

        $params = [];
        if ($keywords) $params['keywords'] = $keywords;

        $result = ApiOwner::getOwners($params, $page, $per_page);

        if ($page == 1) {
            dispatch(new LogAction(ActionLog::ACTION_MOBILE_OWNER_SEARCH, json_encode($request->all()), json_encode([
                'total' => $result['total'],
            ]), $user->id, $request->ip()));
        } else {
            dispatch(new LogAction(ActionLog::ACTION_MOBILE_OWNER_LOADMORE, json_encode($request->all()), json_encode([
                'total' => $result['total'],
            ]), $user->id, $request->ip()));
        }

        return response()->json($result, 200);
    }

    public function getOwner(Request $request, $facebook_id)
    {
        $user = $request->user();
        $includes = explode(',', $request->input('includes'));
        $owner = ApiOwner::getOwner($facebook_id);

        if (in_array('analysis', $includes)) {
            $owner['analysis'] = ApiOwner::getOwnerAnalysis($facebook_id);

            $owner['analysis']['topAds'] = [];
            $owner['analysis']['topAds']['impression'] = ApiAd::getAds([
                'owner' => $facebook_id,
                'order'     => ['total_impression', 1],
            ], 1, 20)['data'];

            $owner['analysis']['topAds']['like'] = ApiAd::getAds([
                'owner' => $facebook_id,
                'order' => ['like_rate', 1],
            ], 1, 20)['data'];

            $owner['analysis']['topAds']['share'] = ApiAd::getAds([
                'owner' => $facebook_id,
                'order' => ['share_rate', 1],
            ], 1, 20)['data'];

            $owner['analysis']['topAds']['comment'] = ApiAd::getAds([
                'owner' => $facebook_id,
                'order' => ['comment_rate', 1],
            ], 1, 20)['data'];
        }

        dispatch(new LogAction(ActionLog::ACTION_MOBILE_OWNER_ANALYSIS, json_encode($request->all()), '', $user->id, $request->ip()));

        return response()->json($owner, 200);
    }
}
