<?php /**Created by Кирилл kirill.ruh@gmail.com 20.11.2020 16:52 */
class SqlDriver {
	private $_self;

	function __construct() {
		$var = include PATH_VARS;
		$this->_self = new mysqli($var['mysqlhost'], $var['mysqllogin'], $var['mysqlpassword'], $var['mysqldbname']);
		if ($this->_self->connect_errno) {
			throw new Exception('Could not select database');
		}
		$this->_self->set_charset('utf8');
	}

	private function _query($sql) {
		$result = $this->_self->query($sql);
		return $result;
	}

	function getNet($ipBin, $ipv = 4) {
		$sql = ''.
			'select network_ip, mask '.
			'from ' . (($ipv === 4)?'network_ips':'network_ips6') . ' '.
			'where (' . $this->_mest($ipBin) . ' between network_ip and network_end) '.
			'order by mask desc '.
			'limit 1 '.
		'';
		$key = md5($sql);
		$row = Cache::get()->read($key);
		if ($row === false) {
			$result = $this->_query($sql);
			if ($result) {
				$row = $result->fetch_assoc();
				$result->free();
				Cache::get()->write($key, $row);
			}
			else {
				throw new Exception('Request error ' . $this->_self->error, $sql, $this->_self->errno);
			}
		}
		return $row;
	}

	private function _mest($s) {
		return "'" . $this->_self->real_escape_string($s) . "'";
	}

}
