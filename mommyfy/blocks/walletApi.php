<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/walletApi.class.php';
$walletApi = new WalletApi();
if (!empty($_GET['code'])) {
	$token = $walletApi->getToken($_GET['code']);
}
else {
	$walletApi->getCode();
}
?>