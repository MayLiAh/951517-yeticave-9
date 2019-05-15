<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$categoryId = '';
if (isset($_GET['id'])) {
    $categoryId = mysqli_real_escape_string($con, $_GET['id']);
} else {
    http_response_code(404);
    header("Location: pages/404.html");
}

$categoriesIdsSql = "SELECT id FROM categories";
$ids = getMysqlSelectionResult($con, $categoriesIdsSql);

if (!isInArray($ids, $categoryId)) {
    http_response_code(404);
    header("Location: pages/404.html");
}

$categoriesSql = "SELECT id, name, symbol_code FROM categories ORDER BY id";
$categorySql = "SELECT name FROM categories WHERE id = $categoryId";
$lotsSql = "SELECT l.name AS name, l.id AS id, 
            c.name AS category, 
            current_cost, image, end_at 
            FROM lots AS l JOIN categories AS c 
            ON c.id = category_id
            WHERE l.end_at > CURDATE()
            AND l.category_id = $categoryId
            AND winner_id IS NULL
            ORDER BY l.created_at DESC";

$categories = getMysqlSelectionResult($con, $categoriesSql);
$category = getMysqlSelectionAssocResult($con, $categorySql);
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

array_walk_recursive($newLots, function (&$value, $key) {
    $value = strip_tags($value);
});
array_walk_recursive($categories, function (&$value, $key) {
    $value = strip_tags($value);
});
array_walk_recursive($category, function (&$value, $key) {
    $value = strip_tags($value);
});

$contentAdress = 'lots-by-category.php';
$contentValues = [ 'categories' => $categories,
                   'categoryName' => $category['name'],
                   'lots' => $newLots
                  ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [ 'pageTitle' => $category['name'],
                  'categories' => $categories,
                  'pageContent' => $pageContent
                ];
                  
$pageLayout = include_template($layoutAdress, $layoutValues);
                  
print $pageLayout;