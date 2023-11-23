<?php

namespace RezKit\Tours;

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use GuzzleHttp\HandlerStack;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Joomla\CMS\Component\ComponentHelper;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Softonic\GraphQL\Client as GraphQLClient;
use Softonic\GraphQL\ClientBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

class Client
{
	public const DEFAULT_ENDPOINT = "https://tours.api.rezkit.app/graphql";

    public static function create(): GraphQLClient
    {
		$params = ComponentHelper::getParams('com_rktours');
		$endpoint = $params->get('apiendpoint', self::DEFAULT_ENDPOINT);
		$apiKey = $params->get('apikey');
		$ttl = $params->get('cache_ttl',  900);

		$cacheDir = JPATH_ROOT . '/cache/tour_manager';

		$cacheHandler = DoctrineProvider::wrap(new FilesystemAdapter(self::class, $ttl, $cacheDir));
		$cacheStrategy = new GreedyCacheStrategy(new DoctrineCacheStorage($cacheHandler), $ttl);
		$middleware = new CacheMiddleware($cacheStrategy);
		$middleware->setHttpMethods(['POST','GET']);

		$stack = new HandlerStack();
		$stack->push($middleware);

		return ClientBuilder::build(
			$endpoint,
			[
				'headers' => [
					'Authorization' => "Bearer $apiKey"
				],
				'handler' => $stack,
			]
		);
    }
}
