<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$categoriesSql = "SELECT id, name FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);

$usersSql = "SELECT id, full_name, email, password FROM users";
$users = getMysqlSelectionResult($con, $usersSql);
array_walk_recursive($users, function (&$value, $key) {
    $value = strip_tags($value);
});
array_walk_recursive($categories, function (&$value, $key) {
    $value = strip_tags($value);
});

$contentAdress = 'login.php';
$contentValues = [ 'categories' => $categories,
                   'email' => '',
                   'errors' => []
                 ];

if (isset($_POST['submit'])) {
    $errors = [];
    foreach ($_POST as $key => $value) {
        if (empty($value) && $key !== 'submit') {
            $errors[$key] = 'Поле должно быть заполнено!';
        } elseif ($key === 'email' && !isInArray($users, $value)) {
            $errors[$key] = 'Пользователя с таким e-mail не существует';
        }
    }

    $email = mysqli_real_escape_string($con, $_POST['email']);

    if (empty($errors)) {
        $password = $_POST['password'];
        $userSql = "SELECT id, full_name, password FROM users WHERE email = '$email'";
        $user = getMysqlSelectionAssocResult($con, $userSql);
        array_walk_recursive($user, function (&$value, $key) {
            $value = strip_tags($value);
        });
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
