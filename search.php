<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$categoriesSql = "SELECT name, symbol_code FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);
tagsTransforming('strip_tags', $categories);

$search = '';

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$search = mysqli_real_escape_string($con, $search);

$contentAdress = 'search.php';
$contentValues = [ 'categories' => $categories,
                   'search' => $search,
                   'lots' => []
                  ];

if (!empty($search)) {
    $lotsSql = "SELECT l.name AS name, l.about AS about,
                l.id AS id, c.name AS category, 
                current_cost, image, end_at 
                FROM lots AS l JOIN categories AS c 
                ON c.id = category_id
                WHERE MATCH(l.name, l.about) AGAINST('$search')
                AND l.end_at > CURDATE()
                ORDER BY l.created_at DESC";
    $lots = getMysqlSelectionResult($con, $lotsSql);
    $newLots = [];
    foreach ($lots as $lot) {
        $lotId = mysqli_real_escape_string($con, $lot['id']);
        $countSql = "SELECT * FROM rates
                     WHERE lot_id = $lotId";
        $count = count(getMysqlSelectionResult($con, $countSql));
        $costType = 'Стартовая цена';
        if ($count > 0) {
            $rateForm = get_noun_plural_form($count, 'ставка', 'ставки', 'ставок');
            $costType = "$count $rateForm";
        }
        $lot['cost_type'] = $costType;
        $newLots[] = $lot;
    }
    tagsTransforming('strip_tags', $newLots);
    $contentValues['lots'] = $newLots;
}

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => 'Главная',
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;