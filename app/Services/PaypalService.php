<?php

namespace App\Services;

use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
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
					'log.LogEnabled' => true,
					'log.FileName' => 'PayPal.log',
					'log.LogLevel' => 'DEBUG'
				)
            );
		return $apiContext;
    }

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
            ->setSetupFee(new Currency(array('value' => 0, 'currency' => 'USD')));

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


        return $output;
    }
}
