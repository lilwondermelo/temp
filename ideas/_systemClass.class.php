<?php

// Класс общесистемных функций приложения
class SystemClass {

    public $error;

    /**
     * Вовзращает глобальный идентификатор
     * @return Ыекштп
     */
    public static function getUUID() {
        /* return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
          ); */
        require_once '_uuidClass.class.php';
        return UUIDclass::v1('PRINT-PT.RU');
    }

    /**
     * ОТправка СМС
     * @param string $phone - Номер телефона
     * @param string $message - Сообщение
     * @return boolean
     */
    function sendSMS($phone, $message) {
        require_once("_smsSender.class.php");
        $smsSender = new SMSSender();
        //$result = $smsSender->send(array("text" => $message, "action" => "send"), array($phone));
        $result = $smsSender->send($phone, $message, 'DIRECT');
        //if ($result['code'] == 1) {
        //    return true;
        // }

        if (!is_array($result)) {
            $this->error = 'Неизвестная ошибка при отправке SMS попробуйте ещё раз, либо обратитесь к администрации сайта';
            return false;
        } elseif ($result['success']) {
            return true;
        }
        $this->error = $result['message'];
        //$this->error = json_encode($result);
        return false;
    }

    /**
     * Отправка электронной почты
     * @param string $mailTo - адрес
     * @param string $title - заголовок
     * @param string $message - текст сообщения
     * @param array  $files - массив файлов для прикрепления
     * @return boolean
     */
    function sendEmail($mailTo, $title, $message, $files = []) {

        require_once("_emailSender.class.php");
        $emailSender = new EmailSender();
        $result = $emailSender->send($mailTo, $title, $message);
        if ($result !== TRUE) {
            $this->error = $result;
            return false;
        }
        return $result;
    }

    // 
    /**
     * Поверка соответсвия кода СМС
     * @param string $verificationCode
     * @return boolean
     */
    function SMScodeCheck($verificationCode) {
        // Проверка кода подтверждения
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['tmpSMSVerificationCode'])) {
            $this->error = "Не установлен код подтверждения SMS в сессии, повторите отправку";
            return FALSE;
        }
        if ($_SESSION['tmpSMSVerificationCode'] != $verificationCode) {
            $this->error = "Код подтверждения SMS не совпадает ";
            return false;
        }
        return true;
    }

//    
    /**
     * Поверка соответсвия кода Email 
     * @param string $emailCode
     * @return boolean
     */
    function emailCodeCheck($emailCode) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['tmpEmailVerificationCode'])) {
            $this->error = "Не установлен код подтверждения email в сессии, повторите отправку";
            return FALSE;
        }
        if ($_SESSION['tmpEmailVerificationCode'] != $emailCode) {
            $this->error = "Код подтверждения email не совпадает ";
            return FALSE;
        }
        return TRUE;
    }

