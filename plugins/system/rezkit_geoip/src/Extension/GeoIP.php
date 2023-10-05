<?php

namespace RezKit\Tours\Plugins\GeoIP\Extension;

use Joomla\CMS\Console\Loader\WritableLoaderInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\DI\Container;
use Joomla\Event\SubscriberInterface;
use Joomla\Session\Session;
use Psr\Container\ContainerInterface;
use RezKit\Tours\Cli\UpdateGeoIPDatabaseCommand;
use RezKit\Tours\GeoIP\MaxMindGeoCountry2DBResolver;
use RezKit\Tours\GeoIP\Resolver;

class GeoIP extends CMSPlugin implements SubscriberInterface
{
	use TaskPluginTrait;

	private const TASKS_MAP = [
		'tour_manager.geoip_update' => [
			'langConstPrefix' => 'PLG_REZKIT_GEOIP_TASK_UPDATE',
			'method' => 'updateGeoIPDatabase',
			'form' => 'update_geoip_database'
		]
	];

	public const SESSION_KEY_COUNTRY = 'rezkit.geoip.country';
	public const SESSION_KEY_CURRENCY = 'rezkit.geoip.currency';

	public static function getSubscribedEvents(): array
	{
		return [
			'onTaskOptionsList' => 'advertiseRoutines',
			'onAfterInitialise' => 'handleRequest'
		];
	}

	/**
	 * Handle an incoming request
	 *
	 * @return void
	 * @since 0.1.0
	 */
	public function handleRequest(): void
	{
		$container = Factory::getContainer();

		$container->alias('rezkit.geoip.resolver', MaxMindGeoCountry2DBResolver::class)
			->share(MaxMindGeoCountry2DBResolver::class, function (Container $container) {
					$params = (array)PluginHelper::getPlugin('system', 'rezkit_geoip');
					$path = json_decode($params, true)['database_path'];
					return new MaxMindGeoCountry2DBResolver($path);
			}, true);


		$container->share(
			'rezkit.geoip.update',
			static function (ContainerInterface $container) {
				return new UpdateGeoIPDatabaseCommand();
			},
			true
		);

		Factory::getContainer()->get(WritableLoaderInterface::class)->add('rezkit:geoip:update', 'rezkit.geoip.update');

		/** @var Session $session */
		$session = $container->get('session');

		if (!$session->get(static::SESSION_KEY_COUNTRY)) {
			/** @var Resolver $resolver */
			$resolver = $container->get('rezkit.geoip.resolver');
			$ip = '127.0.0.1';
			$session->set(static::SESSION_KEY_COUNTRY, $resolver->country($ip));
		}

		if (!$session->get(static::SESSION_KEY_CURRENCY)) {
			$country = $session->get(static::SESSION_KEY_COUNTRY);
			$currency = $this->params->get('default_currency', 'USD');

			foreach ($this->params->get('mapping') as $mapping) {
				if ($mapping->country === $country) {
					$currency = $mapping->currency;
					break;
				}
			}

			$session->set(static::SESSION_KEY_CURRENCY, $currency);
		}
	}

	private function updateGeoIPDatabase(ExecuteTaskEvent $event): int
	{
		$uri = new Uri("https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key=YOUR_LICENSE_KEY&suffix=tar.gz");

		return Status::OK;
	}
}
