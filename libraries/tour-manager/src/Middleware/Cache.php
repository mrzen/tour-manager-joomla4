<?php

namespace RezKit\Tours\Middleware;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use Joomla\CMS\Cache\Cache as JoomlaCache;
use Joomla\CMS\Profiler\Profiler;
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

			$cacheItem = $this->cache->get($key, 'tour_manager:graphql');

			if ($cacheItem !== false) {
				$data = json_decode($cacheItem, true, 8, JSON_THROW_ON_ERROR);

				$response = new Response($data['status'], $data['headers'], $data['body']);
				Profiler::getInstance('Application')->mark('Tour Manger GraphQL Query (Cached)');

				return $response;
			}

			/** @var Promise $promise */
			$promise = $next($request, $options);

			return $promise->then(
				function (ResponseInterface $response) use ($request, $key) {
					$this->store($key, $response);

					Profiler::getInstance('Application')->mark('Tour Manger GraphQL Query');

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

	public function store(string $key, ResponseInterface $response): void
	{
		$data = [
			'headers' => $response->getHeaders(),
			'status' => $response->getStatusCode(),
			'body' => $response->getBody()->getContents(),
		];

		$this->cache->store(json_encode($data, JSON_PRETTY_PRINT), $key, 'tour_manager:graphql');
	}
}
