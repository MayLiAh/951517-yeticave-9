<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$categoriesSql = "SELECT id, name FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);

$usersSql = "SELECT id, full_name, email, password FROM users";
$users = getMysqlSelectionResult($con, $usersSql);

$contentAdress = 'login.php';
$contentValues = [ 'categories' => $categories,
                   'email' => '',
                   'errors' => []
                 ];

if (isset($_POST['submit'])) {
    $errors = checkFieldsFilling($_POST);
    $email = mysqli_real_escape_string($con, $_POST['email']);

    if (!isInArray($users, $email)) {
        $errors['email'] = 'Пользователя с таким e-mail не существует';
    }

    if (empty($errors)) {
        $password = $_POST['password'];
        $userSql = "SELECT id, full_name, password FROM users WHERE email = '$email'";
        $user = getMysqlSelectionAssocResult($con, $userSql);
        $rightPassword = $user['password'];

        if (!password_verify($password, $rightPassword)) {
            $errors['password'] = 'Неверный пароль';
        }
    }

    $contentValues['email'] = $email;
    $contentValues['errors'] = $errors;

    if (empty($errors)) {
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
    }
}

$pageContent = include_template($contentAdress, $contentValues);

$layoutAdress = 'layout.php';
$layoutValues = [
                 'pageTitle' => 'Регистрация',
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
