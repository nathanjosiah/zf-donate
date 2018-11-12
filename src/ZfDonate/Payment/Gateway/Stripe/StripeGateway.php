<?php

namespace ZfDonate\Payment\Gateway\Stripe;

use Omnipay\Stripe\Gateway;
use ZfDonate\Payment\Gateway\OptionsAwareInterface;

class StripeGateway extends Gateway implements OptionsAwareInterface {
	private $planName;
	public function purchaseMonthly(array $parameters) {
		if($this->getParameter('plan_name')) {
			$parameters['plan_name'] = $this->getParameter('plan_name');
		}
		return $this->createRequest(\ZfDonate\Payment\Gateway\Stripe\Message\PurchaseMonthlyRequest::class,$parameters);
	}

	public function setOptions(array $options): void {
		if(isset($options['api_key'])) {
			$this->setApiKey($options['api_key']);
		}
		if(isset($options['monthly_plan_name'])) {
			$this->setParameter('plan_name',$options['monthly_plan_name']);
		}
	}
}
