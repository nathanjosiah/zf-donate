<?php
namespace ZfDonateTest\Controller;

use Zend\EventManager\EventManager;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\SharedEventManager;
use Zend\Form\Form;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;
use Zend\ServiceManager\ServiceManager;
use ZfDonate\Controller\DefaultController;
use ZfDonate\Controller\DefaultControllerServiceFactory;
use ZfDonate\Event\ConfirmationEmailEventListener;
use ZfDonate\Event\ConfirmationRedirectListener;
use ZfDonate\Model\Adapter\FormAdapterInterface;
use ZfDonate\Model\Adapter\StorageAdapterInterface;
use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\DonationGateway;
use ZfDonate\Payment\PaymentFactory;
use ZfDonateTest\Controller\TestAsset\ControllerStub;

class DefaultControllerServiceFactoryTest extends \PHPUnit\Framework\TestCase {
	public function testDependenciesAreCreatedProperly() {
		$factory = new DefaultControllerServiceFactory();
		$shared_event_manager = new SharedEventManager();
		$redirect_listener_mock = $this->getMockBuilder(ListenerAggregateInterface::class)->getMock();
		$email_listener_mock = $this->getMockBuilder(ListenerAggregateInterface::class)->getMock();
		$form_adapter = $this->getMockBuilder(FormAdapterInterface::class)->getMock();
		$storage_adapter = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();
		$payment_factory_mock = $this->getMockBuilder(PaymentFactory::class)->disableOriginalConstructor()->getMock();
		$donation_gateway = $this->getMockBuilder(DonationGateway::class)->disableOriginalConstructor()->getMock();
		$form = new Form();
		$entity = new DonationEntity();

		// Configure our super custom donate module
		$config = [
			'form_adapter' => 'myformadapter',
			'storage_adapter' => 'mystorageadapter',
			'form' => 'myform',
			'entity' => 'myentity',
			'controller' => ControllerStub::class,
		];

		// Setup the Service Manager with our super custom stuff
		$service_manager = new ServiceManager([
			'services' => [
				'Config' => [
					'zf-donate' => $config,
				],
				ConfirmationEmailEventListener::class => $email_listener_mock,
				ConfirmationRedirectListener::class => $redirect_listener_mock,
				PaymentFactory::class => $payment_factory_mock,
				'SharedEventManager' => $shared_event_manager,
				'myformadapter' => $form_adapter,
				'mystorageadapter' => $storage_adapter,
				'myentity' => $entity,
			]
		]);
		$service_manager->setService('FormElementManager', new FormElementManagerV3Polyfill($service_manager,['services'=>['myform'=>$form]]));


		// Assert default listeners are attached
		$email_listener_mock->expects($this->once())->method('attach');
		$redirect_listener_mock->expects($this->once())->method('attach');
		$payment_factory_mock->expects($this->once())->method('createGateway')->with('default')->willReturn($donation_gateway);

		// Create it!
		$result = $factory($service_manager,DefaultController::class);


		// Assert correct controller was created
		$this->assertInstanceOf(ControllerStub::class,$result);

		// Assert controller was given proper number of arguments
		$args = $result->getConstructorArgs();
		$this->assertCount(7,$args);

		// Assert controller was given proper arguments
		$this->assertSame($donation_gateway,$args[0]);
		$this->assertSame($form_adapter,$args[1]);
		$this->assertSame($storage_adapter,$args[2]);
		$this->assertSame($entity,$args[3]);
		$this->assertSame($form,$args[4]);
		$this->assertInstanceOf(EventManager::class,$args[5]);
		$this->assertSame($config,$args[6]);

		$event_manager = $args[5];
		// Make sure listeners can attach using controller name
		$this->assertContains(DefaultController::class,$event_manager->getIdentifiers());
	}

	public function testDependenciesAreCreatedProperly_StorageAdapterIsOptional() {
		$factory = new DefaultControllerServiceFactory();
		$shared_event_manager = new SharedEventManager();
		$redirect_listener_mock = $this->getMockBuilder(ListenerAggregateInterface::class)->getMock();
		$email_listener_mock = $this->getMockBuilder(ListenerAggregateInterface::class)->getMock();
		$form_adapter = $this->getMockBuilder(FormAdapterInterface::class)->getMock();
		$payment_factory_mock = $this->getMockBuilder(PaymentFactory::class)->disableOriginalConstructor()->getMock();
		$donation_gateway = $this->getMockBuilder(DonationGateway::class)->disableOriginalConstructor()->getMock();
		$form = new Form();
		$entity = new DonationEntity();

		// Configure our super custom donate module
		$config = [
			'form_adapter' => 'myformadapter',
			'storage_adapter' => null,
			'form' => 'myform',
			'entity' => 'myentity',
			'controller' => ControllerStub::class,
		];

		// Setup the Service Manager with our super custom stuff
		$service_manager = new ServiceManager([
			'services' => [
				'Config' => [
					'zf-donate' => $config,
				],
				ConfirmationEmailEventListener::class => $email_listener_mock,
				ConfirmationRedirectListener::class => $redirect_listener_mock,
				PaymentFactory::class => $payment_factory_mock,
				'SharedEventManager' => $shared_event_manager,
				'myformadapter' => $form_adapter,
				'myentity' => $entity,
			]
		]);
		$service_manager->setService('FormElementManager', new FormElementManagerV3Polyfill($service_manager,['services'=>['myform'=>$form]]));


		// Assert default listeners are attached
		$email_listener_mock->expects($this->once())->method('attach');
		$redirect_listener_mock->expects($this->once())->method('attach');
		$payment_factory_mock->expects($this->once())->method('createGateway')->with('default')->willReturn($donation_gateway);

		// Create it!
		$result = $factory($service_manager,DefaultController::class);


		// Assert correct controller was created
		$this->assertInstanceOf(ControllerStub::class,$result);

		// Assert controller was given proper number of arguments
		$args = $result->getConstructorArgs();
		$this->assertCount(7,$args);

		// Assert controller was given proper arguments
		$this->assertSame($donation_gateway,$args[0]);
		$this->assertSame($form_adapter,$args[1]);
		$this->assertNull($args[2]);
		$this->assertSame($entity,$args[3]);
		$this->assertSame($form,$args[4]);
		$this->assertInstanceOf(EventManager::class,$args[5]);
		$this->assertSame($config,$args[6]);
	}
}
