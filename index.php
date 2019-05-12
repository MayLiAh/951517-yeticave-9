<?php
require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$lotsSql = "SELECT l.name AS name, l.id AS id, 
            c.name AS category, 
            current_cost, image, end_at 
            FROM lots AS l JOIN categories AS c 
            ON c.id = category_id
            WHERE l.end_at > CURDATE()
            ORDER BY l.created_at DESC";
    
$categoriesSql = "SELECT name, symbol_code FROM categories ORDER BY id";

$lots = getMysqlSelectionResult($con, $lotsSql);
$categories = getMysqlSelectionResult($con, $categoriesSql);

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

tagsTransforming('strip_tags', $newLots, $categories);

$contentAdress = 'index.php';
$contentValues = [ 'categories' => $categories,
                   'lots' => $newLots
                  ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => 'Главная',
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
