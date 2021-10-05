<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of _fileUploader
 *
 * @author atlant_is
 */
require_once '_dataRowUpdater.class.php';
require_once '_dataRowSource.class.php';

class FileUploader {

    //put your code here   
    var $error, $isImg = false;

    /**
     * Загружает пдф-ку к заказу на поставку
     * @param type $objUUID
     * @param type $file
     */
    function uploadInvoicePDF($objUUID = '', $files = []) {
        //проверим существование заказа
        $row = new DataRowSource("select id from doc_invoices where id='$objUUID'");
        $rowData = $row->getData();
        if (!$rowData) {
            $this->error = 'Invoice not exists ' . $row->error;
            return false;
        }
        foreach ($files as $key => $file) {
            $fname = rawurlencode(basename($file['name']));
            //грузим файл
            if (!file_exists('../docs/invoices/')) {
                mkdir('../docs/invoices/', 0777);
            }
            if (!file_exists('../docs/invoices/' . $objUUID)) {
                mkdir('../docs/invoices/' . $objUUID, 0777);
            }
            $uploadDir = '../docs/invoices/' . $objUUID . '/';
            $ext_arr = explode(".", $fname);
            $ext = strtoupper(end($ext_arr));
            $newfname = 'invoice.' . $ext; //
            if (!$this->uploadFile($file, $newfname, $uploadDir)) {
                return false;
            }
        }
        return true;
    }

    function uploadFile($file, $newFname, $fDir, $img_width = 800, $img_height = 600) {
        if (!$this->checkFileExt(rawurlencode(basename($file['name'])))) {
            //$this->error ='dfsdfsd';
            return false;
        }

        if (!file_exists($file['tmp_name'])) {
            $this->error = 'temp file ' . $file['tmp_name'] . ' does not exists';
            return false;
        }



        if (!move_uploaded_file($file['tmp_name'], $fDir . $newFname)) {
            $this->error = 'Cant save temp file ' . $file['tmp_name'] . ' in catalog ' . $fDir . $newFname;
            unlink($file['tmp_name']);
            return false;
        }

        if ($this->isImg) {
            if (!$this->imgResize($fDir . $newFname, $img_width, $img_height)) {
                //$this->error = 'cant resize image';
                return false;
            }
        }


        return true;
    }

    function checkFileExt($fname) {
        $ext_arr = explode(".", $fname);
        $ext = strtoupper(end($ext_arr));
        $allowedext = array('JPG', 'JPEG', 'PNG', 'GIF', 'PDF', 'DOC', 'DOCX');
        if (!in_array($ext, $allowedext)) {
            unlink($file['tmp_name']);
            $this->error = 'Wrong extension  ' . $fname;
            return false;
        }
        $this->isImg = in_array($ext, array('JPG', 'JPEG', 'PNG', 'GIF'));
        return true;
    }

    function imgResize($image, $width = 800, $height = 600, $newext = '') {
        if (($width < 1) && ($height < 1)) {
            $this->error = "Некорректные входные параметры";
            return false;
        }
        list($w_i, $h_i, $type) = getimagesize($image); // Получаем размеры и тип изображения (число)
        $types = array("", "gif", "jpeg", "png"); // Массив с типами изображений
        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
        if ($ext) {
            $func = 'imagecreatefrom' . $ext; // Получаем название функции, соответствующую типу, для создания изображения
            $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
        } else {
            $this->error = 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
            return false;
        }
        /* Если указать только 1 параметр, то второй подстроится пропорционально */
        $new_height = $height;
        $new_width = $width;
        if ($width) {
            $new_height = $width / ($w_i / $h_i);
            $new_width = $width;
        } else {
            $new_width = $height / ($h_i / $w_i);
            $new_height = $height;
        }

        if ($new_width > $width && $width > 0) {
            $new_width = $width;
            $new_height = $width / ($w_i / $h_i);
        }

        if ($new_height > $height && $height > 0) {
            $new_height = $height;
            $new_width = $height / ($h_i / $w_i);
        }

        if ($w_i <= $new_width && $h_i <= $new_height) {
            return true;
        }

//echo $new_width.'x'.$new_height;

        $img_o = imagecreatetruecolor($new_width, $new_height); // Создаём дескриптор для выходного изображения
        imagecopyresampled($img_o, $img_i, 0, 0, 0, 0, $new_width, $new_height, $w_i, $h_i); // Переносим изображение из исходного в выходное, масштабируя его
        //если передано расширение в какое сохранить - меняем его
        if ($newext) {
            $ext = $types[$newext];
        } else {
            $func = 'image' . $ext; // Получаем функция для сохранения результата
        }
        $this->error = $func . ' - ' . $new_width . 'x' . $new_height;
        return $func($img_o, $image); // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции
    }

}
