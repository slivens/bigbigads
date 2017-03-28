<?php

namespace App\Services;

use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\Payer;
use PayPal\Api\ShippingAddress;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;
use Carbon\Carbon;
use Log;

class PaypalService
{
    public function getApiContext()
    {
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                config('payment.client_id'),     // ClientID
                config('payment.client_secret')      // ClientSecret
            ));
        $apiContext->setConfig( 
            array(
                'mode'=>config('payment.mode')
                )
            );
        /*         'log.LogEnabled' => true, */
        /*         'log.FileName' => 'PayPal.log', */
        /*         'log.LogLevel' => 'DEBUG' */
        /*     ) */
        /* ); */
        return $apiContext;
    }

    /**
     * 根据plan的参数创建Paypal对应的plan,目前只创建循环扣款的Plan
     * @TODO 支付循环与非循环扣款
     */
    public function createPlan($param)
    {
        $apiContext = $this->getApiContext();
        $plan = new Plan();

        // # Basic Information
        // Fill up the basic information that is required for the plan
        $plan->setName($param->name)
            ->setDescription($param->display_name)
            ->setType('INFINITE');

        // # Payment definitions for this billing plan.
        $paymentDefinition = new PaymentDefinition();

        // The possible values for such setters are mentioned in the setter method documentation.
        // Just open the class file. e.g. lib/PayPal/Api/PaymentDefinition.php and look for setFrequency method.
        // You should be able to see the acceptable values in the comments.
        $paymentDefinition->setName($param->name)
            ->setType($param->type)
            ->setFrequency($param->frequency)
            ->setFrequencyInterval((string)$param->frequency_interval)
            ->setCycles((string)$param->cycles)
            ->setAmount(new Currency(array('value' => $param->amount, 'currency' => $param->currency)));

        // Charge Models
        $chargeModel = new ChargeModel();
        $chargeModel->setType('TAX')
            ->setAmount(new Currency(array('value' => 0, 'currency' => $param->currency)));

        $returnUrl = config('payment.returnurl');
        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl("$returnUrl?success=true")
            ->setCancelUrl("$returnUrl?success=false")
            ->setAutoBillAmount("yes")
            ->setInitialFailAmountAction("CONTINUE")
            ->setMaxFailAttempts("0")
            ->setSetupFee(new Currency(array('value' => $param->amount, 'currency' => 'USD')));

        $plan->setPaymentDefinitions(array($paymentDefinition));
        $plan->setMerchantPreferences($merchantPreferences);
        // For Sample Purposes Only.
        $request = clone $plan;

        // ### Create Plan
        try {
            $output = $plan->create($apiContext);
        } catch (Exception $ex) {
            return false;
        }
		$patch = new Patch();

		$value = new PayPalModel('{"state":"ACTIVE"}');

		$patch->setOp('replace')
			->setPath('/')
			->setValue($value);
		$patchRequest = new PatchRequest();
		$patchRequest->addPatch($patch);

		$output->update($patchRequest, $apiContext);

        return $output;
    }

    public function plans()
    {
        $apiContext = $this->getApiContext();
        try {
            $params = array('page_size' => '10');
            $planList = Plan::all($params, $apiContext);
        } catch(\Exception $e) {
            Log::error("paypal get plan list failed:" . $e->getMessage());
            return null;
        }
        return $planList;
    }

    /**
     * 删除所有的升级计划
     */
    public function dropPlans()
    {
        $planList = $this->plans();
        if ($planList == null || json_encode($planList) == "") {
            echo "get plan list failed";
            return;
        }
        $apiContext = $this->getApiContext();
        $plans = $planList->getPlans();
        foreach($plans as $key => $item) {
            echo "on deleting $key, {$item->getId()}, {$item->getName()}";
            $item->delete($apiContext);
            /* $plan = Plan::get($item->getId(), $apiContext); */
        }
        return $planList;
    }

    /**
     * 创建支付订单
     */
    public function createPayment($param)
    {
        $apiContext = $this->getApiContext();
        $agreement = new Agreement();

        $agreement->setName($param['name'])
            ->setDescription($param['display_name'])
            ->setStartDate(Carbon::now()->addMonth()->toIso8601String());

        // Add Plan ID
        // Please note that the plan Id should be only set in this case.
        $plan = new Plan();
        $plan->setId($param['remote_id']);
        $agreement->setPlan($plan);

        // Add Payer
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $agreement->setPayer($payer);

        // Add Shipping Address
        /* $shippingAddress = new ShippingAddress(); */
        /* $shippingAddress->setLine1('111 First Street') */
        /*     ->setCity('Saratoga') */
        /*     ->setState('CA') */
        /*     ->setPostalCode('95070') */
        /*     ->setCountryCode('US'); */
        /* $agreement->setShippingAddress($shippingAddress); */

        // For Sample Purposes Only.
        $request = clone $agreement;

        // ### Create Agreement
        try {
            // Please note that as the agreement has not yet activated, we wont be receiving the ID just yet.
            $agreement = $agreement->create($apiContext);

            // ### Get redirect url
            // The API response provides the url that you must redirect
            // the buyer to. Retrieve the url from the $agreement->getApprovalLink()
            // method
            $approvalUrl = $agreement->getApprovalLink();
        } catch (Exception $ex) {
            Log::error("paypal create payment:" . $ex->getMessage());
            return "create payment failed, please retry or contact the merchant.";
        }
        Log::info("paypal id:" . $approvalUrl);
        return $approvalUrl;
    }

    /**
     * 客户同意支付的回调
     */
    public function onPay($request)
    {
		$apiContext = $this->getApiContext();
        if ($request->has('success') && $request->success == 'true') {
            $token = $request->token;
            $agreement = new \PayPal\Api\Agreement();
            try {
                 $agreement->execute($token, $apiContext);
            } catch(\Exception $e) {
                Log::error("pay failed after user's agreement" . $e->getMessage());
                return ull;
            }
            Log::info("onpay id:" . $agreement->getId());
            return $agreement;
        }
        return null;
    }

    /**
     * 获取支付订单
     * @param $id  paypal的payment_id
     */
    public function subscription($id)
    {
        $apiContext = $this->getApiContext();
        try {
            $agreement = Agreement::get($id, $apiContext);
        } catch(\Exception $e) {
            Log::error("get subscription failed" . $e->getMessage());
            return null;
        }
        return $agreement;
    }

    /**
     * 将指令的订阅挂起
     */
    public function suspendSubscription($id)
    {
        $subscription = $this->subscription($id);

        $apiContext = $this->getApiContext();
        $agreementStateDescriptor = new AgreementStateDescriptor();
        $agreementStateDescriptor->setNote("Suspending the agreement");
        try {
             $subscription->suspend($agreementStateDescriptor, $apiContext);
        } catch (\Exception $e) {
            Log::error("suspend:" . $e->getMessage());
            return null;
        }
        return $subscription;
    }

    /** 获取交易记录
     * @param $id subscription payment_id
     * @warning 总是获取该subscription的所有记录
     */
    public function transactions($id)
    {
        $apiContext = $this->getApiContext();

        /* $agreement = Agreement::get($id, $apiContext); */
        /* echo $agreement; */
        /* die("xx"); */
        $params = ['start_date' => date('Y-m-d', strtotime('-15 years')), 'end_date' => date('Y-m-d', strtotime('+5 days'))];
        try {
            $result = Agreement::searchTransactions($id, $params, $apiContext);
        } catch(\Exception $e) {
            Log::error("get transactions failed" . $e->getMessage());
            return null;
        }
        return $result->getAgreementTransactionList() ;
    }
}
