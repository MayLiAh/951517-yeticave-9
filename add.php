<?php

require_once 'helpers.php';
require_once 'functions.php';

if (!isset($_SESSION['user_name'])) {
    http_response_code(403);
    header("Location: login.php");
}

$categories = getCategories();

$contentAdress = 'add.php';
$contentValues = [ 'categories' => $categories,
                   'lotName' => '',
                   'categoryId' => '',
                   'message' => '',
                   'cost' => '',
                   'step' => '',
                   'date' => '',
                   'errors' => []
                 ];

if (isset($_POST['submit'])) {
    $rate = $_POST['lot-rate'];
    $rateStep = $_POST['lot-step'];
    $date = $_POST['lot-date'];
    $errors = array_merge(checkFieldsFilling($_POST), checkLotFields($rate, $rateStep, $date));

    $errors = array_merge($errors, checkAddFile($_FILES, 'image/png', 'image/jpeg'));

    $fileName = isset($_FILES['lot-img']['name']) ? $_FILES['lot-img']['name'] : '';
    $fileUrl = !empty($fileName) ? "uploads/$fileName" : '';

    $name = $_POST['lot-name'];
    $message = $_POST['message'];
    $cost = $_POST['lot-rate'];
    $step = $_POST['lot-step'];
    $categoryId = $_POST['category'];
    $date = $_POST['lot-date'];
    $userId = $_SESSION['user_id'];

    if (!isInArray($categories, $categoryId)) {
        $errors['category'] = 'Выберите категорию';
    }

    $contentValues['lotName'] = $name;
    $contentValues['message'] = $message;
    $contentValues['cost'] = $cost;
    $contentValues['step'] = $step;
    $contentValues['date'] = $date;
    $contentValues['categoryId'] = $categoryId;
    $contentValues['errors'] = $errors;

    if (empty($errors) && !empty($fileUrl)) {
        $data = [$name, $message, $fileUrl, $cost, $step, $cost, $userId, $categoryId, $date];
        $newLotId = setNewLot($data);
        header("Location: lot.php?id=$newLotId");
    }
}

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => 'Добавление лота',
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
