<?php
namespace ZfDonateTest\Controller\TestAsset;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\View\Model\ViewModel;
use ZfDonate\Controller\DefaultController;
use ZfDonate\Event\DonationEvent;

class EventListenerSpy extends AbstractListenerAggregate  {
	private $events;

	public function attach(EventManagerInterface $events, $priority = 1) {
		$events->attach(DefaultController::EVENT_PROCESS_DONATION,[$this,'onProcess']);
		$events->attach(DefaultController::EVENT_STORE_DONATION,[$this,'onStore']);
		$events->attach(DefaultController::EVENT_FINISH,[$this,'onFinish']);
	}

	public function onProcess(DonationEvent $event) {
		$this->events[DefaultController::EVENT_PROCESS_DONATION] = clone $event;
	}
	public function onStore(DonationEvent $event) {
		$this->events[DefaultController::EVENT_STORE_DONATION] = clone $event;
	}
	public function onFinish(DonationEvent $event) {
		$this->events[DefaultController::EVENT_FINISH] = clone $event;
		$event->stopPropagation();
		return new ViewModel(['myresult'=>'yep']);
	}
	public function hasEvent(string $name) : bool {
		return isset($this->events[$name]);
	}

	public function getEvent(string $name) {
		return $this->events[$name];
	}
}