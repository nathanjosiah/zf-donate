<?php
namespace ZfDonate\Model;

class DonationEntity {
	const TYPE_CREDIT_CARD = 'Credit Card';
	const TYPE_EFT = 'EFT';
	const EFT_TYPE_CHECKING = 'Checking';
	const EFT_TYPE_SAVINGS = 'Savings';
	const RECUR_NONE = 'Single';
	const RECUR_MONTHLY = 'Monthly';

	public
	$id,
	$amount,
	$recurrence,
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