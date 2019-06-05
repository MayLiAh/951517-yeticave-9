<?php
require_once './vendor/autoload.php';
require_once 'Connection.php';

session_start();

$timezoneDetector = new Dater\TimezoneDetector();
$timezone = $timezoneDetector->getClientTimezone();
date_default_timezone_set($timezone);

/**
 * Получает список категорий
 *
 * @return array Двумерный массив с категориями
 */
function getCategories() : array
{
    $con = Connection::getDbConnection();
    $sql = "SELECT id, name, symbol_code FROM categories ORDER BY id";

    return getMysqlSelectionResult($con, $sql);
}

/**
 * Получает список пользователей
 *
 * @return array Двумерный массив с пользователями
 */
function getUsers() : array
{
    $con = Connection::getDbConnection();
    $sql = "SELECT id, full_name, email, password FROM users";
    
    return getMysqlSelectionResult($con, $sql);
}

/**
 * Получает пользователя по email
 *
 * @param string $email Email, по которому осуществляется поиск
 *
 * @return array Массив с пользователем
 */
function getUserByEmail(string $email) : array
{
    $con = Connection::getDbConnection();
    $email = mysqli_real_escape_string($con, $email);
    $sql = "SELECT id, full_name, password FROM users WHERE email = '$email'";

    return getMysqlSelectionAssocResult($con, $sql);
}

/**
 * Получает список активных лотов для вывода на странице (главной, по категории, поиска)
 *
 * @param int $limit Лимит лотов на страницу
 * @param int $offset Offset для запросав БД
 * @param string $page Страница, для которой нужны лоты. По умолчанию - 'main', главная. 'by-category' для вывода
 * по категории, 'search' для выборки по поисковому запросу
 * @param string $parameter Параметр, по которому осуществляется отбор (категория, строка поиска), по умолчанию - ''
 * допустимо не указывать только если нужна выборка лотов для главной страницы
 *
 * @return array Двумерный массив с лотами
 */
function getActiveLots(int $limit, int $offset, string $page = 'main', string $parameter = '')
{
    $con = Connection::getDbConnection();
    $limit = mysqli_real_escape_string($con, $limit);
    $offset = mysqli_real_escape_string($con, $offset);
    $parametr = mysqli_real_escape_string($con, $parameter);
    $sql = '';

    if ($page === 'main') {
        $sql = "SELECT l.name AS name, l.id AS id, 
                c.name AS category, 
                current_cost, image, end_at 
                FROM lots AS l JOIN categories AS c 
                ON c.id = category_id
                WHERE l.end_at > CURDATE()
                AND winner_id IS NULL
                ORDER BY l.created_at DESC
                LIMIT $limit OFFSET $offset";
    } elseif ($page === 'by-category') {
        $sql = "SELECT l.name AS name, l.id AS id, 
                c.name AS category, 
                current_cost, image, end_at 
                FROM lots AS l JOIN categories AS c 
                ON c.id = category_id
                WHERE l.end_at > CURDATE()
                AND l.category_id = '$parameter'
                AND winner_id IS NULL
                ORDER BY l.created_at DESC
                LIMIT $limit OFFSET $offset";
    } elseif ($page === 'search') {
        $sql = "SELECT l.name AS name, l.about AS about,
                l.id AS id, c.name AS category, 
                current_cost, image, end_at 
                FROM lots AS l JOIN categories AS c 
                ON c.id = category_id
                WHERE MATCH(l.name, l.about) AGAINST('$parameter')
                AND l.end_at > CURDATE()
                AND winner_id IS NULL
                ORDER BY l.created_at DESC
                LIMIT $limit OFFSET $offset";
    }

    return getMysqlSelectionResult($con, $sql);
}

/**
 * Получает общее количество активных лотов в БД для одной из страниц: главной, по категории и поиска
 *
 * @param string $page Страница, для которой осуществляется запрос. По умолчанию - 'main', главная,
 * 'by-category' для лотов по категории и 'search' для выборки лотов по поисковому запросу
 * @param string $parameter Параметр, по которому осуществляется отбор (категория, строка поиска), по умолчанию - ''
 * допустимо не указывать только если нужно количество лотов для главной страницы
 *
 * @return int Количество лотов
 */
function getActiveLotsCount(string $page = 'main', string $parameter = '') : int
{
    $con = Connection::getDbConnection();
    $parameter = mysqli_real_escape_string($con, $parameter);
    $sql = '';

    if ($page === 'main') {
        $sql = "SELECT id FROM lots WHERE end_at > CURDATE() AND winner_id IS NULL";
    } elseif ($page === 'by-category') {
        $sql = "SELECT id FROM lots WHERE end_at > CURDATE() 
                AND winner_id IS NULL AND category_id = '$parametr'";
    } elseif ($page === 'search') {
        $sql = "SELECT id FROM lots WHERE MATCH(name, about) AGAINST('$parameter')
                AND end_at > CURDATE()
                AND winner_id IS NULL";
    }

    return count(getMysqlSelectionResult($con, $sql));
}

