<?php

class DataProvider {
	
	/** @var string */
	private $host;
	/** @var string */
	private $user;
	/** @var string */
	private $password;
	
	/**
	 * DataProvider constructor.
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 */
	public function __construct(
		string $host,
		string $user,
		string $password
	) {
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
	}
	
	/**
	 * @param array $request
	 * @return array
	 */
	public function get(array $request): array {
		// returns a response from external service
	}
}

