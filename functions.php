<?php
/**
 * Создает массив с оставшимися до конца торгов часами и отформатированным временем
 *
 * @param int $end Дата окончания торгов в виде числа (метки системного времени Unix)
 *
 * @return array созданный массив
 */
function getRemainingTime(int $end) : array
{
    $secondsInMinute = 60;
    $secondsInHour = 3600;

    $remainingSeconds = $end - time();
    $remainingHours = floor($remainingSeconds / $secondsInHour);
    $remainingMinutes = floor(($remainingSeconds % $secondsInHour) / $secondsInMinute);
    $format = "%02d:%02d";
    $formattedRemainingTime = sprintf($format, $remainingHours, $remainingMinutes);

    return ['remaining_hours' => $remainingHours, 'remaining_time' => $formattedRemainingTime];
}

/**
 * Создает массив с полученными из базы данных значениями
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