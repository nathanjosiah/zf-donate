<?php

namespace ZfDonateTest\Payment;

use ZfDonate\Payment\PaymentFactory;
use Zend\ServiceManager\ServiceManager;
use ZfDonate\Payment\Adapter\AdapterInterface;
use ZfDonate\Payment\Gateway\OptionsAwareInterface;
use Zend\ServiceManager\Config;

class PaymentFactoryTest extends \PHPUnit_Framework_TestCase {
	public function testGatewayIsConfiguredCorrectly() {
		$pgateway = $this->getMockBuilder(OptionsAwareInterface::class)->getMock();
		$adapter = $this->getMockBuilder(AdapterInterface::class)->getMock();
		$service_locator = new ServiceManager();
		$service_locator->setService('Config',[
			'zf-donate' => [
				'configurations' => [
					'mygateway' => [
						'gateway' => 'SomeAdapter',
						'options' => [
							'myoption' => 'abc123',
						],
					]
				],
				'gateways' => [
					'SomeAdapter' => [
						'adapter' => 'customadapter',
						'gateway' => 'customgateway',
					],
				]
			]
		]);
		$service_locator->setService('customgateway',$pgateway);
		$service_locator->setService('customadapter',$adapter);

		$factory = new PaymentFactory($service_locator);

		// Assert gateway is passed to adapter
		$adapter->expects($this->once())->method('setGateway')->with($pgateway);
		// Assert gateway is passed correct options
		$pgateway->expects($this->once())->method('setOptions')->with(['myoption' => 'abc123']);

		// Run test
		$gateway = $factory->createGateway('mygateway');
		$this->assertSame($adapter,$gateway->getAdapter());
		$this->assertSame('mygateway',$gateway->getGatewayType());
	}
}

