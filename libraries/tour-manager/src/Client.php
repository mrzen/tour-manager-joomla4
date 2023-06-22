<?php

namespace RezKit\Tours;

use Joomla\CMS\Component\ComponentHelper;
use Softonic\GraphQL\Client as GraphQLClient;
use Softonic\GraphQL\ClientBuilder;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

class Client
{
	public const DEFAULT_ENDPOINT = "https://tours.api.rezkit.app/graphql";

    public static function create(): GraphQLClient
    {
		$params = ComponentHelper::getParams('com_ke');
		$endpoint = $params->get('apiendpoint', self::DEFAULT_ENDPOINT);
		$apiKey = $params->get('apikey');

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
