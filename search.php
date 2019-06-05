<?php

require_once 'helpers.php';
require_once 'functions.php';

$categories = getCategories();
$search = '';

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$limit = 9;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($page != (int) $page || $page < 1) {
    header("Location: search.php?search=$search");
}
$offset = ($page - 1) * $limit;

$lotsCount = getActiveLotsCount('search', $search);
$pagesCount = ceil($lotsCount / $limit) > 0 ? ceil($lotsCount / $limit) : 1;

if ($page > $pagesCount) {
    header("Location: search.php?search=$search");
}

$pages = [];

for ($i = 1; $i <= $pagesCount; $i++) {
    $pages[$i] = "href='search.php?search=$search&page=$i'";
}

$contentAdress = 'search.php';
$contentValues = [ 'categories' => $categories,
                   'search' => $search,
                   'lots' => [],
                   'pages' => $pages
                  ];

if (!empty($search)) {
    $lots = getActiveLots($limit, $offset, 'search', $search);
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
