<?php

namespace RezKit\Tours;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Http\Http;
use Joomla\CMS\Http\HttpFactory;
use JsonException;
use Nyholm\Psr7\Uri;

class Search
{
	private Http $http;

	private string $endpoint;

	public function __construct(private string $apiKey, ?string $endpoint) {
		$this->endpoint = $endpoint ?? Client::DEFAULT_ENDPOINT;
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
	 * @param   array|string  $query  Search query parameters
	 *
	 * @return array Search Response
	 * @throws JsonException
	 * @since 1.1.0
	 */
	public function search(array|string $query): array
	{
		$searchUrl = (new Uri($this->endpoint))
			->withPath('/holidays/search');

		if (is_array($query)) {
			$query = http_build_query($query);
		}

		$searchUrl = $searchUrl->withQuery($query);

		$headers = [
			'Authorization' => 'Bearer ' . $this->apiKey,
			'Accept' => 'application/json',
		];

		$response = $this->http->get((string)$searchUrl, $headers);
		return json_decode($response->body, associative: true, flags: JSON_THROW_ON_ERROR);
	}
}
