<?php

class WalletApi {
	private $path, $clientId, $clientSecret;
	public function __construct() {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/_sysSet.class.php';
		$set = new SysSet();
		$this->path = $set->getValue('path');
		$this->clientId = $set->getValue('client_id_yandex');
		$this->clientSecret = $set->getValue('client_secret_yandex');
	}
	function getCode() {
		$paramsYandex = array(
			'client_id'     => $this->clientId,
			'redirect_uri'  => $this->path . '/yandexLogin.php',
			'response_type' => 'code',
			'state'         => '123'
		);
		$urlYandex = 'https://oauth.yandex.ru/authorize?' . urldecode(http_build_query($paramsYandex));
		return ("Location: " . $urlYandex);
	}

	function getToken($code) {
		$params = array(
			'grant_type'    => 'authorization_code',
			'code'          => $code,
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret
		);
		$ch = curl_init('https://oauth.yandex.ru/token');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data, true);
		return $data['access_token'];
	}

	function getWalletData() {
		$ch = curl_init('https://login.yandex.ru/info');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('format' => 'json')); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$info = curl_exec($ch);
		curl_close($ch);
		$info = json_decode($info, true);
		return $info;
	}
}
?>