<?php

// Общесистемные установки
class SysSet {

    var $settingName = '', $settingValue = null, $error = '';

    //put your code here
    /**
     * 
     * @param type $setName
     */
    public function __construct($setName = null) {
        if ($setName) {
            $this->settingName = $setName;
            $this->settingValue = $this->getValue($this->settingName);
        }
    }

    /**
     * Записывает новое значение системной переменной. Если переменной нет - будет создана
     * @param string $setName - имя переменной
     * @param mixed $setValue - значение переменной
     * @return boolean - TRUE в случае успеха
     */
    public function setValue(string $setName = null, $setValue = null) {
        if ($setName) {
            $this->setName = $setName;
        }
        if (!is_null($setValue)) {
            $this->settingValue = $setValue;
        }
        if (!$this->settingName) {
            $this->error = 'Empty Set name';
            return FALSE;
        }
        require_once '_dataRowUpdater.class.php';
        $dataRow = new DataRowUpdater('sys_set');
        $dataRow->setDataFields(["sys_set_key" => $this->settingName, "sys_set_value" => $this->settingValue]);
        if ($dataRow->update()) {
            $this->error = $dataRow->error;
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Возвращает значение системной переменной
     * @param type $setName
     * @return mixed - значение переменной или FALSE если была ошибка
     */
    public function getValue($setName = null) {
        if ($setName) {
            $this->settingName = $setName;
        }
        require_once '_dataRowSource.class.php';
        $dataRow = new DataRowSource('select sys_set_value from sys_set where sys_set_key="' . $this->settingName . '"');
        if ($dataRow->getData()) {
            return $dataRow->getField('sys_set_value');
        } else {
            $this->error = $dataRow->error;
            return FALSE;
        }
    }

    /**
     * Возвращает URL хостинга
     */
    public static function getSiteUrl() {
        return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    }

}
