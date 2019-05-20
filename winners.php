<?php
require_once './vendor/autoload.php';
require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$withoutWinnersSql = "SELECT id, name FROM lots WHERE winner_id IS NULL
                      AND end_at <= CURDATE()";
$withoutWinners = getMysqlSelectionResult($con, $withoutWinnersSql);

foreach ($withoutWinners as $withoutWinner) {
    $lotId = mysqli_real_escape_string($con, $withoutWinner['id']);
    $lotName = $withoutWinner['name'];
    $lotsIdsSql = "SELECT lot_id FROM rates";
    $lotsIds = getMysqlSelectionResult($con, $lotsIdsSql);
    if (isInArray($lotsIds, $lotId)) {
        $winnerSql = "SELECT user_id FROM rates WHERE lot_id = $lotId
                      ORDER BY created_at DESC
                      LIMIT 1 OFFSET 0";
        $winnerId = getMysqlSelectionAssocResult($con, $winnerSql)['user_id'];
        $data = [$winnerId, $lotId];
        $sql = "UPDATE lots SET winner_id = ? WHERE id = ?";
        $winner = insertDataMysql($con, $sql, $data);
        $userSql = "SELECT email, full_name FROM users WHERE id = $winnerId";
        $user = getMysqlSelectionAssocResult($con, $userSql);
        $email = $user['email'];
        $userName = $user['full_name'];

        $contentAdress = 'email.php';
        $contentValues = [ 'userName' => $userName,
                           'lotId' => $lotId,
                           'lotName' => $lotName
                         ];
        $emailContent = include_template($contentAdress, $contentValues);

        $transport = (new Swift_SmtpTransport('phpdemo.ru', 25))
        ->setUsername('keks@phpdemo.ru')
        ->setPassword('htmlacademy');

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message('Ваша ставка победила'))
        ->setFrom(['keks@phpdemo.ru' => 'keks@phpdemo.ru'])
        ->setTo([$email => $userName])
        ->setBody($emailContent, 'text/html');

        $result = $mailer->send($message);
    }   
}