<?php

use GeoIp2\Database\Reader;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use RezKit\Tours\GeoIP\MaxMindGeoCountry2DBResolver;
use RezKit\Tours\GeoIP\Resolver;
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

		$container->set(MaxMindGeoCountry2DBResolver::class, function (Container $container) {
			$params = (array)PluginHelper::getPlugin('system', 'rezkit_geoip');

			return new MaxMindGeoCountry2DBResolver($params['database_path']);
		});

		$container->tag(Resolver::class, [MaxMindGeoCountry2DBResolver::class]);

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
