<?php
namespace ZfDonateTest\Payment\Adapter;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Stripe\Message\Response;
use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\Adapter\StripeAdapter;
use ZfDonate\Payment\Gateway\Stripe\StripeGateway;
use ZfDonate\Payment\PaymentResultEntity;

class StripeAdapterTest extends \PHPUnit\Framework\TestCase {
	public function testProcessSingle() {
		$gateway = $this->getMockBuilder(StripeGateway::class)->disableOriginalConstructor()->getMock();
		$request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
		$token_request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$token_response = new Response($request,json_encode(['object'=>'token','id'=>'my_generated_token']));
		$token_request->expects($this->once())->method('send')->willReturn($token_response);
		$gateway->expects($this->once())
			->method('createToken')
			->with($this->callback(function(array $args) {
				$this->assertInstanceOf(CreditCard::class, $args['card']);
				return true;
			}))
			->willReturn($token_request);

		$customerRequest = $this->getMockBuilder(RequestInterface::class)->disableOriginalConstructor()->getMock();
		$customerRequest->expects($this->once())->method('send')->willReturn(new Response($request, '{"id":"abc123"}'));
		$gateway->expects($this->once())
			->method('createCustomer')
			->with(['email' => 'foobar'])
			->willReturn($customerRequest);

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
				&& $options['customer'] === 'abc123'
				&& !empty($options['metadata'])
				&& $options['metadata']['email'] === 'foobar'
			);
		}))->willReturn($request);
		$request->expects($this->once())->method('send')->willReturn($response);
		$response->method('isSuccessful')->willReturn(true);
		$response->method('getTransactionReference')->willReturn('abc123');

		$donation = new DonationEntity();
		$donation->amount = 12.34;
		$donation->email = 'foobar';
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
		$token_response = new Response($request,json_encode(['object'=>'token','id'=>'my_generated_token']));
		$token_request->expects($this->once())->method('send')->willReturn($token_response);
		$gateway->expects($this->once())
			->method('createToken')
			->with($this->callback(function(array $args) {
				$this->assertInstanceOf(CreditCard::class, $args['card']);

				return true;
			}))
			->willReturn($token_request);

		$customerRequest = $this->getMockBuilder(RequestInterface::class)->disableOriginalConstructor()->getMock();
		$customerRequest->expects($this->once())->method('send')->willReturn(new Response($request, '{"id":"abc123"}'));
		$gateway->expects($this->once())
			->method('createCustomer')
			->with(['email' => 'foobar'])
			->willReturn($customerRequest);

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
				&& !empty($options['metadata'])
				&& $options['metadata']['email'] === 'foobar'
			);
		}))->willReturn($request);
		$request->expects($this->once())->method('send')->willReturn($response);
		$response->method('isSuccessful')->willReturn(true);
		$response->method('getTransactionReference')->willReturn('abc123');

		$donation = new DonationEntity();
		$donation->amount = 12.34;
		$donation->email = 'foobar';
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
		$token_response = new Response($request,json_encode(['object'=>'token','id'=>'my_generated_token']));
		$token_request->expects($this->once())->method('send')->willReturn($token_response);
		$gateway->expects($this->once())
			->method('createToken')
			->with($this->callback(function(array $args) {
				$this->assertInstanceOf(CreditCard::class, $args['card']);

				return true;
			}))
			->willReturn($token_request);

		$customerRequest = $this->getMockBuilder(RequestInterface::class)->disableOriginalConstructor()->getMock();
		$customerRequest->expects($this->once())->method('send')->willReturn(new Response($request, '{"id":"abc123"}'));
		$gateway->expects($this->once())
			->method('createCustomer')
			->with(['email' => 'foobar'])
			->willReturn($customerRequest);

		$adapter = new StripeAdapter();
		$adapter->setGateway($gateway);

		$request->method('send')->willReturn($response);
		$gateway->method('purchase')->willReturn($request);
		$response->method('isSuccessful')->willReturn(false);
		$response->method('getMessage')->willReturn('ohnoe');

		$donation = new DonationEntity();
		$donation->email = 'foobar';
		$result = $adapter->processSingle($donation);
		$this->assertInstanceOf(PaymentResultEntity::class,$result);
		$this->assertSame($result->errors,['ohnoe']);
	}

	public function testProcessMonthly_WithError() {
		$gateway = $this->getMockBuilder(StripeGateway::class)->disableOriginalConstructor()->getMock();
		$request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
		$token_request = $this->getMockBuilder(RequestInterface::class)->getMock();
		$token_response = new Response($request,json_encode(['object'=>'token','id'=>'my_generated_token']));
		$token_request->expects($this->once())->method('send')->willReturn($token_response);
		$gateway->expects($this->once())
			->method('createToken')
			->with($this->callback(function(array $args) {
				$this->assertInstanceOf(CreditCard::class, $args['card']);

				return true;
			}))
			->willReturn($token_request);

		$customerRequest = $this->getMockBuilder(RequestInterface::class)->disableOriginalConstructor()->getMock();
		$customerRequest->expects($this->once())->method('send')->willReturn(new Response($request, '{"id":"abc123"}'));
		$gateway->expects($this->once())
			->method('createCustomer')
			->with(['email' => 'foobar'])
			->willReturn($customerRequest);

		$adapter = new StripeAdapter();
		$adapter->setGateway($gateway);

		$request->method('send')->willReturn($response);
		$gateway->method('purchaseMonthly')->willReturn($request);
		$response->method('isSuccessful')->willReturn(false);
		$response->method('getMessage')->willReturn('ohnoe');

		$donation = new DonationEntity();
		$donation->email = 'foobar';
		$result = $adapter->processMonthly($donation);
		$this->assertInstanceOf(PaymentResultEntity::class,$result);
		$this->assertSame($result->errors,['ohnoe']);
	}
}