//
    /**
     * отправка проверочного кода на телефон
     * @param string $phone
     * @param mixed $virtual - Если передано - сообщение не отправляется, а возвращается через ошибку
     * @return boolean
     */
    function sendSMSCode($phone, $virtual = NULL) {
        $verificationCode = rand(1000, 9000);
        if (!$virtual) {
            if (!$this->sendSMS('+7' . $phone, 'Здравствуйте ! Код подтверждения Вашего телефона на сайте HORECA ' . $verificationCode)) {
                return false;
            }
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['tmpSMSVerificationCode'] = $verificationCode;
        if ($virtual) {
            $this->error = $verificationCode;
        }
        return true;
    }

    /**
     * отправка проверочного кода на почту
     * @param type $email
     * * @param mixed $virtual - Если передано - сообщение не отправляется, а возвращается через ошибку
     * @return boolean
     */
    function sendEmailCode($email, $virtual = NULL) {
        $verificationCode = rand(1000, 9000);
        if (!$virtual) {
            $result = $this->sendEmail($email, 'Подтверждение E - mail на сайте HORECA', 'Здравствуйте ! Код подтверждения вашей электронной почты: ' . $verificationCode);
            if ($result !== TRUE) {
                $this->error = $result;
                return FALSE;
            }
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['tmpEmailVerificationCode'] = $verificationCode;
        if ($virtual) {
            $this->error = $verificationCode;
        }
        return true;
    }

    function addtoQueue($obj_type, $id) {
        require_once '_dataRowUpdater.class.php';
        //добавляем элемент в очередь обмена
        $updater = new DataRowUpdater('sys_queued_obj');
        $updater->setDataFields(['obj_type' => $obj_type, 'status' => 'added', 'date_add' => date('Y.m.d H:i:s')]);
        $updater->setKeyField('id', $id);
        if (!$updater->update()) {
            $this->error = $updater->error;
            return false;
        }
        return true;
        //--добавляем элемент в очередь обмена
    }

    function retJSONErr($err) {
        switch ($err) {
            case JSON_ERROR_NONE:
                $this->error = '';
                break;
            case JSON_ERROR_DEPTH:
                $this->error = ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $this->error = ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $this->error = ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $this->error = ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $this->error = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $this->error = ' - Unknown error';
                break;
        }
    }

    /**
     * Обновляет счетчик товаров в группах/группе раз в час
     * @param type $groupID  -пока не используется
     */
    function updGroupCount($groupID = '') {
        if ($groupID) {
            $sqlText = ''; //пока без этого обойдёмся
        }
        require_once './_dataConnector.class.php';
        $data = new DataConnector();

        //обновляем конечные группы
        $sqlText = 'update dir_goods_groups set goods_count=(select count(1) from dir_goods WHERE dir_goods.goods_group=dir_goods_groups.id and goods_state=1 ), count_upd=CURRENT_TIMESTAMP() 
WHERE is_group=0 AND  (HOUR(timediff(CURRENT_TIMESTAMP(),count_upd))>0 OR count_upd IS NULL) ';

        $rez = $data->sqlQuery($sqlText);

        if (!$rez) {
            $this->error = $data->error;
            return false;
        }

        if ($data->affected_rows == 0) {
            return true;
        }
        //строки были обновлены, работаем дальше
        //чтобы было все по-честному пройдем по товарам

        $sqlText = 'update dir_goods_groups set goods_count_photo=0';
        $rez = $data->sqlQuery($sqlText);
        require_once './_dataSource.class.php';
        $sqlText = 'select id, image, goods_group from dir_goods where image is not null and goods_state=1';

        $dataSrc = new DataSource($sqlText);
        $rez = $dataSrc->getData();
        $photoCount = [];
        if (!$rez) {
            if ($dataSrc->error) {
                $this->error = $dataSrc->error;
                return false;
            }
        }
        foreach ($rez as $elem) {
            $fway = './images/goods_images/' . $elem['id'] . '/' . $elem['image'];
            if (file_exists($fway)) {
                $photoCount["'" . $elem['goods_group'] . "'"] += 1;
            }
        }
        foreach ($photoCount as $elem => $val) {
            $sqlText = 'update dir_goods_groups set goods_count_photo=' . $val . " where id=$elem";
            $data->sqlQuery($sqlText);
        }
        //вычислять рекурсию для уровней вложенности неохота, поэтому просто пройдем апдейтами
        //предположим, что в здравом уме никто не будет делать вложенность глубже 10
        for ($index = 0; $index < 9; $index++) {

            $sqlText = "UPDATE dir_goods_groups SET goods_count = (SELECT a.cnt  from
(select sum(dgs.goods_count) cnt, parent from dir_goods_groups dgs WHERE count_upd IS NOT NULL AND parent<>''
GROUP BY parent) a
WHERE a.parent=dir_goods_groups.id),
goods_count_photo=(SELECT b.cnt  from
(select sum(dgs.goods_count_photo) cnt, parent from dir_goods_groups dgs WHERE count_upd IS NOT NULL AND parent<>''
GROUP BY parent) b
WHERE b.parent=dir_goods_groups.id),
count_upd=CURRENT_TIMESTAMP()  WHERE dir_goods_groups.is_group=1";
            $rez = $data->sqlQuery($sqlText);
            if (!$rez) {
                $this->error = $data->error;
                return false;
            }
        }
        return true;
    }

    //Отправка уведомлений о регистрации и заказах на смс и почту
    function systemNotification($eventType, $data = 0) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        //По умолчанию запрашиваем данные пользователя
        $clientQuery = "select email, phone, client_type, name from dir_clients where id = '" . $_SESSION['authorizedClientId'] . "'";
        $messageHeader = 'Регистрация пользователя ';
        //Если это заказ, изменяем запрос для получения данных этого заказа
        switch ($eventType) {
            case 'new_supplier_order':
                $clientQuery = "select i.doc_number, co.name as contName, co.inn, cl.email, cl.name, cl.phone, cl.client_type from doc_invoices i join dir_conts co join dir_clients cl where i.id = '" . $data . "' and i.cont = co.id and co.client = cl.id";
                $messageHeader = 'Новый заказ поставщика ';
                break;
            case 'new_byuer_order':
                $clientQuery = "select i.doc_number, co.name as contName, co.inn, cl.email, cl.name, cl.phone, cl.client_type from doc_orders i join dir_conts co join dir_clients cl where i.id = '" . $data . "' and i.client_payer = co.id and co.client = cl.id";
                $messageHeader = 'Новый заказ покупателя ';
                break;
            default:
                break;
        }
        //Запрос данных клиента из БД
        require_once '_dataSource.class.php';
        $clientData = new DataSource($clientQuery);
        if (!$clientData->getData()) {
            $this->error = $clientData->error;
            return false;
        }
        //Запрос шаблона из БД
        $templateQuery = "select * from sys_notification where event_type = '" . $eventType . "'";
        require_once '_dataSource.class.php';
        $template = new DataSource($templateQuery);
        if (!$template->getData()) {
            $this->error = $template->error;
            return false;
        }

        //Заполняем переменные значениями
        $search = array('%email%', '%phone%', '%clientType%', '%clientName%', '%contName%', '%inn%', '%orderNumber%');
        $replace = array($clientData->getValue('email', 0),
            $clientData->getValue('phone', 0),
            ($clientData->getValue('client_type', 0) == 'byuer' ? 'Покупатель' : 'Поставщик'),
            $clientData->getValue('name', 0),
            $clientData->getValue('contName', 0),
            $clientData->getValue('inn', 0),
            $clientData->getValue('doc_number', 0));


        require_once './_sysSet.class.php';
        $sysSet = new SysSet('adm_istest');
        $noSms = $sysSet->getValue();
        //Проверяем шаблон на наличие номеров, адресов и текстов сообщений
        if (($template->getValue('phone', 0)) && ($template->getValue('sms_message', 0))) {
            if ($noSms || $noSms == 'true' || $noSms == '1') {
                //
            } else {
                $smsText = $template->getValue('sms_message', 0);
                $smsText = str_replace($search, $replace, $smsText);
                $phones = explode(',', $template->getValue('phone', 0));
                //Отправляем смс
                foreach ($phones as $phone) {
                    if (!$this->sendSms($phone, $smsText)) {
                        $this->logChange('System SMS Notification', $phone . '|' . $smsText, $this->error);
                    }
                }
            }
        }

        if (($template->getValue('email', 0)) && ($template->getValue('email_message', 0))) {
            $emailText = $template->getValue('email_message', 0);
            $emailText = str_replace($search, $replace, $emailText);
            //Отправляем письмо
            if (!$this->sendEmail($template->getValue('email', 0), $messageHeader, $emailText)) {
                $this->logChange('System EMAIL Notification', $template->getValue('email', 0) . '|' . $messageHeader . '|' . $emailText, $this->error);
            }
        }
        return true;
    }

    //зпись события в системный лог обмена
    function logChange($event, $postdata, $result) {
        //
        require_once ('./_dataConnector.class.php');
        $conn = new DataConnector();
        if ($conn->sqlConnect()) {
            $sqllog = 'insert into sys_change_log (event, postdata, result) values ("' . $event . '", "' . mysqli_real_escape_string($conn->db, $postdata) . '", "' . mysqli_real_escape_string($conn->db, $result) . '")';
            $conn->sqlQuery($sqllog);
            //очиства лога
            $sqllog = 'delete FROM sys_change_log WHERE DATE < DATE_ADD(CURDATE(), INTERVAL -20 DAY)';
            $conn->sqlQuery($sqllog);
            $conn->sqlClose();
        }
    }

}
