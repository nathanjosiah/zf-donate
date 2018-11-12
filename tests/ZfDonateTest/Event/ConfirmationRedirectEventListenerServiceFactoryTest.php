<?php
namespace ZfDonateTest\Event;

use Zend\ServiceManager\ServiceManager;
use ZfDonate\Event\ConfirmationRedirectListener;
use ZfDonate\Event\ConfirmationRedirectListenerServiceFactory;

class ConfirmationRedirectEventListenerServiceFactoryTest extends \PHPUnit\Framework\TestCase {
	public function testFactory() {
		$config = [
			'my-settings' => 'these-are-them',
		];
		$service_manager = new ServiceManager([
			'services' => [
				'Config' => [
					'zf-donate' => $config
				]
			]
		]);

		$factory = new ConfirmationRedirectListenerServiceFactory();
		$instance = $factory($service_manager,ConfirmationRedirectListener::class);

		$this->assertInstanceOf(ConfirmationRedirectListener::class,$instance);
		$this->assertSame($config,$instance->getConfig());
	}
}