/**
 * Получает список ставок для конкретного лота
 *
 * @param int $id Id лота
 *
 * @return array Двумерный массив с лотами
 */
function getLotRates(int $id) : array
{
    $con = Connection::getDbConnection();
    $id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT user_id, r.cost, u.full_name AS user_name, 
            r.created_at AS rate_time 
            FROM rates AS r JOIN users AS u
            ON u.id = user_id
            WHERE r.lot_id = $id
            ORDER BY r.created_at DESC";

    return getMysqlSelectionResult($con, $sql);
}

/**
 * Получает количество ставок для конкретного лота
 *
 * @param int $id Id лота
 *
 * @return int Количество ставок
 */
function getLotRatesCount(int $id) : int
{
    $con = Connection::getDbConnection();
    $id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT id FROM rates WHERE lot_id = $id";

    return count(getMysqlSelectionResult($con, $sql));
}

/**
 * Получает имя категории по id
 *
 * @param int $id Id нужной категории
 *
 * @return string Имя категории
 */
function getCategoryById(int $id) : string
{
    $con = Connection::getDbConnection();
    $id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT name FROM categories WHERE id = $id";

    return getMysqlSelectionAssocResult($con, $sql)['name'];
}

/**
 * Получает список истекших лотов без победителей
 *
 * @return array Двумерный массив с лотами
 */
function getExpiredLotsWithoutWinners() :array
{
    $con = Connection::getDbConnection();
    $sql = "SELECT id, name FROM lots WHERE winner_id IS NULL
            AND end_at <= CURDATE()";

    return getMysqlSelectionResult($con, $sql);
}

/**
 * Получает спискок id лотов из таблицы ставок
 *
 * @return array Двумерный массив с id
 */
function getLotsIdsFromRates() : array
{
    $con = Connection::getDbConnection();
    $sql = "SELECT lot_id FROM rates";

    return getMysqlSelectionResult($con, $sql);
}

/**
 * Получает id победителя лота
 *
 * @param int $id Id лота
 *
 * @return int Id победителя
 */
function getWinnerId(int $id) : int
{
    $con = Connection::getDbConnection();
    $id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT user_id FROM rates WHERE lot_id = $id
            ORDER BY created_at DESC
            LIMIT 1 OFFSET 0";

    return getMysqlSelectionAssocResult($con, $sql)['user_id'];
}

/**
 * Вставляет id победителя в таблицу лотов
 *
 * @param array $data Массив с данными для вставки - id победителя и id лота, только в таком порядке
 *
 * @return int id измененной строки
 */
function setWinner(array $data) : int
{
    $con = Connection::getDbConnection();
    $sql = "UPDATE lots SET winner_id = ? WHERE id = ?";

    return insertDataMysql($con, $sql, $data);
}

/**
 * Получает имя и email победителя по id
 *
 * @param int $id Id победителя
 *
 * @return array Массив с победителем
 */
function getWinner(int $id) : array
{
    $con = Connection::getDbConnection();
    $id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT email, full_name FROM users WHERE id = $id";

    return getMysqlSelectionAssocResult($con, $sql);
}

/**
 * Добавляет новую строку в таблицу лотов
 *
 * @param array $data Массив с данными для вставки в следующем порядке:
 *                    имя лота, описание, адрес изображения, стартовая цена, шаг ставки, текущая цена,
 *                    id пользователя, добавившего лот, id категории, дата окончания
 *
 * @return int Индекс добавленной строки, 0 - если при добавлении произошла ошибка
 */
