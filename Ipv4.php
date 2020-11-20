<?php /**Created by Кирилл kirill.ruh@gmail.com 20.11.2020 16:56 */

class Ipv4 {
	protected $_v = 4;
	protected $_padLength = 8;
	protected $_sqlDriver;

	function __construct() {
		$this->_sqlDriver = new SqlDriver();
	}

	protected function _maskToBin ($mask) {
		$hex = str_repeat('f', floor($mask / 4));
		$hex .= dechex(4 * ($mask % 4));
		$hex = str_pad($hex, $this->_padLength, '0');
		return pack('H*', $hex);
	}

	function getNetwork($ipBin, $mask, $isEnd = false) {
		$ipParts = str_split($ipBin, 4);
		$maskParts = str_split($mask, 4);
		$bin = '';
		foreach ($ipParts as $i=>$part) {
			if ($isEnd) $bin .= $part | ~$maskParts[$i];
			else $bin .= $part & $maskParts[$i];
		}
		return $bin;
	}

	function getMin($ipBin) {
		$net = $this->_sqlDriver->getNet($ipBin, $this->_v);
		if (empty($net)) {
			throw new Exception('Сеть не найдена');
		}

		return inet_ntop($net['network_ip']) . '/' . $net['mask'];
	}

}