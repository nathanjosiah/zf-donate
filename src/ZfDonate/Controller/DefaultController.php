<?php

namespace ZfDonate\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class DefaultController extends AbstractActionController {

	public function donateAction() {
		$form_entity = new DonationEntity();
		$this->donateForm->bind($form_entity);

		$this->donateForm->setAttribute('action',$this->url()->fromRoute('donate'));
		$request = $this->getRequest();
		$this->donateForm->processOptionsFromQuery($request->getQuery());

		if($request->isPost()) {
			$this->donateForm->setData($request->getPost());
			if($this->donateForm->isValid()) {
				$response = $this->donationGateway->processDonation($donation);
				$this->donationTable->save($donation);

				if($response->errors) {
					$messages = $this->donateForm->getMessages();
					$messages['cc'] = [
						'number' => $response->errors,
					];
					$this->donateForm->setMessages($messages);
				}
				else {
					$line_items = [];
					$line_items[] = 'Donation: $' . number_format($recipt_donation,2);
					$line_items[] = 'Total transaction amount: $' . number_format($recipt_total,2);

					$organization = $this->organizationsTable->fetchWithId($this->identity->organizationId);
					$message = new \Zend\Mail\Message();
					$message->setTo($donation->email);
					$message->setFrom('noreply@ubergive.com');
					$message->setSubject('Receipt for your transaction with ' . $organization->name);
					$message->setBody('This is your receipt for your transaction with ' . $organization->name . '.

' . implode("\n",$line_items) . '

Blessings from the ' . $organization->name . ' team!');

					$this->mailTransport->send($message);

					return $this->Redirect()->toRoute('thank-you',[],['query' => [
						'total' => $recipt_total,
						'donation' => $recipt_donation,
						'products' => $recipt_products
					]]);
				}

			}
		}
		else {
			$this->donateForm->setData([
				'recurrence' => DonationEntity::RECUR_NONE
			]);
		}

		$vm = new ViewModel([
			'page_title' => 'Donate',
			'form' => $this->donateForm,
			'app_config' => $this->appConfig,
			'products' => $dbproducts
		]);
		$vm->setTemplate('page/app/donate');
		return $vm;
	}

	public function thankYouAction() {
		$vm = new ViewModel([
			'app_config' => $this->appConfig,
		]);
		$vm->setTemplate('page/app/thank-you');
		return $vm;
	}
}

