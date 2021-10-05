<?php
require_once 'application.class.php';
$app = new Application();
$userId = "";
session_start();

if (isset($_SESSION["userId"])) {
    //доп проверка сществования
    $userId = $app->checkUserExists($_SESSION["userId"]);
}



// По умолчанию новости так как при пустом запросе загружается раздел новости страницы главная
// Потом содержимое может изменяться с помощью запросов, поэтому page может изменяться в зависимости от страницы без перезагрузки
$page = 'course';
// Вот здесь как раз проверяется гет запрос и меняется page если он есть в гет запросе
if (filter_input(INPUT_GET, 'page') !== null) {
    $page = filter_input(INPUT_GET, 'page');
}

if($_SERVER['REQUEST_URI']=='/my-account')
{
$page = 'my-account';
}
if($_SERVER['REQUEST_URI']=='/shop')
{
$page = 'shop';
}
if($_SERVER['REQUEST_URI']=='/wp-admin')
{
$page = 'admin';
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Sofia Petrova &#8211; sport and equimpment</title>

        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.2">
        
       

        <script  src="js/jquery-3.5.1.min.js"></script>      
        <link rel="stylesheet" type="text/css" href="style/style.css">
        
       

      
    </head>
    <body>
        <div class="adminBar">
            <div class="adminBarLogo">
                <div class="adminBarWp img">
                    <img src="img/wp.svg" alt="">
                </div>
                <div class="adminBarHome img">
                    <img src="img/home.svg" alt="">
                </div>
                <div class="adminBarTitle">Sofia Petrova &#8211; sport and equimpment</div>
            </div>
        </div>
        <?php
        $html = ' <div class="container">
            <div class="mainBlock block">
                <div class="innerContent">
                    <div class="innerHeader row">
                        
                    </div>
                    <div class="title">
                        <img src="img/sofaa-mask.svg" alt="">
                    </div>
                    <div class="subtitle">
                        <img src="img/sportequipment.svg" alt="">
                    </div>
                    <div class="mainDescr row">
                        
                    </div>
                    <div class="mainMenu row">
                        
                    </div>
                </div>
            </div>

            <div class="aboutBlock block">
                <div class="innerContent">
                    
                </div>
            </div>
        </div>';
        if ($page == 'my-account') {
            require_once 'pages/my-account.php';
        }
        else if ($page == 'shop') {
            require_once 'pages/shop.php';
        }
        else if ($page == 'admin') {
            require_once 'pages/admin.php';
        }

        else {
            echo $html;
        }
    

?>
       
        


        <?php
        //require_once 'blocks/header.php';
//<li class="mobileMenuItem menuItem " id="teamM"  data-id="?mainpage=team"><div class="mobileMenuBlockContainer">Команда</div></li> 
?>
                 
           

<?php
// Загружаем по ссылке Главная - about есть своё подменю и суб меню
if (in_array($page, ['news', 'journal', 'media', 'events', 'post', 'marketplace', 'interview'])) {
    //require_once 'blocks/about.php';
}
?>
<div class="systemMessageOverlay">
            <div class="systemMessage">
                <div class="systemMessageText" id="systemMessageText">Текст сообщения</div>
                <div class="systemCloseButton" onclick="closeSystemMessage()">Понятно</div>
            </div>
        </div>
        <script type="text/javascript" src="js/main.js"></script>

    </body>
</html>
