<?php

require_once 'helpers.php';

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

$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

array_walk($categories, function (&$value, $key) {
    $value = strip_tags($value);
});

$goods = [ ['name' => '2014 Rossignol District Snowboard',
            'category' => 'Доски и лыжи',
            'price' => '10999',
            'imageUrl' => 'img/lot-1.jpg'],
           ['name' => 'DC Ply Mens 2016/2017 Snowboard',
           'category' => 'Доски и лыжи',
           'price' => '159999',
           'imageUrl' => 'img/lot-2.jpg'],
           ['name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
           'category' => 'Крепления',
           'price' => '8000',
           'imageUrl' => 'img/lot-3.jpg'],
           ['name' => 'Ботинки для сноуборда DC Mutiny Charocal',
           'category' => 'Ботинки',
           'price' => '10999',
           'imageUrl' => 'img/lot-4.jpg'],
           ['name' => 'Куртка для сноуборда DC Mutiny Charocal',
           'category' => 'Одежда',
           'price' => '7500',
           'imageUrl' => 'img/lot-5.jpg'],
           ['name' => 'Маска Oakley Canopy',
           'category' => 'Разное',
           'price' => '5400',
           'imageUrl' => 'img/lot-6.jpg']
];

array_walk_recursive($goods, function (&$value, $key) {
    $value = strip_tags($value);
});

$contentAdress = 'index.php';
$contentValues = [ 'categories' => $categories,
                   'goods' => $goods,
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
