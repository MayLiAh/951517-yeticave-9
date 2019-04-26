<?php

require_once 'helpers.php';

$con = mysqli_connect("localhost", "mayliah", "", "951517_yeticave_9");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
} else {
    mysqli_set_charset($con, "utf8");

    $lotsSql = "SELECT lots.name AS name, categories.name AS category, current_cost, image 
    FROM lots JOIN categories ON categories.id = category_id
    ORDER BY lots.created_at DESC";
    
    $categoriesSql = "SELECT name, symbol_code FROM categories ORDER BY id";

    $lotsResult = mysqli_query($con, $lotsSql);
    $categoriesResult = mysqli_query($con, $categoriesSql);

    if (!$lotsResult) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
    } else {
        $lots = mysqli_fetch_all($lotsResult, MYSQLI_ASSOC);
    }

    if (!$categoriesResult) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
    } else {
        $categories = mysqli_fetch_all($categoriesResult, MYSQLI_ASSOC);
    }
}

array_walk_recursive($lots, function (&$value, $key) {
    $value = strip_tags($value);
});

array_walk_recursive($categories, function (&$value, $key) {
    $value = strip_tags($value);
});


const SECONDS_IN_MINUTE = 60;
const SECONDS_IN_HOUR = 3600;

$now = time();
$tommorow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
$remainingTime = $tommorow - $now;
$remainingHours = floor($remainingTime / SECONDS_IN_HOUR);
$remainingMinutes = floor(($remainingTime % SECONDS_IN_HOUR) / SECONDS_IN_MINUTE);
$format = "%02d:%02d";
$formattedRemainingTime = sprintf($format, $remainingHours, $remainingMinutes);

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
                   'remainingHours' => $remainingHours,
                   'formattedRemainingTime' => $formattedRemainingTime
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
