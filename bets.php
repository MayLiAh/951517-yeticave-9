<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

if (!isset($_SESSION['user_name'])) {
    http_response_code(403);
    header("Location: index.php");
}

$userId = $_SESSION['user_id'];
$rates = [];

$categoriesSql = "SELECT id, name FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);

$lotsIdsSql = "SELECT lot_id FROM rates WHERE user_id = $userId";
$lotsIds = getMysqlSelectionResult($con, $lotsIdsSql);

if (!empty($lotsIds)) {
    foreach ($lotsIds as $lotId) {
        $id = $lotId['lot_id'];
        $rateSql = "SELECT l.image AS lot_img, l.name AS lot_name, l.end_at AS lot_end,
                   c.name AS category_name, r.cost AS cost, r.created_at AS rate_time
                   FROM lots AS l JOIN categories AS c ON c.id = l.category_id 
                   JOIN rates AS r ON r.lot_id = l.id
                   WHERE l.id = $id 
                   ORDER BY r.created_at DESC";
        $rate = getMysqlSelectionAssocResult($con, $rateSql);
        $rates[] = $rate;
    }
}

tagsTransforming('strip_tags', $categories, $rates);

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
