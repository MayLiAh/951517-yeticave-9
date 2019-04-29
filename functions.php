<?php
/**
 * Создает массив с оставшимися до конца торгов часами и отформатированным временем
 *
 * @param string $end Дата окончания торгов в виде числа (метки системного времени Unix)
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