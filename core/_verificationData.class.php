<?php
// Сласс по проверки данных на соотвествие

class VerificationData{

    /*  ПРОВЕРЯЕТ ВХОДНЫЕ СТРОКИ И ПРИ НЕОБХОДИМОСТИ ПРЕОБРАЗУЕТ ИХ В ЧИСЛА
    * - $string - входная строка
    * 
    * - $flag_type - определяет тип данных который необходимо вернуть из функции в случаи успешной обработки строки
    *   - 1 - строка
    *   - 2 - целое число
    *   - 3 - дробное число
    *   - 4 - строка с удалёнными начальными и конечными пробелами
    * 
    * - $flag_tag - указывает удалять или нет html теги в строке
    *   - true - не удалять
    *   - false - удалять
    */
    public function filtr_input_string($string, $flag_type = 1, $flag_tag = true)
    {
        if ($flag_type == 2 || $flag_type == 3) {
            $string = trim($string);
            if ($flag_type == 2) return filter_var($string, FILTER_VALIDATE_INT);
            if ($flag_type == 3) return filter_var($string, FILTER_VALIDATE_FLOAT);
        } else {
            if ($flag_type == 4) {
                $string = trim($string);
            }
            if ($flag_tag === false) {
                // удаляем теги
                $string = filter_var($string, FILTER_SANITIZE_STRING);
            }

            // Превращаем <>&"' в html сущности
            $string = filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
            // Заменяем -- на их сущности и возвращаем. Два тере используются для комментов в базе данных поэтом их заменяем на сущности
            return preg_replace('/\s--\s/', '&#32;&#45;&#45;&#32;', $string);
        }
    }
}