<?php
namespace ZfDonateTest\Adapter;


use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\Adapter\AbstractAdapter;
use ZfDonate\Payment\PaymentResultEntity;

class AbstractAdapterStub extends AbstractAdapter  {
	public function processSingle(DonationEntity $donation): PaymentResultEntity {
	}

	public function processMonthly(DonationEntity $donation): PaymentResultEntity {
	}

	public function setGateway($gateway): void {
	}

	public function getOptions(DonationEntity $donation): array {
		return parent::getOptions($donation);
	}
}