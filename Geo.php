<?php /**Created by Кирилл kirill.ruh@gmail.com 20.11.2020 16:58 */
use GeoIp2\Database\Reader;
class Geo {
	private $_geo;
	function __construct() {
		$this->_geo = new Reader(GEO_DB);
	}

	function getCountryName($ip) {
		$counntryName = Cache::get()->read($ip);
		if ($counntryName === false) {
			$record = $this->_geo->country($ip);
			$counntryName = $record->country->name;
			Cache::get()->write($ip, $counntryName);
		}
		return $counntryName;
	}
}