<?php

namespace ZfDonate\Payment\Adapter;

use ZfDonate\Model\DonationEntity;
use Omnipay\Common\CreditCard;

abstract class AbstractAdapter implements AdapterInterface {
	protected function getOptions(DonationEntity $donation) : array {
		$options = [
			'amount' => (float)$donation->amount,
		];

		$base_data = [
			'firstName' => $donation->firstName,
			'lastName' => $donation->lastName,
			'email' => $donation->email,
			'billingAddress1' => $donation->address,
			'billingCity' => $donation->city,
			'billingPostcode' => $donation->postalCode,
			'billingState' => $donation->state,
			'billingCountry' => 'US',
			'shippingPhone' => $donation->phone,
			'shippingAddress1' => $donation->address,
			'shippingCity' => $donation->city,
			'shippingPostcode' => $donation->postalCode,
			'shippingState' => $donation->state,
			'shippingCountry' => 'US',
			'shippingPhone' => $donation->phone,
			'number' => $donation->ccNumber,
			'expiryMonth' => $donation->ccExpirationMonth,
			'expiryYear' => $donation->ccExpirationYear,
			'cvv' => $donation->ccV,
		];

		$options['card'] = new CreditCard($base_data);

		return $options;
	}
}

