<?php

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
	const COMPONENT_NAMESPACE = 'RezKit\\Component\\RKTours';
	public function register(Container $container):  void
	{
		$container->registerServiceProvider(new MVCFactory(static::COMPONENT_NAMESPACE));
		$container->registerServiceProvider(new ComponentDispatcherFactory(static::COMPONENT_NAMESPACE));
		$container->registerServiceProvider(new RouterFactory(static::COMPONENT_NAMESPACE));
		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component  = new MVCComponent($container->get(ComponentDispatcherFactoryInterface::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));

				return $component;
			}
		);
	}
};
