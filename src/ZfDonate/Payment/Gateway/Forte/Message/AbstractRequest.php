<?php
namespace ZfDonate\Payment\Gateway\Forte\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest {
	protected $liveEndpoint = 'https://www.paymentsgateway.net/cgi-bin/postauth.pl';
	protected $testEndpoint = 'https://www.paymentsgateway.net/cgi-bin/posttest.pl';

	protected function getBaseData() {
		$data = [
			'PG_MERCHANT_ID' => $this->getMerchantId(),
			'pg_password' => $this->getPaymentGatewayPassword()
		];
		return $data;
	}

	public function sendData($data) {
		$body = '';
		foreach($data as $key => $val) {
			$body .= $key . '=' . rawurlencode($val) . "\n";
		}
		$body .= 'endofdata' . "\n";
		$request = $this->httpClient->post($this->getEndpoint(),null,$body);
		$response = $request->send();
		return new PurchaseResponse($request,$response);
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
	protected function getEndpoint() {
		return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
	}
}
