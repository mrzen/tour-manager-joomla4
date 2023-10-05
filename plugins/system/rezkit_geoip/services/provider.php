<?php

use GeoIp2\Database\Reader;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use RezKit\Tours\Plugins\GeoIP\Extension\GeoIP;

return new class() implements ServiceProviderInterface {
	/**
	 * Register container services
	 *
	 * @param   Container  $container
	 * @return void
	 * @since 0.1.0
	 */
	public function register(Container $container): void
	{
		$container->set(Reader::class,
			static function (Container $container) {
				return null;
			}
		);

		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$subject = $container->get(DispatcherInterface::class);

				$plugin = new GeoIP(
					$subject,
					(array) PluginHelper::getPlugin('system', 'rezkit_geoip'),
				);

				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			}
		);
	}
};