function setNewLot(array $data) : int
{
    $con = Connection::getDbConnection();
    $sql = "INSERT INTO lots 
            (name, about, image, start_cost, rate_step, current_cost, user_id, category_id, end_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    return insertDataMysql($con, $sql, $data);
}

/**
 * Получает список id всех лотов
 *
 * @return array Двумерный массив с id
 */
function getAllLotsIds() : array
{
    $con = Connection::getDbConnection();
    $sql = "SELECT id FROM lots";

    return getMysqlSelectionResult($con, $sql);
}

/**
 * Получает лот по id
 *
 * @param int $id Id лота
 *
 * @return array Массив с лотом
 */
function getLotById(int $id) : array
{
    $con = Connection::getDbConnection();
    $id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT l.name AS name, user_id,
            c.name AS category, about, current_cost,
            rate_step, image, end_at 
            FROM lots AS l JOIN categories AS c
            ON c.id = category_id 
            WHERE l.id = $id";

    return getMysqlSelectionAssocResult($con, $sql);
}

/**
 * Добавляет новую строку в таблицу ставок
 *
 * @param array $data Массив с данными для вставки в следующем порядке:
 *                    стоимость ставки, id пользователя, id лота, на который сделана ставка
 *
 * @return int Индекс добавленной строки, 0 - если при добавлении произошла ошибка
 */
function setNewRate(array $data) : int
{
    $con = Connection::getDbConnection();
    $sql = "INSERT INTO rates 
            (cost, user_id, lot_id)
            VALUES (?, ?, ?)";

    return insertDataMysql($con, $sql, $data);
}

/**
 * Обновляет цену лота после ставки
 *
 * @param array $data Массив с данными для вставки в следующем порядке:
 *                    стоимость ставки, id лота, на который сделана ставка
 *
 * @return int Индекс обновленной строки, 0 - если при обновлении произошла ошибка
 */
function setRateInLot(array $data) : int
{
    $con = Connection::getDbConnection();
    $sql = "UPDATE lots SET current_cost = ? WHERE id = ?";

    return insertDataMysql($con, $sql, $data);
}

/**
 * Получает id лотов, на которые пользователь сделал ставки
 *
 * @param int $id Id пользователя
 *
 * @return array Двумерный массив с id лотов
 */
function getUserRatesLotsIds(int $id) : array
{
    $con = Connection::getDbConnection();
    $id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT lot_id FROM rates WHERE user_id = $id
            GROUP BY lot_id, created_at
            ORDER BY created_at DESC";

    return getMysqlSelectionResult($con, $sql);
}

/**
 * Получает последнюю ставку пользователя на лот
 *
 * @param int $userId Id пользователя
 * @param int $lotId Id лота
 *
 * @return array Массив со ставкой
 */
function getLastRate(int $userId, int $lotId) : array
{
    $con = Connection::getDbConnection();
    $userId = mysqli_real_escape_string($con, $userId);
    $lotId = mysqli_real_escape_string($con, $lotId);
    $sql = "SELECT l.id AS lot_id, l.image AS lot_img, l.name AS lot_name, l.end_at AS lot_end,
            l.winner_id AS winner_id, c.name AS category_name, r.cost AS cost, r.created_at AS rate_time
            FROM lots AS l JOIN categories AS c ON c.id = l.category_id 
            JOIN rates AS r ON r.lot_id = l.id
            WHERE r.user_id = $userId AND l.id = $lotId
            AND r.created_at = 
            (SELECT MAX(r.created_at) FROM rates AS r
            JOIN lots AS l ON lot_id = l.id
            WHERE r.user_id = $userId AND l.id = $lotId)";

    return getMysqlSelectionAssocResult($con, $sql);
}

/**
 * Получает контакты пользователя по id
 *
 * @param int $id Id пользователя
 *
 * @return string Контакты пользователя
 */
function getUserContacts(int $id) : string
{
    $con = Connection::getDbConnection();
    $id = mysqli_real_escape_string($con, $id);
    $sql = "SELECT contacts FROM users WHERE id = $id";

    return getMysqlSelectionAssocResult($con, $sql)['contacts'];
}

/**
 * Получает список email из таблицы пользователей
 *
 * @return array Двумерный массив с email-ами
 */
function getEmails() : array
{
    $con = Connection::getDbConnection();
    $sql = "SELECT email FROM users";

    return getMysqlSelectionResult($con, $sql);
}

/**
 * Добавляет новую строку в таблицу пользователей
 *
 * @param array $data Массив с данными для вставки в следующем порядке:
 *                    имя, email, пароль, контакты
 *
 * @return int Индекс добавленной строки, 0 - если при добавлении произошла ошибка
 */
function setNewUser(array $data) : int
{
    $con = Connection::getDbConnection();
    $sql = "INSERT INTO users 
            (full_name, email, password, contacts)
            VALUES (?, ?, ?, ?)";

    return insertDataMysql($con, $sql, $data);
}

/**
 *  Проверяет поля формы на заполнение, при обнаружении пустого поля записывает ошибку в массив
 *
 * @param array $fields Массив с полями
 *
 * @return array Пустой массив, если все поля заполнены, массив с ошибками в противном случае
 */
function checkFieldsFilling(array $fields) : array
{
    $errors = [];
    foreach ($fields as $key => $value) {
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
 * @param string $format Один или несколько форматов, которому (одному из которых)
 *                       загруженный файл должен соответствовать
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
        $errors['password'] = 'Пароль может содержать только цифры и латинские буквы и не может быть короче 6 символов';
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
    } elseif ($elapsedHours >= 24 && $elapsedHours < 48) {
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
 * @return int id добавленной или измененной строки, 0 - если произошла ошибка
 */
function insertDataMysql(mysqli $con, string $sql, array $data) : int
{
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        return mysqli_insert_id($con);
    }

    return 0;
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
