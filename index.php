<?php
require_once 'helpers.php';
require_once 'functions.php';
require_once 'winners.php';

$limit = 9;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($page != (int) $page || $page < 1) {
    header("Location: index.php");
}
$offset = ($page - 1) * $limit;

$lots = getActiveLots($limit, $offset);
$categories = getCategories();
$lotsCount = getActiveLotsCount();
$pagesCount = ceil($lotsCount / $limit) > 0 ? ceil($lotsCount / $limit) : 1;

if ($page > $pagesCount) {
    header("Location: index.php");
}

$pages = [];

for ($i = 1; $i <= $pagesCount; $i++) {
    $pages[$i] = "href='index.php?page=$i'";
}

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

$contentAdress = 'index.php';
$contentValues = [ 'categories' => $categories,
                   'lots' => $newLots,
                   'pages' => $pages
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
