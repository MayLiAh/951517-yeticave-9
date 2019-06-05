<?php

/**
 * Статичный класс, устанавливающий постоянное соединение с базой данных
 */
class Connection
{
    /**
     * Переменная хранящая ресурс соединения mysqli
     */
    private static $con;

    /**
     * Статичный метод для создания соединения
     *
     * @return mysqli Ресурс соединения
     */
    public static function getDbConnection()
    {
        if (self::$con && !is_null(self::$con)) {
            return self::$con;
        }

        self::$con = new mysqli("p:localhost", "mayliah", "", "951517_yeticave_9");

        if (self::$con->connect_errno) {
            exit("Не удалось подключиться к MySQL: (" . self::$con->connect_errno . ") " . self::$con->connect_error);
        }

        self::$con->set_charset("utf8");

        return self::$con;
    }
}
