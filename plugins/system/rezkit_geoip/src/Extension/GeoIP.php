<?php

namespace RezKit\Tours\Plugins\GeoIP\Extension;

use Joomla\Application\ApplicationEvents;
use Joomla\CMS\Console\Loader\WritableLoaderInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\DI\Container;
use Joomla\Event\SubscriberInterface;
use Joomla\Session\Session;
use RezKit\Tours\Cli\UpdateGeoIPDatabaseCommand;
use RezKit\Tours\GeoIP\DatabaseUpdater;
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
			'onAfterInitialise' => 'handleRequest',
			ApplicationEvents::BEFORE_EXECUTE => 'configureServices',
		];
	}

	public function configureServices(): void
	{
		$container = Factory::getContainer();
		$container->share(
			UpdateGeoIPDatabaseCommand::class,
			static function (Container $container) {
				return new UpdateGeoIPDatabaseCommand();
			},
			true
		)->alias('rezkit.geoip.update', UpdateGeoIPDatabaseCommand::class);

		$container->get(WritableLoaderInterface::class)
			->add('rezkit:geoip:update', UpdateGeoIPDatabaseCommand::class);

		$container->share(
			DatabaseUpdater::class,
			function (Container $container) {
				$params = json_decode(PluginHelper::getPlugin('system', 'rezkit_geoip')->params, true);
				return new DatabaseUpdater($params);
			},
			true
		)->alias('rezkit.geoip.updater', DatabaseUpdater::class);
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
					$path = json_decode($params['params'], true)['database_path'];
					return new MaxMindGeoCountry2DBResolver(JPATH_ROOT . '/' . $path);
			}, true);

		/** @var Session $session */
		$session = $container->get('session');

		if (!$session->get(static::SESSION_KEY_COUNTRY)) {
			/** @var Resolver $resolver */
			$resolver = $container->get('rezkit.geoip.resolver');
			
			if ($resolver) {
				$session->set(static::SESSION_KEY_COUNTRY, $resolver->country($ip));
			}
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
		/** @var DatabaseUpdater $updater */
		$updater = Factory::getContainer()->get(DatabaseUpdater::class);

		if ($updater->requiresUpdate()) {
			$updater->updateDatabase();
		}

		return Status::OK;
	}
}
