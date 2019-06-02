<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$categoriesSql = "SELECT id, name, symbol_code FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);

$search = '';

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$search = mysqli_real_escape_string($con, $search);

$limit = 9;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($page != (int) $page || $page < 1) {
    header("Location: search.php?search=$search");
}
$offset = mysqli_real_escape_string($con, ($page - 1) * $limit);

$allLotsSql = "SELECT id FROM lots WHERE MATCH(name, about) AGAINST('$search')
               AND end_at > CURDATE()
               AND winner_id IS NULL";
$lotsCount = count(getMysqlSelectionResult($con, $allLotsSql));
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
    $lotsSql = "SELECT l.name AS name, l.about AS about,
                l.id AS id, c.name AS category, 
                current_cost, image, end_at 
                FROM lots AS l JOIN categories AS c 
                ON c.id = category_id
                WHERE MATCH(l.name, l.about) AGAINST('$search')
                AND l.end_at > CURDATE()
                AND winner_id IS NULL
                ORDER BY l.created_at DESC
                LIMIT $limit OFFSET $offset";
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
