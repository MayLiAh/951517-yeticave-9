<?php
require_once './vendor/autoload.php';

$withoutWinners = getExpiredLotsWithoutWinners();

foreach ($withoutWinners as $withoutWinner) {
    $lotId = $withoutWinner['id'];
    $lotName = $withoutWinner['name'];
    $lotsIds = getLotsIdsFromRates();
    if (isInArray($lotsIds, $lotId)) {
        $winnerId = getWinnerId($lotId);
        $data = [$winnerId, $lotId];
        $winner = setWinner($data);
        $user = getWinner($winnerId);
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
