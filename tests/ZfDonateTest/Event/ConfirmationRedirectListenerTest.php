<?php
namespace ZfDonateTest\Event;

use Zend\EventManager\EventManager;
use ZfDonate\Controller\DefaultController;
use ZfDonate\Event\ConfirmationRedirectListener;
use ZfDonate\Event\DonationEvent;
use ZfDonate\Model\DonationEntity;

class ConfirmationRedirectListenerTest extends \PHPUnit\Framework\TestCase {
	public function testRedirectsWithCorrectInfo() {
		$donation = new DonationEntity();
		$donation->firstName = 'John';
		$donation->lastName = 'Doe';
		$donation->email = 'foobar';
		$donation->amount = 12.34;

		$event = new DonationEvent();
		$event->setDonationEntity($donation);
		$event->setName(DefaultController::EVENT_FINISH);
		$model = new \stdClass();
		$controller_mock = new ControllerMock($model);
		$event->setTarget($controller_mock);

		$listener = new ConfirmationRedirectListener([
			'routes' => [
				'confirmation' => 'myroute',
			]
		]);

		$event_manager = new EventManager();
		$listener->attach($event_manager);
		$result = $event_manager->triggerEvent($event);


		$this->assertTrue($result->stopped());
		$this->assertSame($result->last(),$model);
		$this->assertSame($controller_mock->getArguments()[0],'myroute');
		$this->assertSame($controller_mock->getArguments()[1],[]);
		$this->assertSame($controller_mock->getArguments()[2],[
			'query' => [
				'amount' => 12.34,
				'fname' => 'John',
				'lname' => 'Doe',
				'email' => 'foobar',
			]
		]);
	}
}
