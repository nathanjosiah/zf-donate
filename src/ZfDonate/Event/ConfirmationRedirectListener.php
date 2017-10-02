<?php
namespace ZfDonate\Event;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use ZfDonate\Controller\DefaultController;

class ConfirmationRedirectListener extends AbstractListenerAggregate {
	protected $zfdonateConfig;

	public function __construct(array $zfdonateConfig) {
		$this->zfdonateConfig = $zfdonateConfig;
	}

	public function attach(EventManagerInterface $events, $priority = 1) {
		$events->attach(DefaultController::EVENT_FINISH,[$this,'redirect'],1000);
	}

	public function redirect(DonationEvent $donationEvent) {
		$donation = $donationEvent->getDonationEntity();

		// Stop further events
		$donationEvent->stopPropagation();

		return $donationEvent->getTarget()->Redirect()->toRoute($this->zfdonateConfig['routes']['confirmation'],[],['query' => [
			'amount' => $donation->amount,
			'fname' => $donation->firstName,
			'lname' => $donation->lastName,
			'email' => $donation->email,
		]]);
	}

	public function getConfig() : array {
		return $this->zfdonateConfig;
	}
}