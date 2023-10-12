<?php

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use RezKit\Tours\Plugins\Search\Plugin;

return new class() implements ServiceProviderInterface {

	public function register(Container $container): void
	{
		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$subject = $container->get(DispatcherInterface::class);

				$plugin = new Plugin(
					$subject,
					(array) PluginHelper::getPlugin('webservices', 'toursearch')
				);

				$plugin->setApplication(Factory::getApplication());
				return $plugin;
			}
		);
	}
};
