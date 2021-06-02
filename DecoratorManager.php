<?php

require_once('DataProvider.php');

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Cache\InvalidArgumentException;

class DecoratorManager extends DataProvider {
	
	/** @var CacheItemPoolInterface */
	private $cache;
	/** @var LoggerInterface */
	private $logger;
	
	/**
	 * DecoratorManager constructor.
	 *
	 * @param string                 $host
	 * @param string                 $user
	 * @param string                 $password
	 * @param CacheItemPoolInterface $cache
	 * @param LoggerInterface        $logger
	 */
	public function __construct(
		string $host,
		string $user,
		string $password,
		CacheItemPoolInterface $cache,
		LoggerInterface $logger
	) {
		$this->cache = $cache;
		$this->logger = $logger;
		parent::__construct(
			$host,
			$user,
			$password
		);
	}
	
	/**
	 * @param array $input
	 * @return false|string
	 */
	private function getCacheKey(array $input): ?string {
		return json_encode($input);
	}
	
	/**
	 * @param array $data
	 * @return CacheItemInterface
	 */
	private function createCacheItem(array $data): CacheItemInterface {
		// @TODO(high): реализовать создание объекта класса, который имеет интерфейс CacheItemInterface
		//$cacheItem->set($result)
		//		  ->expiresAt(
		//			  (new DateTime())->modify('+1 day')
		//		  );
	}
	
	/**
	 * @param array $input
	 * @return CacheItemInterface|null
	 */
	private function getCacheItem(array $input): ?CacheItemInterface {
		try {
			$cacheKey = $this->getCacheKey($input);
			$cacheItem = $this->cache->getItem($cacheKey);
		} catch (InvalidArgumentException $e) {
			// Если кэш отсуствует, нет необходимости его логировать
			return null;
		}
		
		return $cacheItem;
	}
	
	/**
	 * @param array $input
	 * @return array
	 */
	public function getResponse(array $input): array {
		/** @var CacheItemInterface $cacheItem */
		$cacheItem = $this->getCacheItem($input);
		if (isset($cacheItem) && $cacheItem->isHit()) {
			return $cacheItem->get();
		}
		
		$result = parent::get($input);
		
		$cacheItem = $this->createCacheItem($result);
		if (!$this->cache->save($cacheItem)) {
			$this->logger->error('Failed to save cache');
		}
		
		return $result;
	}
}