<?php

namespace RezKit\Tours\Middleware;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use Joomla\CMS\Cache\Cache as JoomlaCache;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Cache
{

	private JoomlaCache $cache;

	public function __construct(int $ttl)
	{
		$this->cache = new JoomlaCache([]);
		$this->cache->setCaching($ttl > 0);
		$this->cache->setLifeTime($ttl);
	}

	public function __invoke(callable $next): callable
	{
		return function (RequestInterface $request, array $options) use (&$next) {
			$key = $this->getCacheKey($request);

			$cachedResponse = $this->cache->get($key, 'tour_manager:graphql');

			if ($cachedResponse !== false) {
				return new FulfilledPromise($cachedResponse);
			}

			/** @var Promise $promise */
			$promise = $next($request, $options);

			return $promise->then(
				function (ResponseInterface $response) use ($request, $key) {
					$this->cache->store($response, $key, 'tour_manager:graphql');
					return $response;
				}
			);
		};
	}

	public function getCacheKey(RequestInterface $request): string
	{
		$bodySum = hash('sha256', $request->getBody()->getContents());
		$key = $request->getMethod() . $request->getRequestTarget() . $bodySum;

		return $key;
	}
}
