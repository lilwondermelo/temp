<?php

Class Cabinet {

    var $error = '';

    function getUserData() {
        if (!isset($_SESSION["userId"])) {
            $this->error = 'Пользователь не найден!';
            return false;
        }
        require_once '_dataRowSource.class.php';
        $dataRow = new DataRowSource('select u.name, u.surname, u.inn, u.phone, u.site, dt.type_en type '
                . 'from dir_users u  '
                . 'join dir_user_types dt on dt.id=u.type where u.id="' . $_SESSION["userId"] . '"');
        $data = $dataRow->getDataRow();
        if (!$data) {
            $this->error = 'Пользователь не найден!';
            return false;
        }
        if ($dataRow->getValue('type') == 'Person') {
            $rows = array('name' => 'Имя', 'surname' => 'Фамилия', 'phone' => 'Телефон');
        } else {
            $rows = array('name' => 'Наименование', 'inn' => 'ИНН', 'site' => 'Сайт', 'phone' => 'Телефон');
        }
        $html = '';
        foreach ($rows as $key => $value) {
            if ((($data[$key] != '') || ($key == 'site')) && ($key != 'type')) {
                $html .= '<div class="cabinetRow">
                    <div class="cabinetSubtitle">' . $value . '</div>
                    <input class="cabinetInput" type="text" id="' . $key . 'Cabinet" value="' . $data[$key] . '" data-id="' . $data[$key] . '">
                </div>';
            }
        }
        $html .= '<div class="cabinetRow">
                    <div class="cabinetSubtitle">Новый пароль</div>
                    <input class="cabinetInput" type="password" id="passwordCabinet" data-id="">
                </div>

                <div class="cabinetRow">
                    <div class="cabinetSubtitle">Повторите пароль</div>
                    <input class="cabinetInput" type="password" id="confirmCabinet" data-id="">
                </div>
        <div class="cabinetButton">Сохранить данные</div>';
        return $html;
    }

    function saveUserData($data) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $id = $_SESSION["userId"];
        require_once '_dataRowSource.class.php';
        $regRow = new DataRowSource('select id from dir_users where phone="' . $data["phone"] . '"');
        if (($regRow->getData()) && ($regRow->getValue('id') != $id)) {
            $this->error = 'Пользователь с таким номером телефона уже существует';
            return false;
        }
        // Сохраняем в базу
        require_once '_dataRowUpdater.class.php';
        $updater = new DataRowUpdater('dir_users');
        $updater->setKey('id', $id);
        $updater->setDataFields($data);
        $result = $updater->update();
        if (!$result) {
            $this->error = $updater->error;
            return false;
        }
        return true;
    }

  

    function getMyGoods() {
        if (!isset($_SESSION["userId"])) {
            $this->error = 'Пользователь не найден!';
            return false;
        }
        require_once './_dataSource.class.php';
        $html = '';

        $queryText = "select g.id as goodId, (SELECT GROUP_CONCAT(a.name) FROM dir_marketplace_advanced a 
INNER JOIN dir_marketplace_goods_advanced ga  
ON ga.advanced_id=a.id 
where ga.good_id=g.id) advNames, 
(SELECT count(*) FROM dir_marketplace_likes l 
where l.good_id=g.id) likes,
(SELECT count(*) FROM dir_marketplace_shows s 
where s.good_id=g.id) countViews,
(SELECT count(*) FROM dir_marketplace_shows s 
where s.good_id=g.id and DATEDIFF(NOW(),s.view_date) < 1) todayViews,
c.print_type as printType, c.eq_type as eqType, g.old as old,  g.is_detail as isDetail, 
c.id as company, m.id as model, g.publish_date as pubDate, m.name as modelName, g.price as price, 
g.print_sections as printSections, g.cut_sections as cutSections, g.year as year, g.form_cyl as formCyl, 
g.mag_cyl as magCyl, g.canvas_width as canvasWidth, g.cut_width as cutWidth, g.cut_type as cutType, 
g.print_process as process, p.name printProcess, DATEDIFF(NOW(),g.publish_date) dayago, g.status, g.reason,
case when status=0 then 
'На модерации' 
else case when status=1 then 'Допущено' 
else case when status=2 then 'Отклонено' 
else 'Скрыто' end end end status_name
from dir_marketplace_goods g 
join dir_marketplace_models m on m.id=g.model_id
join dir_marketplace_companies c on c.id=m.company_id 
left join dir_marketplace_processes p on p.id=g.print_process  
where status<3 and g.owner='" . $_SESSION["userId"] . "' order by g.publish_date desc ";

        $data = new DataSource($queryText);
        $result = $data->getData();
        if (!$result) {
            $html = 'Нет объявлений. Разместить объявление можно по ссылке <a href="?mainpage=addGoods">добавить объявление</a> ';
            //$this->error=$data->error;
            //$html.=$data->error;
            return $html;
        }
//Выводим карточки товаров
        require_once './marketplace.class.php';
        $market = new Marketplace();
        foreach ($result as $row) {
            $html .= $market->getGoodCard($row, true);
        }
        return $html;
    }



    function getMyLikes() {
        if (!isset($_SESSION["userId"])) {
            $this->error = 'Пользователь не найден!';
            return false;
        }
        require_once './_dataSource.class.php';
        $html = '';

        $queryText = "select g.id as goodId, (SELECT GROUP_CONCAT(a.name) FROM dir_marketplace_advanced a 
INNER JOIN dir_marketplace_goods_advanced ga  
ON ga.advanced_id=a.id 
where ga.good_id=g.id) advNames, 
c.print_type as printType, c.eq_type as eqType, g.old as old,  g.is_detail as isDetail, 
c.id as company, m.id as model, g.publish_date as pubDate, m.name as modelName, g.price as price, 
g.print_sections as printSections, g.cut_sections as cutSections, g.year as year, g.form_cyl as formCyl, 
g.mag_cyl as magCyl, g.canvas_width as canvasWidth, g.cut_width as cutWidth, g.cut_type as cutType, 
g.print_process as process, p.name printProcess, DATEDIFF(NOW(),g.publish_date) dayago, g.status, g.reason,
case when status=0 then 
'На модерации' 
else case when status=1 then 'Допущено' 
else case when status=2 then 'Отклонено' 
else 'Скрыто' end end end status_name
from dir_marketplace_goods g 
join dir_marketplace_likes l on l.good_id=g.id
join dir_marketplace_models m on m.id=g.model_id
join dir_marketplace_companies c on c.id=m.company_id 
left join dir_marketplace_processes p on p.id=g.print_process  
where status<3 and l.user_id='" . $_SESSION["userId"] . "' order by g.publish_date desc ";

        $data = new DataSource($queryText);
        $result = $data->getData();
        if (!$result) {
            $html = 'Нет объявлений. Разместить объявление можно по ссылке <a href="?mainpage=addGoods">добавить объявление</a> ';
            //$this->error=$data->error;
            //$html.=$data->error;
            return $html;
        }
//Выводим карточки товаров
        require_once './marketplace.class.php';
        $market = new Marketplace();
        foreach ($result as $row) {
            $html .= $market->getGoodCard($row);
        }
        return $html;
    }


}
