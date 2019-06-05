<?php

require_once 'helpers.php';
require_once 'functions.php';

$categoryId = '';
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];
} else {
    http_response_code(404);
    header("Location: pages/404.html");
}

$categories = getCategories();

if (!isInArray($categories, $categoryId)) {
    http_response_code(404);
    header("Location: pages/404.html");
}

$limit = 9;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($page != (int) $page || $page < 1) {
    header("Location: lots-by-category.php?id=$categoryId");
}
$offset = ($page - 1) * $limit;

$lotsCount = getActiveLotsCount('by-category', $categoryId);
$pagesCount = ceil($lotsCount / $limit) > 0 ? ceil($lotsCount / $limit) : 1;

if ($page > $pagesCount) {
    header("Location: lots-by-category.php?id=$categoryId");
}

$pages = [];

for ($i = 1; $i <= $pagesCount; $i++) {
    $pages[$i] = "href='lots-by-category.php?id=$categoryId&page=$i'";
}

$category = getCategoryById($categoryId);
$lots = getActiveLots($limit, $offset, 'by-category', $categoryId);
$newLots = [];
foreach ($lots as $lot) {
    $lotId = $lot['id'];
    $count = getLotRatesCount($lotId);
    $costType = 'Стартовая цена';
    if ($count > 0) {
        $rateForm = get_noun_plural_form($count, 'ставка', 'ставки', 'ставок');
        $costType = "$count $rateForm";
    }
    $lot['cost_type'] = $costType;
    $newLots[] = $lot;
}

$contentAdress = 'lots-by-category.php';
$contentValues = [ 'categories' => $categories,
                   'categoryName' => $category,
                   'lots' => $newLots,
                   'pages' => $pages
                  ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [ 'pageTitle' => $category,
                  'categories' => $categories,
                  'pageContent' => $pageContent,
                  'categoryId' => $categoryId
                ];
                  
$pageLayout = include_template($layoutAdress, $layoutValues);
                  
print $pageLayout;
