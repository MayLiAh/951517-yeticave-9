<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

if (!isset($_SESSION['user_name'])) {
    http_response_code(403);
    header("Location: index.php");
}

$userId = mysqli_real_escape_string($con, $_SESSION['user_id']);
$rates = [];

$categoriesSql = "SELECT id, name FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);
array_walk_recursive($categories, function (&$value, $key) {
    $value = strip_tags($value);
});

$lotsIdsSql = "SELECT lot_id FROM rates WHERE user_id = $userId
               GROUP BY lot_id
               ORDER BY created_at DESC";
$lotsIds = getMysqlSelectionResult($con, $lotsIdsSql);

if (!empty($lotsIds)) {
    foreach ($lotsIds as $lotId) {
        $id = mysqli_real_escape_string($con, $lotId['lot_id']);
        $rateSql = "SELECT l.id AS lot_id, l.image AS lot_img, l.name AS lot_name, l.end_at AS lot_end,
                   l.winner_id AS winner_id, c.name AS category_name, r.cost AS cost, r.created_at AS rate_time
                   FROM lots AS l JOIN categories AS c ON c.id = l.category_id 
                   JOIN rates AS r ON r.lot_id = l.id
                   WHERE r.user_id = $userId AND l.id = $id
                   AND r.created_at = 
                   (SELECT MAX(r.created_at) FROM rates AS r
                   JOIN lots AS l ON lot_id = l.id
                   WHERE r.user_id = $userId AND l.id = $id)";
        $rate = getMysqlSelectionAssocResult($con, $rateSql);
        $modifiedRate = $rate;
        $modifiedRate['rate_class'] = '';
        $modifiedRate['timer_class'] = '';
        $modifiedRate['timer_status'] = '';
        $modifiedRate['user_contacts'] = '';

        if (!is_null($rate['winner_id'])) {
            $winnerId = mysqli_real_escape_string($con, $rate['winner_id']);
            if ($winnerId === $_SESSION['user_id']) {
                $contactsSql = "SELECT contacts FROM users WHERE id = $winnerId";
                $contacts = getMysqlSelectionAssocResult($con, $contactsSql);
                $modifiedRate['user_contacts'] = $contacts['contacts'];
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

array_walk_recursive($rates, function (&$value, $key) {
    $value = strip_tags($value);
});

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
