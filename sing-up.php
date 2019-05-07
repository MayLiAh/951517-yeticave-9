<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$categoriesSql = "SELECT id, name FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);
tagsTransforming('strip_tags', $categories);

$emailsSql = "SELECT email FROM users";
$emails = getMysqlSelectionResult($con, $emailsSql);

$contentAdress = 'sing-up.php';
$contentValues = [ 'categories' => $categories,
                   'email' => '',
                   'newUserName' => '',
                   'message' => '',
                   'errors' => []
                 ];

if (isset($_POST['submit'])) {
    $errors = [];
    foreach ($_POST as $key => $value) {
        if (empty($value) && $key !== 'submit') {
            $errors[$key] = 'Поле должно быть заполнено!';
        } elseif ($key === 'email' && isInArray($emails, $value)) {
            $errors[$key] = 'Уже существует пользователь с таким e-mail';
        } elseif ($key === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[$key] = 'Введите корректный e-mail';
        } elseif ($key === 'password' && strlen($value) < 6) {
            $errors[$key] = 'Пароль не должен быть короче 6 символов';
        }
    }

    $email = $_POST['email'];
    $newUserName = $_POST['name'];
    $message = $_POST['message'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $contentValues['email'] = $email;
    $contentValues['newUserName'] = $newUserName;
    $contentValues['message'] = $message;
    $contentValues['errors'] = $errors;

    if (empty($errors)) {
        $data = [$newUserName, $email, $password, $message];
        $sql = "INSERT INTO users 
                (full_name, email, password, contacts)
                VALUES (?, ?, ?, ?)";

        $newUser = insertDataMysql($con, $sql, $data);
        header("Location: login.php");
    }
}

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => 'Регистрация',
                 'isAuth' => $isAuth,
                 'userName' => $userName,
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;