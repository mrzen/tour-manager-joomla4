<?php

namespace RezKit\Tours\GeoIP;

use GuzzleHttp\Client;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Uri\Uri;

/**
 * GeoIP Database Updater
 * 
 * Runner to check for and perform updates on MaxMind GeoIP databases.
 * 
 * @since 0.1.0
 */
final class DatabaseUpdater {

    public const DOWNLOAD_URL_BASE = 'https://download.maxmind.com/app/geoip_download';
    private Cache $cache;

    private Client $http;

    public function __construct(
        private array $params
    )
    {
        $this->cache = new Cache([
            'caching' => true,
            'lifetime' => 86400 * 7,
        ]);

        $this->http = new Client();
    }


    /**
     * Check if an update is required to the GeoIP database
     */
    public function requiresUpdate(): bool
    {
        // TODO: Check if an update is required
        return true;
    }

    /**
     * Perform an update on the GeoIP database
     */
    public function updateDatabase(): void
    {
        $uri = $this->getDownloadUri();

        $response = $this->http->get($uri->toString());
        $body = $response->getBody();

        $content = fopen('php://memory', 'w+b');

        while (!$body->eof()) {
            fwrite($content, $body->read(1024));
        }

        rewind($content);
        $header = gzread($content, 1024);

        dump($header);
    }


    public function getDownloadUri(): Uri
    {
        $uri = new Uri(static::DOWNLOAD_URL_BASE);
        $query = $uri->getQuery(true);

        $query['suffix'] = 'tar.gz';
        $query['edition_id'] = 'GeoLite2-Country';
        $query['license_key'] = $this->params['license_key'];

        $uri->setQuery($query);

        return $uri;
    }
}