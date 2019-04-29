<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

mysqli_set_charset($con, "utf8");

$lotsSql = "SELECT lots.name AS name, 
lots.id AS id, categories.name AS category, 
current_cost, image, end_at 
FROM lots JOIN categories ON categories.id = category_id
ORDER BY lots.created_at DESC";
    
$categoriesSql = "SELECT name, symbol_code FROM categories ORDER BY id";

$lots = getMysqlSelectionResult($con, $lotsSql);
$categories = getMysqlSelectionResult($con, $categoriesSql);

tagsTransforming('strip_tags', $lots, $categories);

$isAuth = rand(0, 1);
$userName = 'Майя';

$contentAdress = 'index.php';
$contentValues = [ 'categories' => $categories,
                   'lots' => $lots
                  ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => 'Главная',
                 'isAuth' => $isAuth,
                 'userName' => $userName,
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
