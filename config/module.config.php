<?php
return [
	/*
	 * post -> controller -> (donationentity <- formadapter <- form) -> gatewayadapter then donationentity -> storage adapter
	 */

	'zf-donate' => [
		'form' => ZfDonate\Form\DefaultForm::class,
		'controller' => ZfDonate\Controller\DefaultController::class,
		'entity' => ZfDonate\Model\DonationEntity::class,
		'storage_adapter' => ZfDonate\Model\Adapter\StorageAdapterInterface::class,
		'form_adapter' => ZfDonate\Model\Adapter\FormAdapter::class,
		'view_options' => [
			'views' => [
				'form' => 'zfdonate/page/form',
				'thank_you' => 'zfdonate/page/thank-you',
			],
		],
		'gateway' => [
			'adapter' => ZfDonate\Gateway\Adapter\Stripe::class,
			'options' => [
				'api_key' => 'abc123',
				'test_mode' => false,
			],
		],
	]
];