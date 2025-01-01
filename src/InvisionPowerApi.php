<?php

namespace cjrasmussen\InvisionPowerApi;

use RuntimeException;

class InvisionPowerApi
{
	private string $token;
	private string $api_url;
	private ?string $lastResponseHeader = null;

	public function __construct($token, $community_url)
	{
		$this->token = $token;
		$this->api_url = trim($community_url, ' /') . '/api/';
	}

	/**
	 * Get the response header from the most recent API request
	 *
	 * @return ?string
	 */
	public function getLastResponseHeader(): ?string
	{
		return $this->lastResponseHeader;
	}

	/**
	 * Make a request to the Invision Power Board API
	 *
	 * @param string $type
	 * @param string $request
	 * @param array $args
	 * @return ?object
	 * @throws RuntimeException
	 * @throws \JsonException
	 */
	public function request(string $type, string $request, array $args = []): ?object
	{
		$url = $this->api_url . trim($request, ' /');

		if (($type === 'GET') && (count($args))) {
			$url .= '?' . http_build_query($args);
		}

		$c = curl_init();
		curl_setopt($c, CURLOPT_HEADER, 1);
		curl_setopt($c, CURLOPT_VERBOSE, 0);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($c, CURLOPT_USERPWD, $this->token);
		curl_setopt($c, CURLOPT_URL, $url);

		switch ($type) {
			case 'POST':
				curl_setopt($c, CURLOPT_POST, 1);
				break;
			case 'GET':
				curl_setopt($c, CURLOPT_HTTPGET, 1);
				break;
			default:
				curl_setopt($c, CURLOPT_CUSTOMREQUEST, $type);
		}

		if (($type !== 'GET') && (count($args))) {
			curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($args));
		} elseif ($type === 'POST') {
			curl_setopt($c, CURLOPT_POSTFIELDS, null);
		}

		$response = curl_exec($c);
		$header_length = curl_getinfo($c, CURLINFO_HEADER_SIZE);
		curl_close($c);

		if (!$response) {
			return null;
		}

		$this->lastResponseHeader = substr($response, 0, $header_length);
		$body = substr($response, $header_length);

		// DECODE THE RESPONSE INTO A GENERIC OBJECT
		return json_decode($body, false, 512, JSON_THROW_ON_ERROR);
	}
}