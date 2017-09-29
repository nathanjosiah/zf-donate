<?php

namespace ZfDonate\Payment\Gateway\Forte;

use Omnipay\Common\AbstractGateway;
use ZfDonate\Payment\Gateway\OptionsAwareInterface;

/**
 * @see http://www.forte.net/devdocs/pdf/agi_integration.pdf
 */
class HttpGateway extends AbstractGateway implements OptionsAwareInterface {
	public function getName() {
		return 'Forte HTTP Gateway';
	}

	public function getDefaultParameters() {
		return [
			'merchantId' => null,
			'paymentGatewayPassword' => null
		];
	}

	public function setMerchantId($value) {
		return $this->setParameter('merchantId',$value);
	}
	public function getMerchantId() {
		return $this->getParameter('merchantId');
	}
	public function setPaymentGatewayPassword($value) {
		return $this->setParameter('paymentGatewayPassword',$value);
	}
	public function getPaymentGatewayPassword() {
		return $this->getParameter('paymentGatewayPassword');
	}

	public function purchase(array $parameters = array()) {
		return $this->createRequest(\ZfDonate\Payment\Gateway\Forte\Message\PurchaseRequest::class,$parameters);
	}

	public function setOptions(array $options): void {
		if(isset($options['merchant_id'])) {
			$this->setMerchantId($options['merchant_id']);
		}
		if(isset($options['payment_gateway_password'])) {
			$this->setPaymentGatewayPassword($options['payment_gateway_password']);
		}
	}
}

