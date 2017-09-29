<?php

namespace ZfDonate\Payment\Gateway\Stripe\Message;

class PurchaseMonthlyRequest extends \Omnipay\Stripe\Message\AbstractRequest {
	public function getData() {
		$data = [];
		$data['quantity'] = (int)($this->getAmountInteger() / 100);
		$data['email'] = $this->getCard()->getEmail();
		$data['plan'] = $this->getParameter('plan_name');
		$data['source'] = array_merge(['object' => 'card'],$this->getCardData());
		return $data;
	}

	public function getEndpoint() {
		return $this->endpoint . '/customers';
	}

	public function setPlanName(string $plan) : void {
		$this->parameters->set('plan_name',$plan);
	}
}
