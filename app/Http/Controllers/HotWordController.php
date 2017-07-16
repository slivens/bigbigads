<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HotWord;

class HotWordController extends Controller
{
    //
    public function getHotWord() {
        $HotWord = HotWord::all();
        return $HotWord;
    }
}
