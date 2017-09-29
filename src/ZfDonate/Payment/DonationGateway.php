<?php

namespace ZfDonate\Payment;

use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\Adapter\AdapterInterface;
use Omnipay\Common\CreditCard;

class DonationGateway {
	private $adapter,$gatewayType;

	public function __construct(AdapterInterface $adapter,$gatewayType) {
		$this->adapter = $adapter;
		$this->gatewayType = $gatewayType;
	}

	/**
	 * @param \ZfDonate\Model\DonationEntity $donation
	 * @return \ZfDonate\Payment\PaymentResultEntity
	 */
	public function processDonation(DonationEntity $donation) {
		if($donation->recurrence === DonationEntity::RECUR_MONTHLY) {
			$result = $this->adapter->processMonthly($donation);
		}
		else {
			$result = $this->adapter->processSingle($donation);
		}

		$donation->gatewayCode = $result->code;
		$donation->gatewayMessage = $result->message;
		$donation->gatewayTransactionId = $result->transactionId;
		$donation->gatewayAvsResult = $result->avsResult;
		$donation->gatewayType = $this->gatewayType;

		$card = new CreditCard([
			'number' => $donation->ccNumber,
		]);
		$donation->ccType = $card->getBrand();
		$donation->ccLastFour = $card->getNumberLastFour();

		return $result;
	}

	public function getAdapter() : AdapterInterface {
		return $this->adapter;
	}

	public function getGatewayType() : string {
		return $this->gatewayType;
	}
}
