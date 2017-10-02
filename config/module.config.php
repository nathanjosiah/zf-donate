<?php
return [
	/*
	 * post -> controller -> (donationentity <- formadapter <- form) -> gatewayadapter then donationentity -> storage adapter
	 */
	'zf-donate' => [
		'form' => ZfDonate\Form\DonateForm::class,
		'controller' => ZfDonate\Controller\DefaultController::class,
		'entity' => ZfDonate\Model\DonationEntity::class,
		// Optional. Set to null to disable. Must be a ZfDonate\Model\Adapter\TableStorageAdapter
		'storage_adapter' => null,
		'form_adapter' => ZfDonate\Model\Adapter\DefaultFormAdapter::class,
		'routes' => [
			'page' => 'zfdonate-form',
			'confirmation' => 'zfdonate-thank-you',
		],
		'email' => [
			'transport' => 'SlmMail\Mail\Transport\MailgunTransport',
			'subject_line' => 'Thank you for your gift!',
			'from_name' => 'Your name here',
			'from_email' => 'yourverifiedemail@example.com',
		],
		'views' => [
			'form' => 'zfdonate/page/form',
			'thank_you' => 'zfdonate/page/thank-you',
			'email' => 'zfdonate/email/receipt',
		],
		'configurations' => [
			'default' => [
				'gateway' => 'Stripe',
				'options' => [
					'api_key' => 'sk_test_R7BQQgxmG4txeZfdRLvrBhH9',
					'monthly_plan_name' => 'my_plan',
					'test_mode' => true,
				],
			]
		],
		'gateways' => [
			'Stripe' => [
				'adapter' => ZfDonate\Payment\Adapter\StripeAdapter::class,
				'gateway' => ZfDonate\Payment\Gateway\Stripe\StripeGateway::class,
			],
		]
	],
	'service_manager' => [
		'factories' => [
			ZfDonate\Payment\Adapter\StripeAdapter::class => Zend\ServiceManager\Factory\InvokableFactory::class,
			ZfDonate\Payment\Gateway\Stripe\StripeGateway::class => Zend\ServiceManager\Factory\InvokableFactory::class,

			ZfDonate\Event\ConfirmationEmailEventListener::class => ZfDonate\Event\ConfirmationEmailEventListenerServiceFactory::class,
			ZfDonate\Event\ConfirmationRedirectListener::class => ZfDonate\Event\ConfirmationRedirectListenerServiceFactory::class,
			ZfDonate\Payment\PaymentFactory::class => ZfDonate\Payment\PaymentFactoryServiceFactory::class,
		]
	],
	'form_elements' => [
		'factories' => [
			ZfDonate\Form\DonateForm::class => Zend\ServiceManager\Factory\InvokableFactory::class,
		]
	],
	'controllers' => [
		'factories' => [
			ZfDonate\Controller\DefaultController::class => ZfDonate\Controller\DefaultController::class,
		]
	],
	'slm_mail' => [
		'mailgun' => [
			'domain' => '',
			'key' => '',
		]
	],
];