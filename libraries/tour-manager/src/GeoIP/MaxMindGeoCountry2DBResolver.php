<?php

namespace RezKit\Tours\GeoIP;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\Country;
use MaxMind\Db\Reader\InvalidDatabaseException;

class MaxMindGeoCountry2DBResolver implements Resolver
{
	private Reader $reader;

	/**
	 * @throws InvalidDatabaseException
	 * @since 0.1.0
	 */
	public function __construct(string $dbPath)
	{
		$this->reader = new Reader($dbPath);
	}

	public function country(string $ip): ?string
	{
		return $this->reader->country($ip)->country->isoCode;
	}

	public function city(string $ip): ?string
	{
		// Country2 DB doesn't support City lookup
		return null;
	}

	/**
	 * Resolve an IP
	 * @param   string  $ip
	 *
	 * @return ?Country
	 * @throws InvalidDatabaseException
	 * @throws AddressNotFoundException
	 * @since 0.1.0
	 */
	public function resolve(string $ip): ?Country
	{
		return $this->reader->country($ip);
	}
}
