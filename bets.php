<?php

require_once 'helpers.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    header("Location: login.php");
}

$userId = (int) $_SESSION['user_id'];
$rates = [];

$categories = getCategories();
$lotsIds = getUserRatesLotsIds($userId);

if (!empty($lotsIds)) {
    foreach ($lotsIds as $lotId) {
        $id = $lotId['lot_id'];
        $rate = getLastRate($userId, $id);
        $modifiedRate = $rate;
        $modifiedRate['rate_class'] = '';
        $modifiedRate['timer_class'] = '';
        $modifiedRate['timer_status'] = '';
        $modifiedRate['user_contacts'] = '';

        if (isset($rate['winner_id']) && !empty($rate['winner_id'])) {
            $winnerId = $rate['winner_id'];
            if ($winnerId === $_SESSION['user_id']) {
                $contacts = getUserContacts($userId);
                $modifiedRate['user_contacts'] = $contacts;
                $modifiedRate['rate_class'] = 'rates__item--win';
                $modifiedRate['timer_class'] = 'timer--win';
                $modifiedRate['timer_status'] = 'Ставка выиграла';
            } else {
                $modifiedRate['rate_class'] = 'rates__item--end';
                $modifiedRate['timer_class'] = 'timer--end';
                $modifiedRate['timer_status'] = 'Торги окончены';
            }
        } elseif (strtotime($rate['lot_end']) < time()) {
            $modifiedRate['rate_class'] = 'rates__item--end';
            $modifiedRate['timer_class'] = 'timer--end';
            $modifiedRate['timer_status'] = 'Торги окончены';
        } else {
            $remainingTime = getRemainingTime($rate['lot_end']);
            if ($remainingTime['remaining_hours'] <= 1) {
                $modifiedRate['timer_class'] = 'timer--finishing';
            }
            $modifiedRate['timer_status'] = $remainingTime['remaining_time'];
        }

        $rates[] = $modifiedRate;
    }
}

$contentAdress = 'bets.php';
$contentValues = [ 'categories' => $categories,
                   'rates' => $rates
                  ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => 'Мои ставки',
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
