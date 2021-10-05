<?php
// Класс отправки электронной почты
class EmailSender {

    
    /**
     * 
     * @var string $smtp_username - логин
     * @var string $smtp_password - пароль
     * @var string $smtp_host - хост
     * @var string $smtp_from - от кого
     * @var integer $smtp_port - порт
     * @var string $smtp_charset - кодировка
     *
     */
    public $smtp_username;
    public $smtp_password;
    public $smtp_host;
    public $smtp_from;
    public $smtp_port;
    public $smtp_charset;
    public $boundary;
    public $addFile = false;
    public $multipart;


    public function __construct() {
        require_once '_sysSet.class.php';
        $settings=new SysSet();
        /*// Здесь надо получить даные почты отправителя из настроек
        /*$this->smtp_username = "support@fa-project.ru";
        $this->smtp_password = "FA@project#support";
        $this->smtp_host = "ssl://smtp.yandex.com";
        $this->smtp_from = "support@fa-project.ru";
        $this->smtp_port = "465";
        $this->smtp_charset = "utf-8";*/
        
         $this->smtp_username = $settings->getValue('adm_smtp_username');
        $this->smtp_password = $settings->getValue('adm_smtp_password');
        $this->smtp_host = $settings->getValue('adm_smtp_host');
        $this->smtp_from = $settings->getValue('adm_email');
        $this->smtp_port = $settings->getValue('adm_smtp_port');
        $this->smtp_charset = "utf-8";
    }

    /**
     * Отправка письма
     * 
     * @param string $mailTo - получатель письма
     * @param string $subject - тема письма
     * @param string $message - тело письма
     * @param string $headers - заголовки письма
     *
     * @return bool|string В случаи отправки вернет true, иначе текст ошибки    *
     */
    function send($mailTo, $subject, $message) {
       /* $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
        $headers .= "From: " . $this->smtp_from . " <" . $this->smtp_username . ">\r\n"; // от кого письмо !!! тут e-mail, через который происходит авторизация
        date_default_timezone_set('Asia/Novosibirsk');
        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= 'Subject: =?' . $this->smtp_charset . '?B?' . base64_encode($subject) . "=?=\r\n";
        $contentMail .= $headers . "\r\n";
        $contentMail .= $message . "\r\n";*/
        $smtp_from = array(
          "FA-Project", // Имя отправителя
          $this->smtp_from // почта отправителя
          );
          // подготовка содержимого письма к отправке 
          $contentMail = $this->getContentMail($subject, $message, $smtp_from, $mailTo);

        try {
            if (!$socket = @fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30)) {
                throw new Exception('Socket not open!' . $errorNumber . "." . $errorDescription);
            }
            if (!$this->_parseServer($socket, "220")) {
                throw new Exception('Connection error');
            }

            $server_name = filter_input(INPUT_SERVER, "SERVER_NAME");
            if (!$server_name) {
                $server_name = urlencode('print-pt.ru'); //'xn----btbkcvexr7b.xn--p1ai';
            }
            fputs($socket, "EHLO $server_name\r\n");
            if (!$this->_parseServer($socket, "250")) {
                // если сервер не ответил на EHLO, то отправляем HELO
                fputs($socket, "HELO $server_name\r\n");
                if (!$this->_parseServer($socket, "250")) {
                    fclose($socket);
                    throw new Exception('Error of command sending: HELO ' . $server_name);
                }
            }

            fputs($socket, "AUTH LOGIN\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }

            fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }

            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            if (!$this->_parseServer($socket, "235")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }

            fputs($socket, "MAIL FROM: <" . $this->smtp_username . ">\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: MAIL FROM');
            }

            $mailTo = str_replace(" ", "", $mailTo);
            $emails_to_array = explode(',', $mailTo);
            foreach ($emails_to_array as $email) {
                fputs($socket, "RCPT TO: <{$email}>\r\n");
                if (!$this->_parseServer($socket, "250")) {
                    fclose($socket);
                    throw new Exception('Error of command sending: RCPT TO');
                }
            }

            fputs($socket, "DATA\r\n");
            if (!$this->_parseServer($socket, "354")) {
                fclose($socket);
                throw new Exception('Error of command sending: DATA');
            }

            fputs($socket, $contentMail . "\r\n.\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception("E-mail didn't sent");
            }

            fputs($socket, "QUIT\r\n");
            fclose($socket);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    // добавление файла в письмо
    public function addAttach($path, $fname = '') {
        $file = @fopen($path, "rb");
        if (!$file) {
            throw new Exception("File `{$path}` didn't open");
        }
        $data = fread($file, filesize($path));
        fclose($file);
        $filename = $fname !== '' ? $fname : basename($path);
        $multipart = "\r\n--{$this->boundary}\r\n";
        $multipart .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
        $multipart .= "Content-Transfer-Encoding: base64\r\n";
        $multipart .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
        $multipart .= "\r\n";
        $multipart .= chunk_split(base64_encode($data));

        $this->multipart .= $multipart;
        $this->addFile = true;
    }

    // парсинг ответа сервера
    private function _parseServer($socket, $response) {
        /*  $responseServer = fgets($socket, 256);
          if (!$responseServer) {
          return false;
          } */
        //$responseServer='response';
        while (@substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                return false;
            }
        }
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
        return true;
    }

