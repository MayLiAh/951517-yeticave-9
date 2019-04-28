<?php

require_once 'helpers.php';
require_once 'functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    http_response_code(404);
    header("Location: pages/404.html");
}

$con = mysqli_connect("localhost", "mayliah", "", "951517_yeticave_9");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
} else {
    mysqli_set_charset($con, "utf8");

    $lotsIdsSql = "SELECT id FROM lots";
    $ids = getMysqlSelectionResult($con, $lotsIdsSql);

    if (!isInArray($ids, $id)) {
        http_response_code(404);
        header("Location: pages/404.html");
    }

    $lotsSql = "SELECT lots.name AS name, 
    categories.name AS category,
    about, current_cost,
    rate_step, image, end_at 
    FROM lots JOIN categories ON categories.id = category_id 
    WHERE lots.id = $id
    ORDER BY lots.created_at DESC";
    
    $categoriesSql = "SELECT name FROM categories ORDER BY id";

    $lot = getMysqlSelectionAssocResult($con, $lotsSql);
    $categories = getMysqlSelectionResult($con, $categoriesSql);
}

array_walk_recursive($lot, function (&$value, $key) {
    $value = strip_tags($value);
});

array_walk_recursive($categories, function (&$value, $key) {
    $value = strip_tags($value);
});

$contentAdress = 'lot.php';
$contentValues = [ 'categories' => $categories,
                   'lot' => $lot
                  ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'title' => $lot['name'],
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;