<?php

namespace ZfDonate\Payment\Gateway\Stripe\Message;

class PurchaseSingleRequest extends \Omnipay\Stripe\Message\PurchaseRequest {
	public function getData() {
		$data = parent::getData();
		$data['email'] = $this->getParameter('email');
		return $data;
	}

	public function getEmail() {
		return $this->getParameter('email');
	}

	public function setEmail($email) {
		$this->setParameter('email', $email);
		return $this;
	}
}
