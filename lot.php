<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$lotId = '';
if (isset($_GET['id'])) {
    $lotId = mysqli_real_escape_string($con, $_GET['id']);
} else {
    http_response_code(404);
    header("Location: pages/404.html");
}

$lotsIdsSql = "SELECT id FROM lots";
$ids = getMysqlSelectionResult($con, $lotsIdsSql);

if (!isInArray($ids, $lotId)) {
    http_response_code(404);
    header("Location: pages/404.html");
}

$lotSql = "SELECT l.name AS name, user_id,
            c.name AS category, about, current_cost,
            rate_step, image, end_at 
            FROM lots AS l JOIN categories AS c
            ON c.id = category_id 
            WHERE l.id = $lotId";
    
$categoriesSql = "SELECT id, name FROM categories ORDER BY id";

$ratesSql = "SELECT user_id, r.cost, u.full_name AS user_name, 
             r.created_at AS rate_time 
             FROM rates AS r JOIN users AS u
             ON u.id = user_id
             WHERE r.lot_id = $lotId
             ORDER BY r.created_at DESC";

$lot = getMysqlSelectionAssocResult($con, $lotSql);
$categories = getMysqlSelectionResult($con, $categoriesSql);
$rates = getMysqlSelectionResult($con, $ratesSql);

$ratesCount = empty($rates) ? 0 : count($rates);
$showRate = true;

if (!isset($_SESSION['user_id'])) {
    $showRate = false;
} else {
    $userId = $_SESSION['user_id'];
    if ($lot['user_id'] === $userId) {
        $showRate = false;
    } elseif (isset($rates[0]['user_id'])) {
        $lastRateUserId = $rates[0]['user_id'];
        $showRate = $lastRateUserId === $userId ? false : true;
    } elseif (strtotime($lot['end_at']) < time()) {
        $showRate = false;
    }
}

$minRate = $lot['current_cost'] + $lot['rate_step'];

$contentAdress = 'lot.php';
$contentValues = [ 'categories' => $categories,
                   'lot' => $lot,
                   'lotId' => $lotId,
                   'minRate' => $minRate,
                   'rates' => $rates,
                   'ratesCount' => $ratesCount,
                   'showRate' => $showRate,
                   'cost' => '',
                   'success' => '',
                   'errors' => []
                 ];

if (isset($_POST['submit']) && $showRate) {
    $errors = checkFieldsFilling($_POST);
    $cost = (int) $_POST['cost'];

    if ($cost < $minRate || $cost != round($cost)) {
        $errors['cost'] = 'Введите корректную цену!';
    }

    $contentValues['cost'] = $cost;
    $contentValues['errors'] = $errors;

    if (empty($errors)) {
        $userId = $_SESSION['user_id'];
        $rateData = [$cost, $userId, $lotId];
        $rateSql = "INSERT INTO rates 
                (cost, user_id, lot_id)
                VALUES (?, ?, ?)";
        $lotData = [$cost, $lotId];
        $lotSql = "UPDATE lots SET current_cost = ? WHERE id = ?";
        $newRate = insertDataMysql($con, $rateSql, $rateData);
        $updatedLot = insertDataMysql($con, $lotSql, $lotData);
        $contentValues['showRate'] = false;
        $contentValues['success'] = 'Ставка успешно добавлена';
    }
}

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => $lot['name'],
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
