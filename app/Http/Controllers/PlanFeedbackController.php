<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\PlanFeedback;

class PlanFeedbackController extends Controller
{
    public function store(Request $request)
    {
        // plan 的反馈收集是在开放新的plan lite plus的用户调查，该功能估计只开放一个月时间，等plan开放就关闭该功能
        $validator = Validator::make($request->all(), [
            'firstName'     => 'required|between:1,50',
            'lastName'      => 'required|between:1,50',
            'email'         => 'required|between:4,100',
            'company'       => 'required|max:200',
            'website'       => 'max:200',
            'page'          => 'max:200',
            'phone'         => 'max:64',
            'skype'         => 'max:64',
        ]);
        if ($validator->fails()) {
            return $validator->messages();
        }
        $planFeedback = PlanFeedback::create([
            'first_name'    => $request->firstName,
            'last_name'     => $request->lastName,
            'email'         => $request->email,
            'company'       => $request->company,
            'website'       => $request->website,
            'page'          => $request->page,
            'phone'         => $request->phone,
            'skype'         => $request->skype,
            'price'         => $request->price,
            'feedback'      => $request->feedback,
            'location'      => $request->location
        ]);
        $planFeedback->save();
    }
}
