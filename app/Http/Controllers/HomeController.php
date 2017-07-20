<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use TCG\Voyager\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $recents = Post::orderBy('created_at', 'desc')->take(5)->get();
        return view('index')->with('recents', $recents);
    }

    public function getTotalCount()
    {
        $client = new Client();
        $url = env('ADSER_SEARCH_URL', 'http://127.0.0.1:8080/adser_search');
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
