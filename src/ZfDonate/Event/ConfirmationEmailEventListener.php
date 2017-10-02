<?php
namespace ZfDonate\Event;


use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\View\Model\ViewModel;
use Zend\View\View;
use ZfDonate\Controller\DefaultController;

class ConfirmationEmailEventListener extends AbstractListenerAggregate {
	protected $transport,$zfdonateConfig,$view;

	public function __construct(TransportInterface $transport,View $view,array $zfdonateConfig) {
		$this->transport = $transport;
		$this->view = $view;
		$this->zfdonateConfig = $zfdonateConfig;
	}

	public function attach(EventManagerInterface $events, $priority = 1) {
		$events->attach(DefaultController::EVENT_FINISH,[$this,'sendEmail'],-10);
	}

	public function sendEmail(DonationEvent $donationEvent) {
		$donation = $donationEvent->getDonationEntity();
		$email_config = $this->zfdonateConfig['email'];

		// Render the email
		$view_model = new ViewModel([
			'donation_entity' => $donation,
			'donation_form' => $donationEvent->getDonationForm()
		]);
		$view_model->setTemplate($this->zfdonateConfig['views']['email']);
		$email_body = $this->view->render($view_model);

		// Send the email
		$message = new \Zend\Mail\Message();
		$message->setTo($donation->email);
		$message->setFrom($email_config['from_email'],$email_config['from_name']);
		$message->setSubject($email_config['subject_line']);
		$message->setBody($email_body);

		$this->transport->send($message);
	}
}