<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Users;
use App\Maillist;

class EDMController extends Controller
{
    //
    public function index()
    {
        $mailist = Maillist::all();
        return view('edm.index')->with('maillist', $mailist);
    }
}
