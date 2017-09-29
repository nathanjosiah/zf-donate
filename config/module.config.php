<?php
return [
	/*
	 * post -> controller -> (donationentity <- formadapter <- form) -> gatewayadapter then donationentity -> storage adapter
	 */
	'zf-donate' => [
		'form' => ZfDonate\Form\DonateForm::class,
		'controller' => ZfDonate\Controller\DefaultController::class,
		'entity' => ZfDonate\Model\DonationEntity::class,
		// Optional. Set to null to disable.
		'storage_adapter' => ZfDonate\Model\Adapter\TableStorageAdapter::class,
		'form_adapter' => ZfDonate\Model\Adapter\FormAdapter::class,
		'email' => [
			'transport' => 'SlmMail\Mail\Transport\MailgunTransport',
			'subject_line' => 'Thank you for your gift!',
			'from_name' => 'Your name here',
			'from_email' => 'yourverifiedemail@example.com',
		],
		'view_options' => [
			'views' => [
				'form' => 'zfdonate/page/form',
				'thank_you' => 'zfdonate/page/thank-you',
				'email' => 'zfdonate/email/receipt',
			],
		],
		'configurations' => [
			'default' => [
				'adapter' => 'Stripe',
				'options' => [
					'api_key' => 'sk_test_R7BQQgxmG4txeZfdRLvrBhH9',
					'monthly_plan_name' => 'my_plan',
					'test_mode' => true,
				],
			]
		],
		'adapters' => [
			'Stripe' => [
				'adapter' => ZfDonate\Payment\Adapter\StripeAdapter::class,
				'gateway' => ZfDonate\Payment\Gateway\Stripe\StripeGateway::class,
			],
			'Forte' => [
				'adapter' => ZfDonate\Payment\Adapter\ForteAdapter::class,
				'gateway' => ZfDonate\Payment\Gateway\Forte\HttpGateway::class,
			],
		]
	],
	'service_manager' => [
		'factories' => [
			ZfDonate\Payment\Adapter\StripeAdapter::class => Zend\ServiceManager\Factory\InvokableFactory::class,
			ZfDonate\Payment\Gateway\Stripe\StripeGateway::class => Zend\ServiceManager\Factory\InvokableFactory::class,

			ZfDonate\Payment\Adapter\ForteAdapter::class => Zend\ServiceManager\Factory\InvokableFactory::class,
			ZfDonate\Payment\Gateway\Forte\HttpGateway::class => Zend\ServiceManager\Factory\InvokableFactory::class,
		]
	],
	'form_elements' => [
		'factories' => [
			ZfDonate\Form\DonateForm::class => Zend\ServiceManager\Factory\InvokableFactory::class,
		]
	],
	'slm_mail' => [
		'mailgun' => [
			'domain' => '',
			'key' => '',
		]
	],
];