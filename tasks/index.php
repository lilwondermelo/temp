<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
	$_SESSION["userId"] = "a0b893a0-5036-11eb-b5a5-183f2639db3f";
}
require_once '../tasks/classes/application.class.php';
$app = new Application();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Задачи</title>
        <link rel="apple-touch-icon" sizes="180x180" href="/tasks/media/images/icons/coins.svg">
        <link rel="icon" type="image/png" sizes="32x32" href="/tasks/media/images/icons/coins.svg">
        <link rel="icon" type="image/png" sizes="16x16" href="/tasks/media/images/icons/coins.svg">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.2">
        <link rel="stylesheet" type="text/css" href="media/fonts/mp.css">
        <link rel="stylesheet" type="text/css" href="style/style.css">
        <script src="js/jquery-3.5.1.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="js/main.js"></script>
    </head>
    <body>
        <div class="container" contenteditable="false">
            
            <div class="main">
            	
        	</div>
        	<div class="overlay">
        		
        	</div>
        	<div class="menu row">
	           <div class="menuItem">
		           	<img src="media/images/icons/calendarMenu.svg" alt="">	
	           </div>
	           <div class="menuItem menuItemPlus">
		           	<img src="media/images/icons/plusMenu.svg" alt="">	
	           </div>
	           <div class="menuItem">
		           	<img src="media/images/icons/boards.svg" alt="">
	           </div>
        	</div>
        </div>
<svg>
<filter id="blue-wash">
<feColorMatrix type="matrix" values="0.2 0.2 0.2 0 0   0.2 0.2 0.2 0 0  1 1 1 0 0  0 0 0 1 0"/>
</filter>
</svg>
        <script>
        	
        </script>

    </body>
</html>
