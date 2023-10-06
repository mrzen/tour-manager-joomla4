<?php

namespace RezKit\Tours\GeoIP;

use DateTimeImmutable;
use DateTimeInterface;
use GuzzleHttp\Client;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\Filesystem\File;
use Joomla\CMS\Uri\Uri;
use RuntimeException;

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
        $lastUpdated = $this->getLastUpdated();
        if (!$lastUpdated) {
            return true;
        }

        $url = $this->getDownloadUri()->toString();
        $head = $this->http->head($url);

        $lastModified = current($head->getHeader('last-modified'));
        $lastModified = DateTimeImmutable::createFromFormat(DateTimeInterface::RFC7231, $lastModified);

        return $lastModified > $lastUpdated;
    }

    public function getLastUpdated(): ?DateTimeInterface
    {
        $updated = $this->cache->get('last_updated', 'rezkit:geoip:update');

        if ($updated === false) {
            return null;
        }

        return new DateTimeImmutable('@' . $updated);
    }

    /**
     * Perform an update on the GeoIP database
     */
    public function updateDatabase(): void
    {
        $uri = $this->getDownloadUri();
        $tempPath = Factory::getApplication()->getConfig()->get('tmp_path');
        $tempName = tempnam($tempPath, 'geoip_db_') . '.tar.gz';

        $response = $this->http->get($uri->toString());
        $data = $response->getBody()->getContents();

        file_put_contents($tempName, $data);

        // There's not much we can do to avoid using popen and the `tar` command.
        // Every alternative is either bad wrapper, or broken.
        $entries = explode("\n", `tar tzf $tempName`);

        $databaseFileName = array_filter($entries, static function ($x) {
            return str_ends_with($x, '.mmdb');
        });

        if (!count($databaseFileName)) {
            Log::add('Downloaded a GeoIP update with no database included', Log::ERROR);
            File::delete($tempName);
            return;
        }

        $databaseFileName = current($databaseFileName);

        // Extract the database file itself
        shell_exec("tar -C $tempPath -xvzf $tempName $databaseFileName");
        $databaseFile = $tempPath . '/' . $databaseFileName;
        $targetFile = JPATH_ROOT . '/' . $this->params['database_path'];

        if (!File::move($databaseFile, $targetFile)) {
            throw new RuntimeException("Unable to move $databaseFile to $targetFile");
        }

        File::delete($tempName);

        $this->cache->store(time(), 'last_updated', 'rezkit:geoip:update');
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