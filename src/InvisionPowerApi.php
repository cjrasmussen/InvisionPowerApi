<?php
namespace cjrasmussen\InvisionPowerApi;

use RuntimeException;

class InvisionPowerApi
{
	private $token;
	private $api_url;

	public function __construct($token, $community_url)
	{
		$this->token = $token;
		$this->api_url = trim($community_url, ' /') . '/api/';
	}

	/**
	 * Make a request to the Invision Power Board API
	 *
	 * @param string $type
	 * @param string $request
	 * @param array $args
	 * @return mixed
	 * @throws RuntimeException
	 */
	public function request($type, $request, array $args = [])
	{
		if (!is_array($args)) {
			$args = [$args];
		}

		$url = $this->api_url . $request;

		if ((count($args)) AND ($type === 'GET')) {
			$url .= '?' . http_build_query($args);
		}

		$c = curl_init();
		curl_setopt($c, CURLOPT_HEADER, 0);
		curl_setopt($c, CURLOPT_VERBOSE, 0);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($c, CURLOPT_USERPWD, $this->token);
		curl_setopt($c, CURLOPT_URL, $url);

		if ((count($args)) AND ($type !== 'GET')) {
			curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($args));
		}

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

		$response = curl_exec($c);
		curl_close($c);

		// DECODE THE RESPONSE INTO A GENERIC OBJECT
		$data = json_decode($response);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new RuntimeException('API response was not valid JSON');
		}

		return $data;
	}
}