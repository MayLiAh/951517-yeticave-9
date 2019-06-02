<?php
require_once './vendor/autoload.php';
$timezoneDetector = new Dater\TimezoneDetector();
$timezone = $timezoneDetector->getClientTimezone();
date_default_timezone_set($timezone);

/**
 *  Проверяет поля формы на заполнение, при обнаружении пустого поля записывает ошибку в массив
 * 
 * @param array $fields Массив с полями
 * 
 * @return array Пустой массив, если все поля заполнены, массив с ошибками в противном случае
 */
function checkFieldsFilling(array $arr) : array
{
    $errors = [];
    foreach ($arr as $key => $value) {
        if (empty(trim($value)) && $key !== 'submit') {
            $errors[$key] = 'Поле должно быть заполнено!';
        }
    }

    return $errors;
}

/**
 *  Проверяет корректность введенных значени при добавлении лота
 * 
 * @param string $rate Начальная цена лота
 * @param string $rateStep Минимальный шаг ставки
 * @param string $date Дата окончания лота
 * 
 * @return array Пустой массив, если поля заполнены верно, массив с ошибками в противном случае
 */
function checkLotFields(string $rate, string $rateStep, string $date) : array
{
    $rate = (int) $rate;
    $rateStep = (int) $rateStep;
    $tommorow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
    $errors = [];

    if (($rate <= 0 || floor($rate) != $rate) && !empty($rate)) {
        $errors['lot-rate'] = 'Введите корректную цену';
    }

    if (($rateStep <= 0 || floor($rateStep) != $rateStep) && !empty($rateStep)) {
        $errors['lot-step'] = 'Введите корректную ставку';
    }

    if ((!is_date_valid($date) || strtotime($date) < $tommorow) && !empty($date)) {
        $errors['lot-date'] = 'Введите корректную дату';
    }

    return $errors;
}

/**
 * Проверяет загружен ли файл, правильный ли формат. Если ошибок нет - добавляет файл в папку /uploads
 * 
 * @param array $files Массив $_FILES, содержащий загруженный файл
 * @param string $format Один или несколько форматов, которому (одному из которых) загруженный файл должен соответствовать
 * 
 * @return array Пустой массив, если файл загружен и формат верный, массив с ошибками в противном случае
 */
function checkAddFile(array $files, string ...$format) : array
{
    $errors = [];

    if (isset($files['lot-img']) && !empty($files['lot-img']['name'])) {
        $fileName =  $_FILES['lot-img']['name'];
        $fileType = mime_content_type($files['lot-img']['tmp_name']);
        if (!in_array($fileType, $format)) {
            $errors['lot-img'] = 'Выберите файл формата .png, .jpeg или .jpg';
        }

        if (empty($errors)) {
            $filePath = __DIR__ . '/uploads/';

            move_uploaded_file($files['lot-img']['tmp_name'], $filePath . $fileName);
        }
    } else {
        $errors['lot-img'] = 'Изображение обязательно к добавлению!';
    }

    return $errors;
}

/**
 * Проверяет корректность введенного email при регистрации
 * 
 * @param string $email Email для проверки
 * @param array $emails Массив с имейлами уже зарегистрированных пользователей
 * 
 * @return array Пустой массив, если email корректный, массив с ошибками в противном случае
 */
function checkEmail(string $email, array $emails)
{
    $errors = [];

    if (isInArray($emails, $email)) {
        $errors['email'] = 'Уже существует пользователь с таким e-mail';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $errors['email'] = 'Введите корректный e-mail';
    }

    return $errors;
}

/**
 * Проверяет корректность введенного пароля при регистрации
 * 
 * @param string $password Пароль для проверки 
 * @param string $passwordReg Регулярное выражение, которому должен соответствовать пароль
 * 
 * @return array Пустой массив, если пароль корректный, массив с ошибками в противном случае
 */
function checkPassword($password, $passwordReg)
{
    $errors = [];

    if ((strlen($password) < 6 || !preg_match($passwordReg, $password)) && !empty($password)) {
        $errors['password'] = 'Пароль может содержать только цифры и латинские буквы и не должен быть короче 6 символов';
    }

    return $errors;
}

/**
 * Создает массив с оставшимися до конца торгов часами и отформатированным временем
 *
 * @param string $end Дата окончания торгов в строки
 *
 * @return array созданный массив
 */
