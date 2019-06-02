<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$categoriesSql = "SELECT id, name FROM categories ORDER BY id";
$categories = getMysqlSelectionResult($con, $categoriesSql);

$emailsSql = "SELECT email FROM users";
$emails = getMysqlSelectionResult($con, $emailsSql);

$contentAdress = 'sing-up.php';
$contentValues = [ 'categories' => $categories,
                   'email' => '',
                   'newUserName' => '',
                   'message' => '',
                   'errors' => []
                 ];

$passwordReg = '^[0-9A-Za-z]^';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $errors = array_merge(checkFieldsFilling($_POST), checkEmail($email, $emails), checkPassword($pass, $passwordReg));

    $password = password_hash($pass, PASSWORD_DEFAULT);
    $newUserName = $_POST['name'];
    $message = $_POST['message'];

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
                 'categories' => $categories,
                 'pageContent' => $pageContent
                ];

$pageLayout = include_template($layoutAdress, $layoutValues);

print $pageLayout;
