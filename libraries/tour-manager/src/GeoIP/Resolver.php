<?php

namespace RezKit\Tours\GeoIP;

interface Resolver
{
	/**
	 * Get Country
	 *
	 * Get the ISO country code associated with the given IP address
	 *
	 * @param  string  $ip
	 * @return string|null
	 * @since 0.1.0
	 */
	public function country(string $ip): ?string;

	/**
	 * Get City
	 *
	 * Get the name of the city associated with the given IP
	 *
	 * @param   string  $ip
	 * @return string|null
	 * @since 0.1.0
	 */
	public function city(string $ip): ?string;

	/**
	 * Resolve IP
	 *
	 * Resolve the given IP, getting full detailsk
	 *
	 * @template T
	 * @param   string  $ip
	 * @return T
	 * @since 0.1.0
	 */
	public function resolve(string $ip): mixed;
}
