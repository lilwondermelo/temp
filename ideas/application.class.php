<?php

class Application {

    public $error;

    //private $code;
    // Авторизация
    function login($email, $password) {
        $retVal = "false";
        require_once './_dataRowSource.class.php';
        $queryText = "select id as userId from dir_users where email='" . $email . "' and password='" . $password . "'";
        $loginDataRow = new DataRowSource($queryText);
        if ($loginDataRow->getData()) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION["userId"] = $loginDataRow->getValue("userId");
            $retVal = "true";
        }
        return $retVal;
    }

    function getUserData() {
        if (isset($_SESSION['userId'])) {
            $id = $_SESSION['userId'];
            require_once './_dataRowSource.class.php';
            $queryText = "select u.name as firstName, u.surname as lastName, u.inn as code, u.email as email, u.phone as phone, t.type as type, t.type_en as typeEn from dir_users u join dir_user_types t where u.id = '" . $id . "' and u.type = t.id";
            $userData = new DataRowSource($queryText);
            if (!$userData->getData()) {
                $this->error = $userData->error;
                return false;
            }
            for ($i = 0; $i < $userData->getFieldsCount(); $i++) {
                $result[$userData->getFieldName($i)] = $userData->getValue($userData->getFieldName($i));
            }

            return $result;
        } else {
            return false;
        }
    }


      function getCabinetCounter() {
        if (!isset($_SESSION["userId"])) {
            $this->error = 'Пользователь не найден!';
            return false;
        }
        require_once './_dataRowSource.class.php';
        $html = '';
         $queryText = "select (select count(*)
from dir_marketplace_goods g 
join dir_marketplace_likes l on l.good_id=g.id
where g.status<3 and l.user_id='" . $_SESSION["userId"] . "') likes,
(select count(*)
from dir_marketplace_goods
where status<3 and owner='b615c390-f71f-11ea-acc1-183f2639db3f') own";
$data = new DataRowSource($queryText);
        $result = $data->getData();
        if (!$result) {
            return array(0,0);
        }
        return array($data->getValue('own'), $data->getValue('likes'));
    }

    /* Случайный новый пароль
      function createPassword() {
      $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
      $max=10;
      $size=StrLen($chars)-1;
      $password=null;
      while($max--) {
      $password.=$chars[rand(0,$size)];
      }
      return $password;
      }
     */

    //Отправить пароль на почту
    function sendEmail($mailTo) {
        require_once $_SERVER['DOCUMENT_ROOT'] . './_dataRowSource.class.php';
        $queryText = "select password from dir_users where email = '" . $mailTo . "'";
        $loginDataRow = new DataRowSource($queryText);
        if (!$loginDataRow->getData()) {
            $this->error = 'Пользователя с такой почтой нет на портале!';
            return false;
        }
        require_once '_emailSender.class.php';
        $sender = new EmailSender();
        $result = $sender->send($mailTo, 'Print-PT восстановление пароля', 'Ваш пароль на сайте Print-PT: ' . $loginDataRow->getValue('password'));
        if (!$result) {
            $this->error = 'Ошибка:' . $sender->error;
            return false;
        }
        return $result;
    }

    function loginCheck($email) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/_dataRowSource.class.php';
        $dataRow = new DataRowSource('select u.password, u.id as userId, u.type as userType from dir_users u join dir_user_types t where u.email="' . $email . '" and u.type = t.id');

        if (!$dataRow->getData()) {
            //Возвращаем поле для регистрации физического лица
            $html = '
                <div class="loginFormSubtitle">Заполните все поля для регистрации</div>
                    <input class="loginFormInput resultInput" placeholder="Имя" type="text" id="name">
                    <input class="loginFormInput resultInput" placeholder="Фамилия" type="text" id="surname">
                    <input class="loginFormInput resultInput" placeholder="Номер телефона" type="text" id="phone">
                    <input class="loginFormInput resultInput" placeholder="Пароль" type="password" id="password">
                    <input class="loginFormInput resultInput" placeholder="Повторить пароль" type="password" id="confirm">
                <div class="loginFormRow">
                    <div class="authorizationFormButton regFormButton" onclick="registration(\'Person\');" id="registrationButton">Зарегистрироваться</div>
                </div>
                </div>
                <div class="loginFormRow loginFormRowDefault">
                <script>$("#phone").mask("+7(999)999-99-99", {placeholder: "+7 (___) ___ __ __" });</script>';
        } else {
            $html = '
           <div class="loginFormRow">
                    <input class="loginFormInput" placeholder="Введите пароль" type="password" id="password">
                    <div class="loginFormInputClear"><img src="/images/icons/clear.svg" alt=""></div>
                    <div class="authorizationFormButton loginFormButton" onclick="login();" id="loginButton">Войти</div>
                </div>
                <div class="loginFormSubtitle loginFormlink" onclick="resetPassword();">Забыли пароль?</div>';
        }
        return $html;
    }

    function gen_password($length = 6)
{
    $password = '';
    $arr = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 
        'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '!'
    );
 
    for ($i = 0; $i < $length; $i++) {
        $password .= $arr[random_int(0, count($arr) - 1)];
    }
    return $password;
}


    function saveUserData($email) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        require_once '_dataRowSource.class.php';
        $regRow = new DataRowSource('select id from dir_users where email="' . $email . '"');
        if ($regRow->getData()) {
            $this->error = 'Пользователь с такой почтой уже существует';
            return false;
        }
        require_once $_SERVER['DOCUMENT_ROOT'] . '/_systemClass.class.php';
        $sys = new SystemClass();
        $userId = $sys->getUUID();
        $data['email'] = $email;
        $data['password'] = password_hash($this->gen_password(12), PASSWORD_BCRYPT);
        $data['type'] = 'user';
        // Сохраняем в базу
        require_once '_dataRowUpdater.class.php';
        $updater = new DataRowUpdater('dir_users');
        $updater->setKey('id', $userId);
        $updater->setDataFields($data);
        $result = $updater->update();
        if (!$result) {
            $this->error = $updater->error;
            return false;
        }
        $_SESSION["userId"] = $userId;
        return true;
    }

    /* //Регистрация и/или создание и отправка нового кода
      function setCode($phone, $userId = '', $type = '') {
      $this->newCode();
      $data = array('name' => ' ', 'surname' => ' ', 'code' => $this->code);
      if ($userId == '') {
      require_once $_SERVER['DOCUMENT_ROOT'] . '/_dataRowSource.class.php';
      $typeRow = new DataRowSource('select id from dir_user_types where type = "Физическое лицо"');
      $typeRow->getData();
      $type = $typeRow->getValue('id');
      require_once $_SERVER['DOCUMENT_ROOT'] . '/_systemClass.class.php';
      $sys = new SystemClass();
      $userId = $sys->getUUID();
      $data['phone'] = $phone;
      $data['type'] = $type;
      }
      require_once $_SERVER['DOCUMENT_ROOT'] . '/_dataRowUpdater.class.php';
      $updater = new DataRowUpdater('dir_users');
      $updater->setKey('id', $userId);
      $updater->setDataFields($data);
      $dbResult = $updater->update();
      if (!$dbResult) {
      $this->error = $updater->error;
      }
      $sendResult = $this->sendEmail($phone);
      return $sendResult;
      } */

   

    //Получаем разметку окна в зависимости от типа действия
    function getLoginWindow($type = '') {
        require_once '_dataSource.class.php';
        require_once '_sysSet.class.php';
        $dataRow = new DataSource('select * from dir_user_types');
        $data = $dataRow->getData();
        $html = '<div class="authorizationTitle">' . (($type == 'login') ? 'Авторизация' : 'Регистрация') . '</div>';
        //Вход
        if ($type == 'login') {
            $html .= '
            <div class="loginForm">
            <div class="loginFormSubtitle">Введите ваш email для входа или регистрации аккаунта</div>
                <div class="loginFormRow">
                    <input class="loginFormInput" type="text" id="email" placeholder="Электронная почта">
                    <div class="loginFormInputClear" onclick="emailClear();"><img src="/images/icons/clear.svg" alt=""></div>
                    <div class="authorizationFormButton loginFormButton" onclick="loginCheck();" id="checkButton">Продолжить</div>
                </div>
                <div class="loginFormResult"></div>
                <div class="loginFormRow loginFormRowSocial">'
                    . '<div class="loginFormButtonSocial" id="google">'
                    . '<img src="images/icons/social/google.jpg" alt="">'
                    . '</div>'
                    . '<div href="" class="loginFormButtonSocial" id="vk">'
                    . '<img src="images/icons/social/vk.svg" alt="">'
                    . '</div>'
                    . '<div class="loginFormButtonSocial inactive" id="mailru">'
                    . '<img src="images/icons/social/mailru.png" alt="">'
                    . '</div>'
                    . '<div class="loginFormButtonSocial" id="yandex">'
                    . '<img src="images/icons/social/yandex.svg" alt="">'
                    . '</div>'
                    . '<div class="loginFormButtonSocial inactive" id="twitter">'
                    . '<img src="images/icons/social/twitter.png" alt="">'
                    . '</div>'
                    . '<div" class="loginFormButtonSocial" id="ok">'
                    . '<img src="images/icons/social/ok.png" alt="">'
                    . '</div>'
                    . '</div>
                <div class="loginFormRow loginFormRowDefault" style="display:none;">
                <div class="authorizationFormButton" onclick="authClick(this);" id="resetButton">Восстановить пароль</div>
                </div>'
                    . '</div> 
             </div>
             <script>
                $(".loginFormButtonSocial").click(function(){
                    
                    document.location.href = "' . SysSet::getSiteUrl() . '/oauth/" + $(this).attr("id") + "Login.php";
                    })
                    </script> ';
        }
        //Регистрация               
        else {
            $html .= '
            <div class="regForm">

                <div class="regFormRow">
                    <div class="regFormSubtitle authorizationFormRequired">Наименование</div>
                    <input class="loginFormInput" type="text" id="name">
                </div>
                <div class="regFormRow">
                    <div class="regFormSubtitle authorizationFormRequired">ИНН</div>
                    <input class="loginFormInput" type="text" id="inn">
                </div>
                <div class="regFormRow">
                    <div class="regFormSubtitle authorizationFormRequired">Email</div>
                    <input class="loginFormInput" type="text" id="email">
                </div>
                <div class="regFormRow">
                    <div class="regFormSubtitle authorizationFormRequired">Номер телефона</div>
                    <input class="loginFormInput" type="text" id="phone">
                </div>
                <div class="regFormRow">
                    <div class="regFormSubtitle">Сайт</div>
                    <input class="loginFormInput" type="text" id="site">
                </div>
                <div class="regFormRow">
                    <div class="regFormSubtitle authorizationFormRequired">Пароль</div>
                    <input class="loginFormInput" type="password" id="password">
                </div>
                <div class="regFormRow">
                    <div class="regFormSubtitle authorizationFormRequired">Повторить пароль</div>
                    <input class="loginFormInput" type="password" id="confirm">
                </div>

                <div class="loginFormRow">
                    <div class="loginFormSubtitle">Уже зарегистрированы? </div>
                    <div class="loginFormSubtitle loginFormlink" onclick="document.location.href = \'./index.php?mainpage=login\';">Войдите!</div>
                </div>

                
                <div class="loginFormRow">
                    <div class="authorizationFormButton regFormButton" onclick="registration(\'Company\');" id="companyRegistration">Зарегистрироваться</div>
                </div>
                </div>
                <script>$("#phone").mask("+7(999)999-99-99", {placeholder: "+7 (___) ___ __ __" });</script>';
        }
        return $html;
    }

    function logout() {

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION["userId"] = "";
        return true;
    }

    function checkUserExists($id = '') {
        require_once './_dataRowSource.class.php';
        $queryText = "select id as userId from dir_users where id='$id'";
        $loginDataRow = new DataRowSource($queryText);
        if ($loginDataRow->getData()) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION["userId"] = $loginDataRow->getValue("userId");
            $retVal = $_SESSION["userId"];
        } else {
            $retVal = "";
        }
        return $retVal;
    }

    public static function plural($n, $form1, $form2, $form3) {
        return in_array($n % 10, array(2, 3, 4)) && !in_array($n % 100, array(11, 12, 13, 14)) ? $form2 : ($n % 10 == 1 ? $form1 : $form3);
    }

}
