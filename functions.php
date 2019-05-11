<?php
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

    if ($elapsedHours < 1) {
        $timeWord = get_noun_plural_form($elapsedMinutes, 'минута', 'минуты', 'минут');
        return "$elapsedMinutes $timeWord назад";
    }
    if ($elapsedHours > 0 && $elapsedHours < 24) {
        $timeWord = get_noun_plural_form($elapsedHours, 'час', 'часа', 'часов');
        return "$elapsedHours $timeWord назад";
    }
    if ($elapsedHours >=24 && $elapsedHours < 48) {
        $time = substr($date, -8, -3);
        return "Вчера, в $time";
    }
    if ($elapsedHours >= 48) {
        return date('d.m.y \в h:i', $dateToTime);
    }
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
 * @return array созданный массив
 */
function getMysqlSelectionResult(mysqli $con, string $sql) : array
{
    $result = mysqli_query($con, $sql);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
    } else {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

/**
 * Создает массив с единственной записью полученной из базы данных
 *
 * @param mysqli $con ресурс соединения с базой данных
 * @param string $sql запрос в базу данных
 * 
 * @return array созданный массив
 */
function getMysqlSelectionAssocResult(mysqli $con, string $sql) : array
{
    $result = mysqli_query($con, $sql);

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
    } else {
        return mysqli_fetch_assoc($result);
    }
}

/**
 * Форматирует переданную сумму, добавляет знак валюты
 *
 * @param int    $price    неформатированная цена
 * @param string $currency знак валюты, по умолчанию - рубля
 * 
 * @return string форматированная цена
 */
function getFormattedPrice(int $price, string $currency = '₽') : string
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
/**
 * Преобразует переданный массив или массивы согласно заданому параметру
 *
 * @param string $method способ преобразования, возможно использование функций
 * strip_tags и htmlspecialchars с параметрами по умолчанию.
 * Если $method !== 'htmlspecialchars', будет применена функция strip_tags
 * @param array  $arrays массив или массивы, в которых нужно удалить или преобразовать теги
 * 
 * @return bool true - если преобразование прошло успешно, иначе false
 */
function tagsTransforming(string $method, array ...$arrays)
{
    if ($method === 'htmlspecialchars') {
        return array_walk_recursive($arrays, function (&$value, $key) {
            $value = htmlspecialchars($value);
        });
    }

    return array_walk_recursive($arrays, function (&$value, $key) {
        $value = strip_tags($value);
    });
}
