<?php

namespace RezKit\Tours;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Http\Http;
use Joomla\CMS\Http\HttpFactory;
use Joomla\Uri\Uri;
use Nyholm\Psr7\Request;

class Search
{
	private Http $http;

	private string $endpoint;

	public function __construct(private string $apiKey, ?string $endpoint) {
		$this->endpoiont = $endpoint ?? Client::DEFAULT_ENDPOINT;
		$this->http = HttpFactory::getHttp();
	}

	public static function create(): static
	{
		$params = ComponentHelper::getParams('com_rktours');
		$endpoint = $params->get('apiendpoint');
		$apiKey = $params->get('apikey');

		return new static($apiKey, $endpoint);
	}


	/**
	 * Run a search
	 *
	 * @param array|string $query Search query parameters
	 *
	 * @return array Search Response
	 * @since 1.1.0
	 */
	public function search(array|string $query): array
	{
		$searchUrl = new Uri($this->endpoint);
		$searchUrl->setPath('/holidays/search');
		$searchUrl->setQuery($query);

		$request = new Request(
			'GET',
			$searchUrl,
			[
				'Authorization' => 'Bearer ' . $this->apiKey,
			],
			null,
		);

		$this->http->sendRequest($request);

		$content = $request->getBody()->getContents();

		return json_decode($content, associative: true);
	}
}
