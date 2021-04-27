<?php

$editingSessionId=$_GET['editingSessionId'];
$uploaddir = "./tmp/";
$error = false;
$files = array();
$data = array();

// Создадим папку если её нет

if (!is_dir($uploaddir)) {
    mkdir($uploaddir, 0777);
}

// переместим файлы из временной директории в указанную
foreach ($_FILES as $file) {
    move_uploaded_file($file['tmp_name'], $uploaddir . $editingSessionId.".jpg");
}

echo json_encode( $data );
