<?php

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use RezKit\Plugins\System\RKTours\Extension\TourManagerPlugin;

require_once JPATH_LIBRARIES . '/tour-manager/vendor/autoload.php';

return new class() implements ServiceProviderInterface {

	public function register(Container $container)
	{
		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$subject = $container->get(DispatcherInterface::class);

				$plugin = new TourManagerPlugin(
					$subject,
					(array) PluginHelper::getPlugin('system', 'rktours'),
				);

				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			}
	}
};
