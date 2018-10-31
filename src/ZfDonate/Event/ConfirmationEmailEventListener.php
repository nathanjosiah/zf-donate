<?php
namespace ZfDonate\Event;


use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface;
use ZfDonate\Controller\DefaultController;

class ConfirmationEmailEventListener extends AbstractListenerAggregate {
	protected $transport,$zfdonateConfig,$view;

	public function __construct(TransportInterface $transport,RendererInterface $view,array $zfdonateConfig) {
		$this->transport = $transport;
		$this->view = $view;
		$this->zfdonateConfig = $zfdonateConfig;
	}

	public function attach(EventManagerInterface $events, $priority = 1) {
		$events->attach(DefaultController::EVENT_FINISH,[$this,'sendEmail'],10);
	}

	public function sendEmail(DonationEvent $donationEvent) {
		$donation = $donationEvent->getDonationEntity();
		$email_config = $this->zfdonateConfig['email'];

		if(empty($email_config['enabled'])) {
			return;
		}

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

		$htmlPart = new \Zend\Mime\Part($email_body);
		$htmlPart->type = 'text/html';
		$body = new \Zend\Mime\Message();
		$body->setParts([$htmlPart]);
		$message->setBody($body);

		try {
			$this->transport->send($message);
		}
		catch(\Exception $exception) {
			// Log or something?
		}
	}

	public function getTransport() : TransportInterface {
		return $this->transport;
	}

	public function getView() : RendererInterface {
		return $this->view;
	}

	public function getConfig() : array {
		return $this->zfdonateConfig;
	}
}