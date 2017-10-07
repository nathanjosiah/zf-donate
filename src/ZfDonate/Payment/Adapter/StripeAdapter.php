<?php

namespace ZfDonate\Payment\Adapter;

use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\PaymentResultEntity;
use Omnipay\Stripe\Message\Response;

final class StripeAdapter extends AbstractAdapter {
	private $gateway;

	/**
	 * @param $gateway \ZfDonate\Payment\Gateway\Stripe\StripeGateway
	 */
	public function setGateway($gateway) : void {
		$this->gateway = $gateway;
	}

	protected function getOptions(DonationEntity $donation) : array {
		$options = parent::getOptions($donation);
		$options['apiKey'] = $this->gateway->getApiKey();
		return $options;
	}

	public function processSingle(DonationEntity $donation) : PaymentResultEntity {
		$options = $this->getOptions($donation);
		$options = $this->convertCardToToken($options);
		$request = $this->gateway->purchase($options);

		$response = $request->send();

		return $this->processResponse($response);
	}

	protected function convertCardToToken(array $parameters) : array {
		$token_request = $this->gateway->createToken(['card'=>$parameters['card']]);
		$result = $token_request->send();
		$parameters['token'] = $result->getToken();
		unset($parameters['card']);
		return $parameters;
	}

	public function processMonthly(DonationEntity $donation) : PaymentResultEntity {
		$options = $this->getOptions($donation);
		$options = $this->convertCardToToken($options);
		$request = $this->gateway->purchaseMonthly($options);

		$response = $request->send();
		return $this->processResponse($response);
	}

	private function processResponse(Response $response) : PaymentResultEntity {
		$result = new PaymentResultEntity();
		if(!$response->isSuccessful()) {
			$result->errors = [$response->getMessage()];
		}
		else {
			$result->transactionId = $response->getTransactionReference();
		}
		return $result;
	}
}

