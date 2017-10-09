<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use TCG\Voyager\Models\Post;
use Jenssegers\Agent\Agent;

class HomeController extends Controller
{
    public function index()
    {
        $agent = new Agent();

        if ($agent->isMobile()) {
            return view('mobile');
        } else {
            $recents = Post::orderBy('created_at', 'desc')->take(5)->get();
            return view('index')->with('recents', $recents);
        }
    }

    public function getTotalCount()
    {
        $client = new Client();
        $url = config("services.bigbigads.adser_search_url");//env('ADSER_SEARCH_URL', 'http://127.0.0.1:8080/adser_search');
        $res = $client->request('POST', $url, [
            'body' => '{}'
        ]);

        $data = json_decode($res->getBody(), true);

        return response()->json([
            'total_ads_count'   => $data['total_ads_count'],
            'total_adser_count' => $data['total_adser_count'],
        ]);
    }
}
