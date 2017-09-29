<?php
namespace ZfDonateTest\Payment\Gateway;

use ZfDonate\Payment\DonationGateway;
use ZfDonate\Payment\Adapter\AdapterInterface;
use ZfDonate\Payment\PaymentResultEntity;
use ZfDonate\Model\DonationEntity;

class DonationGatewayTest extends \PHPUnit_Framework_TestCase {

	public function testSingle() {
		$adapter = $this->getMockBuilder(AdapterInterface::class)->getMock();
		$result = new PaymentResultEntity();
		$result->code = 123;
		$result->message = 'yay!';
		$result->transactionId = 456;
		$result->avsResult = 'valid or whatever';
		$donation = new DonationEntity();
		$donation->recurrence = DonationEntity::RECUR_NONE;
		$donation->ccNumber = '4111111111111111';

		$gateway = new DonationGateway($adapter,'foo');

		// Assert donation is passed
		$adapter->expects($this->once())->method('processSingle')->with($donation)->willReturn($result);

		$real_result = $gateway->processDonation($donation);

		$this->assertSame($result,$real_result);
		$this->assertSame('visa',$donation->ccType);
		$this->assertSame('1111',$donation->ccLastFour);
		$this->assertSame(123,$donation->gatewayCode);
		$this->assertSame('yay!',$donation->gatewayMessage);
		$this->assertSame(456,$donation->gatewayTransactionId);
		$this->assertSame('valid or whatever',$donation->gatewayAvsResult);
		$this->assertSame('foo',$donation->gatewayType);
	}

	public function testMonthly() {
		$adapter = $this->getMockBuilder(AdapterInterface::class)->getMock();
		$result = new PaymentResultEntity();
		$result->code = 123;
		$result->message = 'yay!';
		$result->transactionId = 456;
		$result->avsResult = 'valid or whatever';
		$donation = new DonationEntity();
		$donation->recurrence = DonationEntity::RECUR_MONTHLY;
		$donation->ccNumber = '4111111111111111';

		$gateway = new DonationGateway($adapter,'foo');

		// Assert donation is passed
		$adapter->expects($this->once())->method('processMonthly')->with($donation)->willReturn($result);

		$real_result = $gateway->processDonation($donation);

		$this->assertSame($result,$real_result);
		$this->assertSame('visa',$donation->ccType);
		$this->assertSame('1111',$donation->ccLastFour);
		$this->assertSame(123,$donation->gatewayCode);
		$this->assertSame('yay!',$donation->gatewayMessage);
		$this->assertSame(456,$donation->gatewayTransactionId);
		$this->assertSame('valid or whatever',$donation->gatewayAvsResult);
		$this->assertSame('foo',$donation->gatewayType);
	}

	public function testGetters() {
		$adapter = $this->getMockBuilder(AdapterInterface::class)->getMock();
		$gateway = new DonationGateway($adapter,'foo');

		$this->assertSame($adapter,$gateway->getAdapter());
		$this->assertSame('foo',$gateway->getGatewayType());
	}
}

