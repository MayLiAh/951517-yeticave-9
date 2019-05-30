<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

if (!isset($_SESSION['user_name'])) {
    http_response_code(403);
    header("Location: login.php");
}

$categoriesSql = "SELECT id, name FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);
array_walk_recursive($categories, function (&$value, $key) {
    $value = strip_tags($value);
});

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
    $tommorow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
    $errors = [];
    foreach ($_POST as $key => $value) {
        if (empty($value) && $key !== 'submit') {
            $errors[$key] = 'Поле должно быть заполнено!';
        } elseif (($key === 'lot-rate' || $key === 'lot-step') && ($value <= 0 || floor($value) != $value)) {
            $errors[$key] = 'Введите корректную цену';
        } elseif ($key === 'lot-date' && (!is_date_valid($value) || strtotime($value) < $tommorow)) {
            $errors[$key] = 'Введите корректную дату';
        }
    }

    $fileName = isset($_FILES['lot-img']['name']) ? $_FILES['lot-img']['name'] : '';
    $fileUrl = !empty($fileName) ? "uploads/$fileName" : '';

    if (isset($_FILES['lot-img']) && !empty($_FILES['lot-img']['name'])) {
        $fileType = mime_content_type($_FILES['lot-img']['tmp_name']);
        if ($fileType !== 'image/png' && $fileType !== 'image/jpeg') {
            $errors['lot-img'] = 'Выберите файл формата .png, .jpeg или .jpg';
        }

        if (empty($errors)) {
            $filePath = __DIR__ . '/uploads/';

            move_uploaded_file($_FILES['lot-img']['tmp_name'], $filePath . $fileName);
        }
    } else {
        $errors['lot-img'] = 'Изображение обязательно к добавлению!';
    }

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
        $sql = "INSERT INTO lots 
                (name, about, image, start_cost, rate_step, current_cost, user_id, category_id, end_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $newLotId = insertDataMysql($con, $sql, $data);
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
