<?php

namespace ZfDonateTest\Event;

use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\View;
use ZfDonate\Controller\DefaultController;
use ZfDonate\Event\ConfirmationEmailEventListener;
use ZfDonate\Event\DonationEvent;
use ZfDonate\Model\DonationEntity;

class ConfirmationEmailEventListenerTest extends \PHPUnit_Framework_TestCase {
	public function testSendsEmailWithProperViewModel() {
		$transport = $this->getMockBuilder(TransportInterface::class)->getMock();
		$view = $this->getMockBuilder(View::class)->disableOriginalConstructor()->getMock();
		$config = [
			'views' => [
				'email' => 'myemail',
			],
			'email' => [
				'subject_line' => 'mysubject',
				'from_name' => 'myname',
				'from_email' => 'myfromemail@example.com',
			],
		];
		$donation = new DonationEntity();
		$donation->email = 'mytoemail@example.com';
		$form = new Form('myform');
		$event_manager = new EventManager();
		$event = new DonationEvent();
		$event->setName(DefaultController::EVENT_FINISH);
		$event->setDonationEntity($donation);
		$event->setDonationForm($form);

		// Create the listener
		$listener = new ConfirmationEmailEventListener($transport,$view,$config);
		$listener->attach($event_manager);

		// Assert view model was given correct data
		$view->expects($this->once())->method('render')->with($this->callback(function($view_model) use($donation,$form) {
			return (
				$view_model instanceof ViewModel
				&& $view_model->getTemplate() === 'myemail'
				&& $view_model->getVariable('donation_entity') === $donation
				&& $view_model->getVariable('donation_form') === $form
			);
		}))->willReturn('mybody');

		// Assert the message is composed correctly
		$transport->expects($this->once())->method('send')->with($this->callback(function($mail) {
			return (
				$mail instanceof Message
				&& $mail->getTo()->current()->getEmail() === 'mytoemail@example.com'
				&& $mail->getSubject() === 'mysubject'
				&& $mail->getFrom()->current()->getName() === 'myname'
				&& $mail->getFrom()->current()->getEmail() === 'myfromemail@example.com'
				&& $mail->getBody() === 'mybody'
			);;
		}));
		// Trigger the event
		$event_manager->triggerEvent($event);
	}
}