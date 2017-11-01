<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\PlanFeedback;

class FeedbackController extends Controller
{
    public function plan(Request $request)
    {
        // plan 的反馈收集是在开放新的plan lite plus的用户调查，该功能估计只开放一个月时间，等plan开放就关闭该功能
        $validator = Validator::make($request->all(), [
            'firstName'     => 'required|between:1,50',
            'lastName'      => 'required|between:1,50',
            'email'         => 'required|email|max:255',
            'company'       => 'required|max:200',
            'website'       => 'max:200',
            'page'          => 'max:200',
            'phone'         => 'max:64',
            'skype'         => 'max:64',
            'level'         => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->messages();
        }
        //die($request->level);
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
            'location'      => $request->location,
            'level'         => $request->level
        ]);
        $planFeedback->save();
        return response()->json(['code' => 0, 'desc' => 'success']);
    }
}
