<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/dialove/classes/productsController.class.php';
$products = new ProductsController();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Mommyfy</title>
        <link rel="icon" type="image/png" sizes="32x32" href="/mommyfy/media/images/icons/coins.svg">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.2">
        <link rel="stylesheet" type="text/css" href="../space/style/style.css">
        <link rel="stylesheet" type="text/css" href="../space/style/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="../space/style/bootstrap-grid.css">
        <link rel="stylesheet" type="text/css" href="style/style.css">
    </head>
    <body>
        <div class="container">
            <div class="content">
                <?php require 'blocks/products.php'; ?>
            </div>
        </div>
        <div class="overlay">
            <div class="overlayInner">
            </div>
        </div>

        <script src="../space/js/jquery-3.5.1.js"></script>
        <script src="../space/js/main.js"></script>
        <script src="../space/js/jquery.maskedinput.js"></script>
        <script src="../space/js/bootstrap.bundle.js"></script>
        <script src="../space/js/bootstrap.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>