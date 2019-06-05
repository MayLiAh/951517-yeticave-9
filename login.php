<?php
require_once 'helpers.php';
require_once 'functions.php';

$categories = getCategories();
$users = getUsers();

$contentAdress = 'login.php';
$contentValues = [ 'categories' => $categories,
                   'email' => '',
                   'errors' => []
                 ];

if (isset($_POST['submit'])) {
    $errors = checkFieldsFilling($_POST);
    $email = trim($_POST['email']);

    if (!isInArray($users, $email) && !empty($email)) {
        $errors['email'] = 'Пользователя с таким e-mail не существует';
    }

    if (empty($errors)) {
        $password = $_POST['password'];
        $user = getUserByEmail($email);
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
                 'pageTitle' => 'Вход',
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
