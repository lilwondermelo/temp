<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DataRowSource {

    private $query, $keyField, $result = [], $findResult = [], $rowsCount = 0, $fieldsCount = 0, $fieldsNames = [], $queryRez;
    var $error;

    public function __construct($queryText = null) {
        if ($queryText)
            $this->query = $queryText;
    }

    /**
     * Устанавливает текст запроса
     * @param String $queryText - текст запроса
     */
    public function setQuery($queryText) {
        $this->query = $queryText;
    }

    /**
     * Получает массив данных и сохраняет в свойство $result
     * @return boolean - TRUE если данные получены и FALSE если нет. При ошибке запроса, текст ошибки сохраняется в свойстве error
     */
    public function getData() {
        $this->error = '';
        require_once '_dataConnector.class.php';
        $db = new DataConnector();
        if (!$db->sqlConnect()) {
            $this->error = $db->error;
            return FALSE;
        }
        $db_query = $db->sqlQuery();
        if (!$db_query) {
            $this->error = $db->error;
            return FALSE;
        }

        $this->queryRez = $db_query->query($this->query);
        if ($db_query->errno > 0) {
            $this->error = $db_query->error;
            return FALSE;
        }
        $this->result = $this->queryRez->fetch_array(MYSQLI_ASSOC);
        $this->rowsCount = $this->queryRez->num_rows;

        if ($this->rowsCount > 0) {
            $this->fieldsCount = count($this->result);
            $step = 0;
            foreach ($this->result as $key => $value) {
                $this->fieldsNames[$step] = $key;
                $step++;
            }
        }
        $db->sqlClose();
        if ($this->rowsCount > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Получает массив данных или null
     * @return array - массив данных, если все успешно 
     * @return NULL - если  пустой массив 
     */
    public function getDataRow() {
        if (count($this->result) == 0) {
            //вызовем гетдату
            if (!$this->getData()) {
                return false;
            }
        }
        if (count($this->result) > 0) {
            return $this->result;
        } else {
            return NULL;
        }
    }

    /**
     * метод возвращает название поля по номеру или null
     * @param integer $fieldNumber - передается номер поля
     * @return NULL - если не найдено
     * @return string - название поля
     */
    public function getFieldName($fieldNumber) {
        if ($fieldNumber > $this->fieldsCount) {
            return NULL;
        }
        $row = $this->result;
        $step = 0;
        foreach ($row as $key => $value) {
            if ($step === $fieldNumber) {
                return $key;
            }
            $step++;
        }
        return NULL;
    }

    /**
     * метод возвращает значение поля по имени или null
     * @param string $fieldName - передается номер поля
     * @return NULL - если не найдено
     * @return string - название поля
     */
    public function getField($fieldName) {
        if (!$this->result) {
            return NULL;
        }
        if (!array_key_exists($fieldName, $this->result)) {
            return NULL;
        }
        return $this->result[$fieldName];
    }

    /**
     * метод возвращает номер поля по bvtyb или null
     * @param integer $fieldNumber - передается номер поля
     * @return NULL - если не найдено
     * @return int - номер поля
     */
    public function getFieldNumber($fieldName) {
        $row = $this->result;
        $step = 0;
        foreach ($row as $key => $value) {
            if ($key === $fieldName) {
                return $step;
            }
            $step++;
        }
        return NULL;
    }

    /**
     * метод возвращает названия полей результата запроса
     * @return array
     */
    public function getFieldsNames() {
        return $this->fieldsNames;
    }

    /**
     * метод возвращает число полей массива $result 
     * @return int число полей выборки
     */
    public function getFieldsCount() {
        return $this->fieldsCount;
    }

    /**
     * Метод возвращает тип поля
     * @param string $fieldName
     * @return string
     */
    public function getFieldType($fieldName) {
        $field_data = $this->queryRez->fetch_field_direct($this->getFieldNumber($fieldName));
        $mysql_data_type_hash = array(
            1 => 'tinyint',
            2 => 'smallint',
            3 => 'int',
            4 => 'float',
            5 => 'double',
            7 => 'timestamp',
            8 => 'bigint',
            9 => 'mediumint',
            10 => 'date',
            11 => 'time',
            12 => 'datetime',
            13 => 'year',
            16 => 'bit',
            //252 is currently mapped to all text and blob types (MySQL 5.0.51a)
            253 => 'varchar',
            254 => 'char',
            246 => 'decimal',
            252 => 'text'
        );
        //return $field_data->type;
        if (array_key_exists($field_data->type, $mysql_data_type_hash)) {
            return $mysql_data_type_hash[$field_data->type];
        } else {
            return NULL;
        }
    }

    /**
     * Метод возвращает длину поля из базы
     * @param string $fieldName
     * @return int 
     */
    public function getFieldLength($fieldName) {
        $field_data = $this->queryRez->fetch_field_direct($this->getFieldNumber($fieldName));
        if (in_array($this->getFieldType($fieldName), ['varchar', 'char'])) {
            return $field_data->length / 3;
        } else {
            return $field_data->length;
        }
    }

    /**
     * Метод возвращает значение поля по имени
     * @param string $fieldName
     * @return mixed
     */
    public function getValue($fieldName) {
        return $this->result[$fieldName];
    }

}
