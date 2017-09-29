<?php

namespace ZfDonate\Payment\Adapter;

use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\PaymentResultEntity;

interface AdapterInterface {
	public function processSingle(DonationEntity $donation) : PaymentResultEntity;
	public function processMonthly(DonationEntity $donation) : PaymentResultEntity;
	public function setGateway($gateway) : void;
}

