<?php
$con = mysqli_connect("localhost", "mayliah", "", "951517_yeticave_9");

if ($con == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}