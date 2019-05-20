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
    //header("Location: pages/404.html");
}

$limit = 9;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = mysqli_real_escape_string($con, ($page - 1) * $limit);

$allLotsSql = "SELECT id FROM lots WHERE end_at > CURDATE() 
               AND winner_id IS NULL AND category_id = $categoryId";

$lotsCount = count(getMysqlSelectionResult($con, $allLotsSql));
$pagesCount = ceil($lotsCount / $limit);

$pages = [];

for ($i = 1; $i <= $pagesCount; $i++) {
    $pages[$i] = "href='lots-by-category.php?id=$categoryId&page=$i'";
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
            ORDER BY l.created_at DESC
            LIMIT $limit OFFSET $offset";

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
                   'lots' => $newLots,
                   'pages' => $pages
                  ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [ 'pageTitle' => $category['name'],
                  'categories' => $categories,
                  'pageContent' => $pageContent,
                  'categoryId' => $categoryId
                ];
                  
$pageLayout = include_template($layoutAdress, $layoutValues);
                  
print $pageLayout;