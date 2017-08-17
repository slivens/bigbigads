<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AudienceInterest;

class AudienceInterestController extends Controller
{
    public function getAudienceInterest() {
        $AudienceInterest = AudienceInterest::all();
        return $AudienceInterest;
    }
}
