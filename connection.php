<?php
$con = mysqli_connect("localhost", "mayliah", "", "951517_yeticave_9");

if ($con == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8");

$isAuth = rand(0, 1);
$userName = 'Майя';
$userId = 1;