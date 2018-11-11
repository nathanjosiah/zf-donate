<?php

namespace ZfDonateTest\Payment\Gateway\Stripe;

use ZfDonate\Payment\Gateway\Stripe\Message\PurchaseMonthlyRequest;
use ZfDonate\Payment\Gateway\Stripe\Message\PurchaseSingleRequest;
use ZfDonate\Payment\Gateway\Stripe\StripeGateway;

class StripeGatewayTest extends \PHPUnit_Framework_TestCase {
	public function testOptionsAreSet() {
		$gateway = new StripeGateway();
		$gateway->setOptions([
			'api_key' => 'abc123',
		]);

		$this->assertSame('abc123',$gateway->getApiKey());
	}

	public function testPlanNameIsPropagated() {
		$gateway = new StripeGateway();
		$gateway->setOptions([
			'monthly_plan_name' => 'myplan',
		]);

		$request = $gateway->purchaseMonthly([]);
		$parameters = $request->getParameters();
		$this->assertInstanceOf(PurchaseMonthlyRequest::class,$request);
		$this->assertSame('myplan',$parameters['plan_name']);
	}

	public function testEmailIsPassedForSingle() {
		$gateway = new StripeGateway();

		$request = $gateway->purchase([
			'email' => 'foobar'
		]);
		$parameters = $request->getParameters();
		$this->assertInstanceOf(PurchaseSingleRequest::class,$request);
		$this->assertSame('foobar',$parameters['email']);
	}
}
