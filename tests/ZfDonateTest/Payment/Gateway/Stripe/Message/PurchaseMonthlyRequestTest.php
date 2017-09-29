<?php

namespace ZfDonateTest\Payment\Gateway\Stripe\Message;

use ZfDonate\Payment\Gateway\Stripe\Message\PurchaseMonthlyRequest;
use Omnipay\Common\CreditCard;

class PurchaseMonthlyRequestTest extends \PHPUnit_Framework_TestCase {
	public function testData() {
		$http_client = $this->getMockBuilder(\Guzzle\Http\ClientInterface::class)->getMock();
		$http_request = $this->getMockBuilder(\Symfony\Component\HttpFoundation\Request::class)->disableOriginalConstructor()->getMock();
		$message = new PurchaseMonthlyRequest($http_client,$http_request);
		$message->initialize();
		$message->setPlanName('myplan');
		$message->setAmount(12.54);
		$message->setCard(new CreditCard([
			'email' => 'foo@bar.com',
			'number' => '4111111111111111',
			'expiryMonth' => 7,
			'expiryYear' => idate('Y') + 1,
			'cvv' => '123',
		]));
		$data = $message->getData();
		$this->assertSame(12,$data['quantity']);
		$this->assertSame('foo@bar.com',$data['email']);
		$this->assertSame('myplan',$data['plan']);
		$this->assertSame([
			'object'=>'card',
			'number' => '4111111111111111',
			'exp_month' => 7,
			'exp_year' => 2018,
			'cvc' => '123',
			'name' => '',
			'address_line1' => null,
			'address_line2' => null,
			'address_city' => null,
			'address_zip' => null,
			'address_state' => null,
			'address_country' => null,
			'email' => 'foo@bar.com',
		],$data['source']);
	}

	public function testEndpoint() {
		$http_client = $this->getMockBuilder(\Guzzle\Http\ClientInterface::class)->getMock();
		$http_request = $this->getMockBuilder(\Symfony\Component\HttpFoundation\Request::class)->disableOriginalConstructor()->getMock();
		$message = new PurchaseMonthlyRequest($http_client,$http_request);
		$this->assertSame('https://api.stripe.com/v1/customers', $message->getEndpoint());
	}
}

