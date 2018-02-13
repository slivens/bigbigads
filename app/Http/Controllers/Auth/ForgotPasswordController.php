<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Response;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * 方法重写，成功发送邮件方法 or 失败返回
     * 原来处理方案不变，增加json返回，提示语有待商榷
     */

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse($response)
    {
        if (request()->expectsJson()) {
            return Response::json(['send' => 'success']);
        }
        return back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if (request()->expectsJson()) {
            return response()->fail(-1, 'Send email failed,please check your email address and try again later');
        }
        return back()->withErrors(
            ['email' => trans($response)]
        );
    }
}
