<?php
namespace ZfDonate\Controller;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfDonate\Event\ConfirmationEmailEventListener;
use ZfDonate\Event\ConfirmationRedirectListener;

class DefaultControllerServiceFactory implements FactoryInterface {
	public function __invoke(\Interop\Container\ContainerInterface $container,$requested_name,array $options = null) {
		$config = $container->get('Config')['zf-donate'];
		$event_manager = new EventManager($container->get('SharedEventManager'),[DefaultController::class]);

		// Attach the default listeners
		$container->get(ConfirmationEmailEventListener::class)->attach($event_manager);
		$container->get(ConfirmationRedirectListener::class)->attach($event_manager);

		$payment_factory = $container->get(\ZfDonate\Payment\PaymentFactory::class);
		// @TODO there needs to be a way to change this
		$gateway = $payment_factory->createGateway('default');

		$entity = $container->get($config['entity']);
		$form = $container->get('FormElementManager')->get($config['form']);
		$form_adapter = $container->get($config['form_adapter']);
		$storage_adapter = null;
		if(!empty($config['storage_adapter'])) {
			$storage_adapter = $container->get($config['storage_adapter']);
		}
		$controller_class = $config['controller'];
		$controller = new $controller_class($gateway,$form_adapter,$storage_adapter,$entity,$form,$event_manager,$config);
		return $controller;
	}
}