<?php
namespace ZfDonate\Controller;

use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use ZfDonate\Event\DonationEvent;
use ZfDonate\Model\Adapter\FormAdapterInterface;
use ZfDonate\Model\Adapter\StorageAdapterInterface;
use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\DonationGateway;

class DefaultController extends AbstractActionController {
	const EVENT_PROCESS_DONATION = 'process';
	const EVENT_STORE_DONATION = 'store';
	const EVENT_FINISH = 'finish';

	protected $entity,$form,$formAdapter,$storageAdapter,$donationGateway,$eventManager,$zfdonateConfig,$event;
	public function __construct(
		DonationGateway $donationGateway,
		FormAdapterInterface $formAdapter,
		?StorageAdapterInterface $storageAdapter,
		DonationEntity $entity,
		Form $form,
		EventManager $eventManager,
		array $zfdonateConfig
	) {
		$this->donationGateway = $donationGateway;
		$this->formAdapter = $formAdapter;
		$this->storageAdapter = $storageAdapter;
		$this->entity = $entity;
		$this->form = $form;
		$this->eventManager = $eventManager;
		$this->zfdonateConfig = $zfdonateConfig;

		$event = new DonationEvent();
		$event->setTarget($this);
		$event->setDonationEntity($this->entity);
		$event->setDonationForm($this->form);
		$this->event = $event;
	}

	public function donateAction() {
		//$this->form->setAttribute('action',$this->Url()->fromRoute($this->zfdonateConfig['routes']['page']));
		$request = $this->getRequest();

		if($request->isPost()) {
			$this->formAdapter->hydrateForm($this->form,$request->getPost());
			if($this->donateForm->isValid()) {
				$this->formAdapter->hydrateEntity($this->form,$this->entity);

				// Process the donation
				$this->event->setName(self::EVENT_PROCESS_DONATION);
				$this->eventManager->trigger($this->event);
				$response = $this->donationGateway->processDonation($this->entity);

				/*
				 * This event should always fire because this is the only place
				 * where the donation is fully processed.
				 */
				$this->event->setName(self::EVENT_STORE_DONATION);
				$this->eventManager->trigger($this->event);
				if($this->storageAdapter) {
					$this->storageAdapter->save($this->entity);
				}

				if($response->errors) {
					$messages = $this->form->getMessages();
					$messages['cc'] = [
						'number' => $response->errors,
					];
					$this->form->setMessages($messages);
				}
				else {
					// Allow custom return for controller
					$this->event->setName(self::EVENT_FINISH);
					$event_results = $this->eventManager->trigger($this->event);
					if($event_results->stopped()) {
						// Return result from event
						return $event_results->last();
					}

					// Default EVENT_FINISH redirects to the confirmation route
					return $this->Redirect()->toRoute($this->zfdonateConfig['routes']['confirmation'],[],['query' => [
						'donation' => $this->entity->amount,
						'fname' => $this->entity->firstName,
						'lname' => $this->entity->lastName,
						'email' => $this->entity->email,
					]]);
				}
			}
		}
		else {
			$this->formAdapter->setDefaultData($this->form,$request,$this->entity);
			/*$this->form->setData([
				'recurrence' => DonationEntity::RECUR_NONE
			]);*/
		}

		$vm = new ViewModel([
			'form' => $this->form,
		]);
		$vm->setTemplate($this->zfdonateConfig['views']['form']);
		return $vm;
	}

	public function confirmationAction() {
		$request = $this->getRequest();
		$vm = new ViewModel([
			'donation' => $request->getQuery('donation'),
			'first_name' => $request->getQuery('first_name'),
			'last_name' => $request->getQuery('last_name'),
			'email' => $request->getQuery('email'),
		]);
		$vm->setTemplate($this->zfdonateConfig['views']['thank_you']);
		return $vm;
	}
}

