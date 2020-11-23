<?php /**Created by Кирилл kirill.ruh@gmail.com 20.11.2020 16:56 */

class Ipv4 {
	protected $_v = 4;
	protected $_padLength = 8;
	protected $_sqlDriver;
	protected $_localIp = [
		'10.10.0.0/8'=>['start'=>'', 'end'=>''],
		'172.16.0.0/12'=>['start'=>'', 'end'=>''],
		'192.168.0.0/16'=>['start'=>'', 'end'=>''],
		'127.0.0.0/8'=>['start'=>'', 'end'=>''],
	];

	function __construct() {
		$this->_sqlDriver = new SqlDriver();
		foreach ($this->_localIp as $net=>$v) {
			list($ip, $mask) = explode('/', $net);
			$ipBin = inet_pton($ip);
			$maskBin = $this->_maskToBin($mask);
			$this->_localIp[$net] = ['start'=>$this->_getNetwork($ipBin, $maskBin), 'end'=>$this->_getNetwork($ipBin, $maskBin, true)];
		}
	}

	protected function _maskToBin ($mask) {
		$hex = str_repeat('f', floor($mask / 4));
		$hex .= dechex(4 * ($mask % 4));
		$hex = str_pad($hex, $this->_padLength, '0');
		return pack('H*', $hex);
	}

	protected function _getNetwork($ipBin, $mask, $isEnd = false) {
		$ipParts = str_split($ipBin, 4);
		$maskParts = str_split($mask, 4);
		$bin = '';
		foreach ($ipParts as $i=>$part) {
			if ($isEnd) $bin .= $part | ~$maskParts[$i];
			else $bin .= $part & $maskParts[$i];
		}
		return $bin;
	}

	function checkLocal($ipBin) {
		foreach ($this->_localIp as $net=>$v) {
			if (($ipBin >= $v['start']) and ($ipBin <= $v['end'])) return true;
		}
		return false;
	}

	function getMin($ipBin) {
		$net = $this->_sqlDriver->getNet($ipBin, $this->_v);
		if (empty($net)) {
			throw new Exception('Сеть не найдена');
		}

		return inet_ntop($net['network_ip']) . '/' . $net['mask'];
	}

}