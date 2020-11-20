<?php /**Created by Кирилл kirill.ruh@gmail.com 20.11.2020 18:21 */

class Cache {
	private static $_self = null;
	/**
	 * @var Memcache
	 */
	private $_cache = null;
	private $_time = 0;

	final private function __construct() {
		$var = include PATH_VARS;
		$this->_time = $var['cachetime'];
		$this->_cache = new Memcache();
		try {
			$this->_cache->connect($var['cachehost'], $var['cacheport'], 1);
		}
		catch (Exception $e) {
//			$this->_cache = null;
			throw new Exception('Не удалось включить мемкеш');
		}
	}

	function __destruct() {
		if ($this->_cache !== null) $this->_cache->close();
	}

	function read($key) {
		if ($this->_cache === null) return false;
		return $this->_cache->get($key);
	}

	function write($key, $value, $time = null) {
		if ($this->_cache === null) return false;
		if ($time === null) $time = $this->_time;
		return $this->_cache->set($key, $value, MEMCACHE_COMPRESSED, $time);
	}

	static function get() {
		if (self::$_self === null) self::$_self = new Cache();
		return self::$_self;
	}

}