function getRemainingTime(string $end) : array
{
    $endToTime = strtotime($end);
    $secondsInMinute = 60;
    $secondsInHour = 3600;

    $remainingSeconds = $endToTime - time();
    $remainingHours = floor($remainingSeconds / $secondsInHour);
    $remainingMinutes = floor(($remainingSeconds % $secondsInHour) / $secondsInMinute);
    $format = "%02d:%02d";
    $formattedRemainingTime = sprintf($format, $remainingHours, $remainingMinutes);

    return ['remaining_hours' => $remainingHours, 'remaining_time' => $formattedRemainingTime];
}

/**
 * Создает строку с прошедшим с момента ставки временем
 *
 * @param string $date Дата создания ставки в виде строки
 *
 * @return string созданная строка
 */
function getElapsedTime(string $date) : string
{
    $dateToTime = strtotime($date);
    $secondsInMinute = 60;
    $secondsInHour = 3600;

    $elapsedSeconds = time() - $dateToTime;
    $elapsedHours = floor($elapsedSeconds / $secondsInHour);
    $elapsedMinutes = floor(($elapsedSeconds % $secondsInHour) / $secondsInMinute);
    $result = '';

    if ($elapsedHours < 1) {
        $timeWord = get_noun_plural_form($elapsedMinutes, 'минута', 'минуты', 'минут');
        $result = "$elapsedMinutes $timeWord назад";
    } elseif ($elapsedHours > 0 && $elapsedHours < 24) {
        $timeWord = get_noun_plural_form($elapsedHours, 'час', 'часа', 'часов');
        $result = "$elapsedHours $timeWord назад";
    } elseif ($elapsedHours >=24 && $elapsedHours < 48) {
        $time = substr($date, -8, -3);
        $result = "Вчера, в $time";
    } elseif ($elapsedHours >= 48) {
        $result = date('d.m.y \в h:i', $dateToTime);
    }

    return $result;
}

/**
 * Производит отправку данных в БД
 *
 * @param mysqli $con  ресурс соединения с базой данных
 * @param string $sql  запрос в базу данных
 * @param array $data  значения для подстановки в подготовленный запрос
 *
 * @return int id добавленной или измененной строки
 */
function insertDataMysql(mysqli $con, string $sql, array $data)
{
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $result = mysqli_insert_id($con);
    }

    return $result;
}

/**
 * Создает двумерный массив с полученными из базы данных значениями
 *
 * @param mysqli $con ресурс соединения с базой данных
 * @param string $sql запрос в базу данных
 *
 * @return array созданный массив, содержимое которого обработано функцией strip_tags
 */
function getMysqlSelectionResult(mysqli $con, string $sql) : array
{
    $result = mysqli_query($con, $sql);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        return [];
    }

    $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);
    array_walk_recursive($arr, function (&$value, $key) {
        $value = strip_tags($value);
    });

    return $arr;
}

/**
 * Создает массив с единственной записью полученной из базы данных
 *
 * @param mysqli $con ресурс соединения с базой данных
 * @param string $sql запрос в базу данных
 *
 * @return array созданный массив, содержимое которого обработано функцией strip_tags
 */
function getMysqlSelectionAssocResult(mysqli $con, string $sql) : array
{
    $result = mysqli_query($con, $sql);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
        return [];
    }

    $arr = mysqli_fetch_assoc($result);
    array_walk($arr, function (&$value, $key) {
        $value = strip_tags($value);
    });

    return $arr;
}

/**
 * Форматирует переданную сумму, добавляет знак валюты
 *
 * @param int    $price    неформатированная цена
 * @param string $currency знак валюты, по умолчанию - рубля
 *
 * @return string форматированная цена
 */
function getFormattedPrice(int $price, string $currency = '<b class="rub"></b>') : string
{
    $roundPrice = ceil($price);
    $formattedPrice = $roundPrice < 1000 ? $roundPrice : number_format($roundPrice, 0, '.', ' ');

    return "$formattedPrice $currency";
}
/**
 * Проверяет наличие значения в двумерном массиве
 *
 * @param array $array массив, в котором производится поиск
 * @param $value знаначение, которое нужно найти
 *
 * @return bool true - если значение найдено, false - если нет или если массив одномерный
 */
function isInArray(array $array, $value) : bool
{
    foreach ($array as $arr) {
        if (!is_array($arr)) {
            return false;
        }

        if (in_array($value, $arr)) {
            return true;
        }
    }
    return false;
}
