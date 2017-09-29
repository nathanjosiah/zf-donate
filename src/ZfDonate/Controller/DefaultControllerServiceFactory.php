<?php
namespace ZfDonate\Controller;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;

class DefaultControllerServiceFactory implements FactoryInterface {
	/**
	 * ZF2 Compatibility
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $container) {
		return $this($container,'');
	}

	public function __invoke(\Interop\Container\ContainerInterface $controllers,$requested_name,array $options = null) {
		$container = (is_subclass_of($controllers,\Zend\ServiceManager\ServiceManager::class) ? $controllers->getServiceLocator() : $controllers);

		$config = $container->get('Config')['zf-donate'];
		$event_manager = new EventManager($container->get('SharedEventManager'));
		$event_manager->setIdentifiers([DefaultController::class]);

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