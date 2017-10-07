<?php
namespace ZfDonateTest\Payment\Adapter;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Stripe\Message\Response;
use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\Adapter\StripeAdapter;
use ZfDonate\Payment\Gateway\Stripe\StripeGateway;
use ZfDonate\Payment\PaymentResultEntity;

class StripeAdapterTest extends \PHPUnit_Framework_TestCase {
	public function testProcessSingle() {
		$gateway = $this->getMockBuilder(StripeGateway::class)->disableOriginalConstructor()->getMock();
		$request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
		$token_request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$token_response = new Response($request,['object'=>'token','id'=>'my_generated_token']);
		$token_request->expects($this->once())->method('send')->willReturn($token_response);
		$gateway->expects($this->once())->method('createToken')->willReturn($token_request);

		$adapter = new StripeAdapter();
		$adapter->setGateway($gateway);

		// Assert the data is passed to the gateway properly
		$gateway->method('getApiKey')->willReturn('mykey');
		$gateway->expects($this->once())->method('purchase')->with($this->callback(function($options) {
			return (
				$options['amount'] === 12.34
				&& empty($options['card'])
				&& $options['token'] === 'my_generated_token'
				&& $options['apiKey'] === 'mykey'
			);
		}))->willReturn($request);
		$request->expects($this->once())->method('send')->willReturn($response);
		$response->method('isSuccessful')->willReturn(true);
		$response->method('getTransactionReference')->willReturn('abc123');

		$donation = new DonationEntity();
		$donation->amount = 12.34;
		$result = $adapter->processSingle($donation);
		$this->assertInstanceOf(PaymentResultEntity::class,$result);
		$this->assertSame($result->transactionId,'abc123');
		$this->assertEmpty($result->errors);
	}
	public function testProcessMonthly() {
		$gateway = $this->getMockBuilder(StripeGateway::class)->disableOriginalConstructor()->getMock();
		$request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
		$token_request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$token_response = new Response($request,['object'=>'token','id'=>'my_generated_token']);
		$token_request->expects($this->once())->method('send')->willReturn($token_response);
		$gateway->expects($this->once())->method('createToken')->willReturn($token_request);

		$adapter = new StripeAdapter();
		$adapter->setGateway($gateway);

		// Assert the data is passed to the gateway properly
		$gateway->method('getApiKey')->willReturn('mykey');
		$gateway->expects($this->once())->method('purchaseMonthly')->with($this->callback(function($options) {
			return (
				$options['amount'] === 12.34
				&& empty($options['card'])
				&& $options['token'] === 'my_generated_token'
				&& $options['apiKey'] === 'mykey'
			);
		}))->willReturn($request);
		$request->expects($this->once())->method('send')->willReturn($response);
		$response->method('isSuccessful')->willReturn(true);
		$response->method('getTransactionReference')->willReturn('abc123');

		$donation = new DonationEntity();
		$donation->amount = 12.34;
		$result = $adapter->processMonthly($donation);
		$this->assertInstanceOf(PaymentResultEntity::class,$result);
		$this->assertSame($result->transactionId,'abc123');
		$this->assertEmpty($result->errors);
	}

	public function testProcessSingle_WithError() {
		$gateway = $this->getMockBuilder(StripeGateway::class)->disableOriginalConstructor()->getMock();
		$request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
		$token_request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$token_response = new Response($request,['object'=>'token','id'=>'my_generated_token']);
		$token_request->expects($this->once())->method('send')->willReturn($token_response);
		$gateway->expects($this->once())->method('createToken')->willReturn($token_request);

		$adapter = new StripeAdapter();
		$adapter->setGateway($gateway);

		$request->method('send')->willReturn($response);
		$gateway->method('purchase')->willReturn($request);
		$response->method('isSuccessful')->willReturn(false);
		$response->method('getMessage')->willReturn('ohnoe');

		$donation = new DonationEntity();
		$result = $adapter->processSingle($donation);
		$this->assertInstanceOf(PaymentResultEntity::class,$result);
		$this->assertSame($result->errors,['ohnoe']);
	}

	public function testProcessMonthly_WithError() {
		$gateway = $this->getMockBuilder(StripeGateway::class)->disableOriginalConstructor()->getMock();
		$request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
		$token_request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$token_response = new Response($request,['object'=>'token','id'=>'my_generated_token']);
		$token_request->expects($this->once())->method('send')->willReturn($token_response);
		$gateway->expects($this->once())->method('createToken')->willReturn($token_request);

		$adapter = new StripeAdapter();
		$adapter->setGateway($gateway);

		$request->method('send')->willReturn($response);
		$gateway->method('purchaseMonthly')->willReturn($request);
		$response->method('isSuccessful')->willReturn(false);
		$response->method('getMessage')->willReturn('ohnoe');

		$donation = new DonationEntity();
		$result = $adapter->processMonthly($donation);
		$this->assertInstanceOf(PaymentResultEntity::class,$result);
		$this->assertSame($result->errors,['ohnoe']);
	}
}
