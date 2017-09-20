<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Refund;
use App\Contracts\PaymentService;
use TCG\Voyager\Traits\AlertsMessages;
use Voyager;
use App\Notifications\RefundNotification;

class RefundController extends Controller
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function acceptRefund($id)
    {
        $refund = Refund::find($id);
        if (!$refund) {
            return back()->with([
                'message' => "Refund  $id not found",
                'alert-type' => 'error'
            ]);
        }
        $res = $this->paymentService->refund($refund);
        if (!$res) {
            return back()->with([
                'message' => "refund  failed",
                'alert-type' => 'error'
            ]);
        }
        $client = $refund->payment->client;
        $client->notify(new RefundNotification($refund));
        return back();
    }

    public function rejectRefund($id)
    {
        Voyager::canOrFail('edit_refunds');
        $refund = Refund::find($id);
        if (!$refund) {
            return back()->with([
                'message' => "Refund  $id not found",
                'alert-type' => 'error'
            ]);
        }
        $refund->status = Refund::STATE_REJECTED;
        $refund->save();
        $refund->payment->client->notify(new RefundNotification($refund));
        return back()->with([
            'message' => "{$refund->payment->number} rejected",
            'alert-type' => 'success'
        ]);
    }
}
