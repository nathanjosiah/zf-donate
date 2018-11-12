<?php
namespace ZfDonateTest\Payment;

use Zend\ServiceManager\ServiceManager;
use ZfDonate\Payment\PaymentFactory;
use ZfDonate\Payment\PaymentFactoryServiceFactory;

class PaymentFactoryServiceFactoryTest extends \PHPUnit\Framework\TestCase {
	public function testFactory() {
		$service_manager = new ServiceManager();
		$factory = new PaymentFactoryServiceFactory();
		$instance = $factory($service_manager,PaymentFactory::class);

		$this->assertInstanceOf(PaymentFactory::class,$instance);
		$this->assertSame($service_manager,$instance->getServiceLocator());
	}
}
