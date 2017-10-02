<?php
namespace ZfDonateTest\Event;

use Zend\Mail\Transport\TransportInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\View;
use ZfDonate\Event\ConfirmationEmailEventListener;
use ZfDonate\Event\ConfirmationEmailEventListenerServiceFactory;

class ConfirmationEmailEventListenerServiceFactoryTest extends \PHPUnit_Framework_TestCase {
	public function testFactory() {
		$transport = $this->getMockBuilder(TransportInterface::class)->getMock();
		$view = $this->getMockBuilder(View::class)->disableOriginalConstructor()->getMock();
		$config = [
			'email' => [
				'transport' => 'mytransport',
			]
		];
		$service_manager = new ServiceManager([
			'services' => [
				'mytransport' => $transport,
				'View' => $view,
				'Config' => [
					'zf-donate' => $config
				]
			]
		]);

		$factory = new ConfirmationEmailEventListenerServiceFactory();
		$instance = $factory($service_manager,ConfirmationEmailEventListener::class);

		$this->assertInstanceOf(ConfirmationEmailEventListener::class,$instance);
		$this->assertSame($config,$instance->getConfig());
		$this->assertSame($transport,$instance->getTransport());
		$this->assertSame($view,$instance->getView());
	}
}
