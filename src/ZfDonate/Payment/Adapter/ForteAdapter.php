<?php

namespace ZfDonate\Payment\Adapter;

use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\PaymentResultEntity;
use ZfDonate\Payment\Gateway\Forte\Message\PurchaseRequest;

final class ForteAdapter extends AbstractAdapter {
	private $gateway;

	/**
	 * @param $gateway \ZfDonate\Payment\Gateway\Forte\HttpGateway
	 */
	public function setGateway($gateway) : void {
		$this->gateway = $gateway;
	}

	protected function getOptions(DonationEntity $donation) : array {
		$options = parent::getOptions($donation);
		$options['merchantId'] = $this->gateway->getMerchantId();
		$options['paymentGatewayPassword'] = $this->gateway->getPaymentGatewayPassword();
		return $options;
	}

	public function processSingle(DonationEntity $donation) : PaymentResultEntity {
		$options = $this->getOptions($donation);
		$request = $this->gateway->purchase($options);

		$response = $request->send();

		return $this->processResponse($response);
	}

	public function processMonthly(DonationEntity $donation) : PaymentResultEntity {
		$options = $this->getOptions($donation);

		$options['recurrence_frequency'] = PurchaseRequest::RECUR_MONTHLY;
		$date = new \DateTime(null);
		$date->add(new \DateInterval('P1M'));

		$options['recurrence_start_date'] = $date;
		$options['recurrence_amount'] = $donation->amount;

		// Repeat forever
		$options['recurrence_quantity'] = 0;

		$request = $this->gateway->purchase($options);

		$response = $request->send();

		return $this->processResponse($response);
	}

	private function processResponse($response) : PaymentResultEntity {
		$result = new PaymentResultEntity();
		if(!$response->isSuccessful()) {
			$result->errors = [$response->getMessage()];
		}
		else {
			$result->code = $response->getCode();
			$result->transactionId = $response->getTransactionReference();
			$result->message = $response->getMessage();
			$avs = $response->getAvsResult();
			if($avs) {
				$result->avsResult = (string)$avs;
			}
		}
		return $result;
	}
}

