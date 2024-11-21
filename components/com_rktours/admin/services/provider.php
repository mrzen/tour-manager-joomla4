<?php

use Joomla\CMS\Dispatcher\ComponentDispatcherFactory;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
	public function register(Container $container):  void
	{
		$container->registerServiceProvider(new MVCFactory('RezKit\\Component\\Tours'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('RezKit\\Component\\Tours'));
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
