<?php
namespace ZfDonateTest\Controller;

use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;
use ZfDonate\Controller\DefaultController;
use ZfDonate\Model\Adapter\FormAdapterInterface;
use ZfDonate\Model\Adapter\StorageAdapterInterface;
use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\DonationGateway;
use ZfDonate\Payment\PaymentResultEntity;
use ZfDonateTest\Controller\TestAsset\EventListenerSpy;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase {
	public function testDonate_FirstVisit() {
		$config = [
			'views' => [
				'form' => 'mytemplate',
			]
		];
		$donationGateway = $this->getMockBuilder(DonationGateway::class)->disableOriginalConstructor()->getMock();
		$formAdapter = $this->getMockBuilder(FormAdapterInterface::class)->getMock();
		$storageAdapter = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();
		$donationEntity = new DonationEntity();
		$form = new Form();
		$eventManager = new EventManager();
		$controller = new DefaultController($donationGateway,$formAdapter,$storageAdapter,$donationEntity,$form,$eventManager,$config);

		$request = new Request();
		$event = new MvcEvent();
		$event->setRouteMatch(new RouteMatch(['action'=>'index']));
		$controller->setEvent($event);

		// Assert form default state is set
		$formAdapter->expects($this->once())->method('setDefaultData')->with($form,$request,$donationEntity);

		$result = $controller->dispatch($request,new Response());

		$this->assertInstanceOf(ViewModel::class,$result);
		$this->assertSame($form,$result->getVariable('form'));
		$this->assertSame('mytemplate',$result->getTemplate());
	}

	public function testDonate_SubmissionWithErrors() {
		$config = [
			'views' => [
				'form' => 'mytemplate',
			]
		];
		$donationGateway = $this->getMockBuilder(DonationGateway::class)->disableOriginalConstructor()->getMock();
		$formAdapter = $this->getMockBuilder(FormAdapterInterface::class)->getMock();
		$storageAdapter = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();
		$donationEntity = new DonationEntity();
		$form = $this->getMockBuilder(Form::class)->getMock();
		$eventManager = new EventManager();
		$controller = new DefaultController($donationGateway,$formAdapter,$storageAdapter,$donationEntity,$form,$eventManager,$config);

		$request = new Request();
		$request->setMethod(Request::METHOD_POST);
		$request->setPost(new Parameters(['mydata'=>'foo']));
		$event = new MvcEvent();
		$event->setRouteMatch(new RouteMatch(['action'=>'index']));
		$controller->setEvent($event);

		// Assert form default state is NOT set
		$formAdapter->expects($this->never())->method('setDefaultData');
		// Assert nothing else happens
		$formAdapter->expects($this->never())->method('hydrateEntity');
		$formAdapter->expects($this->once())->method('hydrateForm')->with($form,$donationEntity,['mydata'=>'foo']);
		$form->expects($this->once())->method('isValid')->willReturn(false);

		$result = $controller->dispatch($request,new Response());

		$this->assertInstanceOf(ViewModel::class,$result);
		$this->assertSame($form,$result->getVariable('form'));
		$this->assertSame('mytemplate',$result->getTemplate());
	}

	public function testDonate_ValidSubmission_PaymentGatewayError() {
		$config = [
			'views' => [
				'form' => 'mytemplate',
			]
		];
		$donationGateway = $this->getMockBuilder(DonationGateway::class)->disableOriginalConstructor()->getMock();
		$formAdapter = $this->getMockBuilder(FormAdapterInterface::class)->getMock();
		$storageAdapter = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();
		$donationEntity = new DonationEntity();
		$form = $this->getMockBuilder(Form::class)->getMock();
		$eventManager = new EventManager();
		$listener = new EventListenerSpy();
		$listener->attach($eventManager);
		$controller = new DefaultController($donationGateway,$formAdapter,$storageAdapter,$donationEntity,$form,$eventManager,$config);

		$request = new Request();
		$request->setMethod(Request::METHOD_POST);
		$request->setPost(new Parameters(['mydata'=>'foo']));
		$event = new MvcEvent();
		$event->setRouteMatch(new RouteMatch(['action'=>'index']));
		$controller->setEvent($event);

		$donation_result = new PaymentResultEntity();
		$donation_result->errors = ['oh noe'];
		// Assert form default state is NOT set
		$formAdapter->expects($this->never())->method('setDefaultData');
		$formAdapter->expects($this->once())->method('hydrateEntity')->with($form,$donationEntity);
		$formAdapter->expects($this->once())->method('hydrateForm')->with($form,$donationEntity,['mydata'=>'foo']);
		$form->expects($this->once())->method('getMessages')->willReturn([]);
		$form->expects($this->once())->method('setMessages')->with(['cc'=>['number' => ['oh noe']]]);
		$donationGateway->expects($this->once())->method('processDonation')->with($donationEntity)->willReturn($donation_result);
		$form->expects($this->once())->method('isValid')->willReturn(true);

		$result = $controller->dispatch($request,new Response());

		$this->assertInstanceOf(ViewModel::class,$result);
		$this->assertSame($form,$result->getVariable('form'));
		$this->assertSame('mytemplate',$result->getTemplate());

		$this->assertTrue($listener->hasEvent(DefaultController::EVENT_PROCESS_DONATION),'Process event was not triggered.');
		$this->assertSame($donationEntity,$listener->getEvent(DefaultController::EVENT_PROCESS_DONATION)->getDonationEntity());
		$this->assertSame($form,$listener->getEvent(DefaultController::EVENT_PROCESS_DONATION)->getDonationForm());

		$this->assertTrue($listener->hasEvent(DefaultController::EVENT_STORE_DONATION),'Store event was not triggered.');
		$this->assertSame($donationEntity,$listener->getEvent(DefaultController::EVENT_STORE_DONATION)->getDonationEntity());
		$this->assertSame($form,$listener->getEvent(DefaultController::EVENT_STORE_DONATION)->getDonationForm());

		$this->assertFalse($listener->hasEvent(DefaultController::EVENT_FINISH),'Finish event was triggered.');
	}

	public function testDonate_ValidSubmission_NoErrors() {
		$config = [
			'views' => [
				'form' => 'mytemplate',
			]
		];
		$donationGateway = $this->getMockBuilder(DonationGateway::class)->disableOriginalConstructor()->getMock();
		$formAdapter = $this->getMockBuilder(FormAdapterInterface::class)->getMock();
		$storageAdapter = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();
		$donationEntity = new DonationEntity();
		$form = $this->getMockBuilder(Form::class)->getMock();
		$eventManager = new EventManager();
		$listener = new EventListenerSpy();
		$listener->attach($eventManager);
		$controller = new DefaultController($donationGateway,$formAdapter,$storageAdapter,$donationEntity,$form,$eventManager,$config);

		$request = new Request();
		$request->setMethod(Request::METHOD_POST);
		$request->setPost(new Parameters(['mydata'=>'foo']));
		$event = new MvcEvent();
		$event->setRouteMatch(new RouteMatch(['action'=>'index']));
		$controller->setEvent($event);

		$donation_result = new PaymentResultEntity();
		$formAdapter->expects($this->never())->method('setDefaultData');
		$formAdapter->expects($this->once())->method('hydrateEntity')->with($form,$donationEntity);
		$formAdapter->expects($this->once())->method('hydrateForm')->with($form,$donationEntity,['mydata'=>'foo']);
		$form->expects($this->never())->method('getMessages');
		$form->expects($this->never())->method('setMessages');
		$donationGateway->expects($this->once())->method('processDonation')->with($donationEntity)->willReturn($donation_result);
		$form->expects($this->once())->method('isValid')->willReturn(true);

		$result = $controller->dispatch($request,new Response());
		$this->assertInstanceOf(ViewModel::class,$result);
		$this->assertSame('yep',$result->getVariable('myresult'));

		$this->assertTrue($listener->hasEvent(DefaultController::EVENT_PROCESS_DONATION),'Process event was not triggered.');
		$this->assertSame($donationEntity,$listener->getEvent(DefaultController::EVENT_PROCESS_DONATION)->getDonationEntity());
		$this->assertSame($form,$listener->getEvent(DefaultController::EVENT_PROCESS_DONATION)->getDonationForm());

		$this->assertTrue($listener->hasEvent(DefaultController::EVENT_STORE_DONATION),'Store event was not triggered.');
		$this->assertSame($donationEntity,$listener->getEvent(DefaultController::EVENT_STORE_DONATION)->getDonationEntity());
		$this->assertSame($form,$listener->getEvent(DefaultController::EVENT_STORE_DONATION)->getDonationForm());

		$this->assertTrue($listener->hasEvent(DefaultController::EVENT_FINISH),'Finish event was not triggered.');
		$this->assertSame($donationEntity,$listener->getEvent(DefaultController::EVENT_FINISH)->getDonationEntity());
		$this->assertSame($form,$listener->getEvent(DefaultController::EVENT_FINISH)->getDonationForm());
	}

	public function testConfirmationAction() {
		$config = [
			'views' => [
				'thank_you' => 'mytemplate',
			]
		];
		$donationGateway = $this->getMockBuilder(DonationGateway::class)->disableOriginalConstructor()->getMock();
		$formAdapter = $this->getMockBuilder(FormAdapterInterface::class)->getMock();
		$storageAdapter = $this->getMockBuilder(StorageAdapterInterface::class)->getMock();
		$donationEntity = new DonationEntity();
		$form = new Form();
		$eventManager = new EventManager();
		$controller = new DefaultController($donationGateway,$formAdapter,$storageAdapter,$donationEntity,$form,$eventManager,$config);

		$request = new Request();
		$request->setQuery(new Parameters([
			'amount' => '12.34',
			'fname' => 'John',
			'lname' => 'Doe',
			'email' => 'myemail',
		]));
		$event = new MvcEvent();
		$event->setRouteMatch(new RouteMatch(['action'=>'confirmation']));
		$controller->setEvent($event);
		$result = $controller->dispatch($request,new Response());

		$this->assertInstanceOf(ViewModel::class,$result);
		$this->assertSame('12.34',$result->getVariable('amount'));
		$this->assertSame('John',$result->getVariable('first_name'));
		$this->assertSame('Doe',$result->getVariable('last_name'));
		$this->assertSame('myemail',$result->getVariable('email'));
		$this->assertSame('mytemplate',$result->getTemplate());
	}
}
