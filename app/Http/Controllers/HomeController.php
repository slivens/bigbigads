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

    public function totalCount()
    {
        $client = new Client();
        $url = env('TOTAL_COUNT_URL', 'http://127.0.0.1:8080/total_count');
        $res = $client->request('POST', $url, [
            'json' => [
                'search_result' => 'total_count'
            ]
        ]);

        return response()->json(json_decode($res->getBody()));
    }
}