    // подготовка содержимого письма
    private function getContentMail($subject, $message, $smtp_from, $mailTo) {
        // если кодировка windows-1251, то перекодируем тему
        if (strtolower($this->smtp_charset) == "windows-1251") {
            $subject = iconv('utf-8', 'windows-1251', $subject);
        }
        date_default_timezone_set('Asia/Novosibirsk'); //!--TODO - поменять потом!
        $contentMail = "Date: " . date("D, d M Y H:i:s") . " \r\n";
        $contentMail .= 'Subject: =?' . $this->smtp_charset . '?B?' . base64_encode($subject) . "=?=\r\n";

        // заголовок письма
        $headers = "MIME-Version: 1.0\r\n";
        // кодировка письма
        if ($this->addFile) {
            // если есть файлы
            $headers .= "Content-Type: multipart/mixed; boundary=\"{$this->boundary}\"\r\n";
        } else {
            $headers .= "Content-type: text/html; charset={$this->smtp_charset}\r\n";
        }
        $headers .= "From: {$smtp_from[0]} <{$smtp_from[1]}>\r\n"; // от кого письмо
        $headers .= "To: " . $mailTo . "\r\n"; // кому
        $contentMail .= $headers . "\r\n";

        if ($this->addFile) {
            // если есть файлы
            $multipart = "--{$this->boundary}\r\n";
            $multipart .= "Content-Type: text/html; charset=utf-8\r\n";
            $multipart .= "Content-Transfer-Encoding: base64\r\n";
            $multipart .= "\r\n";
            $multipart .= chunk_split(base64_encode($message));

            // файлы
            $multipart .= $this->multipart;
            $multipart .= "\r\n--{$this->boundary}--\r\n";

            $contentMail .= $multipart;
        } else {
            $contentMail .= $message . "\r\n";
        }

        // если кодировка windows-1251, то все письмо перекодируем
        if (strtolower($this->smtp_charset) == "windows-1251") {
            $contentMail = iconv('utf-8', 'windows-1251', $contentMail);
        }

        return $contentMail;
    }

    /* function send($mailTo, $subject, $message) {

      $headers = "MIME-Version: 1.0\r\n";
      $headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
      $headers .= "From: " . $this->smtp_from . " <" . $this->smtp_username . ">\r\n"; // от кого письмо !!! тут e-mail, через который происходит авторизация
      date_default_timezone_set('Asia/Novosibirsk');
      $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
      $contentMail .= 'Subject: =?' . $this->smtp_charset . '?B?' . base64_encode($subject) . "=?=\r\n";
      $contentMail .= $headers . "\r\n";
      $contentMail .= $message . "\r\n";

      try {
      if (!$socket = @fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30)) {
      throw new Exception($errorNumber . "." . $errorDescription);
      }
      if (!$this->_parseServer($socket, "220")) {
      throw new Exception('Connection error');
      }

      $server_name = $_SERVER["SERVER_NAME"];
      fputs($socket, "HELO $server_name\r\n");
      if (!$this->_parseServer($socket, "250")) {
      fclose($socket);
      throw new Exception('Error of command sending: HELO');
      }

      fputs($socket, "AUTH LOGIN\r\n");
      if (!$this->_parseServer($socket, "334")) {
      fclose($socket);
      throw new Exception('Autorization error');
      }

      fputs($socket, base64_encode($this->smtp_username) . "\r\n");
      if (!$this->_parseServer($socket, "334")) {
      fclose($socket);
      echo "5";
      throw new Exception('Autorization error');
      }

      fputs($socket, base64_encode($this->smtp_password) . "\r\n");
      if (!$this->_parseServer($socket, "235")) {
      fclose($socket);
      throw new Exception('Autorization error');
      }

      fputs($socket, "MAIL FROM: <" . $this->smtp_username . ">\r\n");
      if (!$this->_parseServer($socket, "250")) {
      fclose($socket);
      throw new Exception('Error of command sending: MAIL FROM');
      }

      $mailTo = ltrim($mailTo, '<');
      $mailTo = rtrim($mailTo, '>');
      fputs($socket, "RCPT TO: <" . $mailTo . ">\r\n");
      if (!$this->_parseServer($socket, "250")) {
      fclose($socket);
      throw new Exception('Error of command sending: RCPT TO');
      }

      fputs($socket, "DATA\r\n");
      if (!$this->_parseServer($socket, "354")) {
      fclose($socket);
      throw new Exception('Error of command sending: DATA');
      }

      fputs($socket, $contentMail . "\r\n.\r\n");
      if (!$this->_parseServer($socket, "250")) {
      fclose($socket);
      throw new Exception("E-mail didn't sent");
      }

      fputs($socket, "QUIT\r\n");
      fclose($socket);
      } catch (Exception $e) {
      return $e->getMessage();
      }
      return true;
      }

      private function _parseServer($socket, $response) {
      while (@substr($responseServer, 3, 1) != ' ') {
      if (!($responseServer = fgets($socket, 256))) {
      return false;
      }
      }
      if (!(substr($responseServer, 0, 3) == $response)) {
      return false;
      }
      return true;
      } */
}
