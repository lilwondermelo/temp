<?php
class Application {
        public $error;
	private $code;
        private $idManager;
        private $uuid;
        private $userName;
        private $phone = '79963814070'; //Не забыть убрать, dev-значение

        public function __construct() {

        }

	public function sendCode($phone) {

                require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowUpdater.class.php';
                $updater = new DataRowUpdater('dir_users');
        	$updater->setKey('phone', $phone);
                $updater->setDataFields(array('code' => $this->code));
                $result = $updater->update();
                if (!$result) {
                        $this->error = $updater->error;
                        return $this->error;
                }
        	return $result;
	}
	public function loginCheck($phone) {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowSource.class.php';
                $dataRow = new DataRowSource('select code from dir_users where phone="' . $phone . '"');
                $html = '';
                if (!$dataRow->getData()) {
                        $this->code = random_int(1000, 9999);
                }
                else {
                        $this->code = $dataRow->getValue('code');
                }
                $sendCode = $this->sendCode($phone);
                return $sendCode;
        	}
        public function checkCode($phone, $code) {
        	require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowSource.class.php';
                $dataRow = new DataRowSource('select code from dir_users where phone="' . $phone . '" and code="' . $code . '"');
                if (!$dataRow->getData()) {
                        return false;
                }
                return true;
	}

        public function createUuid() {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_uuidClass.class.php';
                $this->idManager = new UUIDClass();
                $this->uuid = $this->idManager->v4();
                return $this->uuid;
        }
        function getUserName() {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowSource.class.php';
                $dataRow = new DataRowSource('select name from data_users where phone="' . $this->phone . '"');
                if (!$dataRow->getData()) {
                        $this->error = $dataRow->error;
                        return false;
                }
                $this->userName = $dataRow->getValue('name');
                return $this->userName;
        }
        function getUserId() {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowSource.class.php';
                $dataRow = new DataRowSource('select id from data_users where phone=' . $this->phone);

                if (!$dataRow->getData()) {
                        $this->error = $dataRow->error;
                        return false;
                }
                $this->userId = $dataRow->getValue('id');
                return $this->userId;
        }

}
?>