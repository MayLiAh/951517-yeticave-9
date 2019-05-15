<?php

require_once 'connection.php';
require_once 'helpers.php';
require_once 'functions.php';

$withoutWinnersSql = "SELECT id FROM lots WHERE winner_id IS NULL
                      AND end_at <= CURDATE()";
$withoutWinners = getMysqlSelectionResult($con, $withoutWinnersSql);

foreach ($withoutWinners as $withoutWinner) {
    $lotId = mysqli_real_escape_string($con, $withoutWinner['id']);
    $lotsIdsSql = "SELECT lot_id FROM rates";
    $lotsIds = getMysqlSelectionResult($con, $lotsIdsSql);
    if (isInArray($lotsIds, $lotId)) {
        $winnerSql = "SELECT user_id FROM rates WHERE lot_id = $lotId
                      ORDER BY created_at
                      LIMIT 1 OFFSET 0";
        $winnerId = getMysqlSelectionAssocResult($con, $winnerSql)['user_id'];
        $data = [$winnerId, $lotId];
        $sql = "UPDATE lots SET winner_id = ? WHERE id = ?";
        $winner = insertDataMysql($con, $sql, $data);
    }   
}