<?php
namespace ZfDonate\Model;

class DonationEntity {
	const TYPE_CREDIT_CARD = 'Credit Card';

	public
	$id,
	$amount,
	$recurring,
	$firstName,
	$lastName,
	$email,
	$phone,
	$address,
	$city,
	$state,
	$postalCode,
	$ccNumber,
	$ccLastFour,
	$ccType,
	$ccV,
	$ccExpirationMonth,
	$ccExpirationYear,
	$gatewayType,
	$gatewayAvsResult,
	$gatewayCode,
	$gatewayMessage,
	$gatewayTransactionId;
}