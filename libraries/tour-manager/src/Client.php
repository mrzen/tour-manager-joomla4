<?php

namespace RezKit\Tours;

use Joomla\CMS\Component\ComponentHelper;
use Softonic\GraphQL\Client as GraphQLClient;
use Softonic\GraphQL\ClientBuilder;

class Client
{
	public const DEFAULT_ENDPOINT = "https://tours.api.rezkit.app/graphql";

    public static function create(): GraphQLClient
    {
		$params = ComponentHelper::getParams('com_ke');
		$endpoint = $params->get('endpoint', self::DEFAULT_ENDPOINT);
		$apiKey = $params->get('api_key');

		return ClientBuilder::build(
			$endpoint,
			[
				'headers' => [
					'Authorization' => "Bearer $apiKey"
				]
			]
		);
    }
}
