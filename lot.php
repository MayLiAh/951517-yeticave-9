<?php

require_once 'helpers.php';
require_once 'functions.php';

$lotId = '';
if (isset($_GET['id'])) {
    $lotId = (int) $_GET['id'];
} else {
    http_response_code(404);
    header("Location: pages/404.html");
}

$ids = getAllLotsIds();

if (!isInArray($ids, $lotId)) {
    http_response_code(404);
    header("Location: pages/404.html");
}

$lot = getLotById($lotId);
$categories = getCategories();
$rates = getLotRates($lotId);

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
        $userId = (int) $_SESSION['user_id'];
        $rateData = [$cost, $userId, $lotId];
        $lotData = [$cost, $lotId];
        $newRate = setNewRate($rateData);
        $updatedLot = setRateInLot($lotData);
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
