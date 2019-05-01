<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

if (isset($_POST['submit'])) {
    $tommorow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
    $errors = [];
    foreach ($_POST as $key => $value) {
        if (empty($value) && $key !== 'submit') {
            $errors[$key] = 'Поле должно быть заполнено!';
        } elseif ($key === 'category' && $value === 'Выберите категорию') {
            $errors[$key] = 'Выберите категорию';
        } elseif (($key === 'lot-rate' || $key === 'lot-step') && ($value <= 0 || floor($value) != $value)) {
            $errors[$key] = 'Введите корректную цену';
        } elseif ($key === 'lot-date' && (!is_date_valid($value) || strtotime($value) < $tommorow)) {
            $errors[$key] = 'Введите корректную дату';
        }
    }

    if (isset($_FILES['lot-img']) && !empty($_FILES['lot-img']['name'])) {
        $fileType = mime_content_type($_FILES['lot-img']['tmp_name']);
        if ($fileType !== 'image/png' && $fileType !== 'image/jpeg') {
            $errors['lot-img'] = 'Выберите файл формата .png, .jpeg или .jpg';
        }

        if (empty($errors)) {
            $fileName = $_FILES['lot-img']['name'];
            $filePath = __DIR__ . '/uploads/';
            $fileUrl = 'uploads/' . $fileName;

            move_uploaded_file($_FILES['lot-img']['tmp_name'], $filePath . $fileName);
        }
    }

    $name = $_POST['lot-name'];
    $message = $_POST['message'];
    $cost = $_POST['lot-rate'];
    $step = $_POST['lot-step'];
    $category = $_POST['category'];
    $date = $_POST['lot-date'];
    $img = $_POST['lot-img'];

    if (empty($errors)) {
        $categorySql = "SELECT id FROM categories WHERE name = '$category'";
        $categoryId = getMysqlSelectionAssocResult($con, $categorySql)['id'];
        $data = [$name, $message, $fileUrl, $cost, $step, $cost, 1, $categoryId, $date];
        $sql = "INSERT INTO lots 
                (name, about, image, start_cost, rate_step, current_cost, user_id, category_id, end_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $newLotId = insertDataMysql($con, $sql, $data);
        header("Location: lot.php?id=$newLotId");
    }
}

$categoriesSql = "SELECT name FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);

tagsTransforming('strip_tags', $categories);

$contentAdress = 'add.php';
$contentValues = [ 'categories' => $categories,
                   'lotName' => $name,
                   'message' => $message,
                   'cost' => $cost,
                   'step' => $step,
                   'img' => $img,
                   'date' => $date,
                   'errors' => $errors
                 ];

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => 'Добавление лота',
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
