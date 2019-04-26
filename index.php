<?php

require_once 'helpers.php';
require_once 'functions.php';

$con = mysqli_connect("localhost", "mayliah", "", "951517_yeticave_9");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
} else {
    mysqli_set_charset($con, "utf8");

    $lotsSql = "SELECT lots.name AS name, 
    categories.name AS category, 
    current_cost, image 
    FROM lots JOIN categories ON categories.id = category_id
    ORDER BY lots.created_at DESC";
    
    $categoriesSql = "SELECT name, symbol_code FROM categories ORDER BY id";

    $lots = getMysqlSelectionResult($con, $lotsSql);
    $categories = getMysqlSelectionResult($con, $categoriesSql);
}

array_walk_recursive($lots, function (&$value, $key) {
    $value = strip_tags($value);
});

array_walk_recursive($categories, function (&$value, $key) {
    $value = strip_tags($value);
});

$tommorow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
$remainingTime = getRemainingTime($tommorow);

$getFormattedPrice = function ($price, $currency = '₽') {
    $intPrice = (int) $price;
    $roundPrice = ceil($intPrice);

    $formattedPrice = $roundPrice < 1000 ? $roundPrice : number_format($roundPrice, 0, '.', ' ');

    return "$formattedPrice $currency";
};

$isAuth = rand(0, 1);
$userName = 'Майя';

$contentAdress = 'index.php';
$contentValues = [ 'categories' => $categories,
                   'lots' => $lots,
                   'getFormattedPrice' => $getFormattedPrice,
                   'remainingTime' => $remainingTime
                  ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'title' => 'Главная',
                 'isAuth' => $isAuth,
                 'userName' => $userName,
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
