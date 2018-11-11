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
		$message->setEmail('foo@bar.com');
		$message->setToken('mytoken');
		$data = $message->getData();
		$this->assertSame(12,$data['quantity']);
		$this->assertSame('foo@bar.com',$data['email']);
		$this->assertSame('myplan',$data['plan']);
		$this->assertSame('mytoken',$data['source']);
	}

	public function testEndpoint() {
		$http_client = $this->getMockBuilder(\Guzzle\Http\ClientInterface::class)->getMock();
		$http_request = $this->getMockBuilder(\Symfony\Component\HttpFoundation\Request::class)->disableOriginalConstructor()->getMock();
		$message = new PurchaseMonthlyRequest($http_client,$http_request);
		$this->assertSame('https://api.stripe.com/v1/customers', $message->getEndpoint());
	}
}

