<?php /**Created by Кирилл kirill.ruh@gmail.com 20.11.2020 16:56 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(__FILE__) . '/define.php';
require_once ROOT_DIR . 'require.php';

$ip = $ipBin = '';
if (!empty($_GET['ip'])) {
	$ip = $_GET['ip'];
	if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
		require_once PATH_IPV4;
		$ipv = new Ipv4;
		$ipBin = inet_pton($ip);
	}
	elseif(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		require_once PATH_IPV6;
		$ipv = new Ipv6;
		$ipBin = inet_pton($ip);
	}
	else {
		include PATH_ERROR;
	}

	if (!empty($ipBin)) {
		try {
			$countryName = (new Geo())->getCountryName($ip);
		}
		catch (Exception $e) {
			$countryName = $e->getMessage();
		}
		try {
			$network = $ipv->getMin($ipBin);
		}
		catch (Exception $e) {
			$network = $e->getMessage();
		}
		include PATH_RESULT;
	}
}
include PATH_FORM;
