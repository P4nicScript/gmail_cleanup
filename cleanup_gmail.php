<?php

require __DIR__ . "/vendor/autoload.php";

use Ddeboer\Imap\Exception\MailboxDoesNotExistException;
use Ddeboer\Imap\Server;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__."/.env");

$server = new Server($_ENV["IMAP_URL"]);

$connection = $server->authenticate($_ENV["IMAP_USER"], $_ENV["IMAP_PW"]);

$mb = $connection->getMailboxes();

$inbox = $connection->getMailbox("INBOX");
$inboxMessages = $inbox->getMessages();

foreach($inboxMessages as $message) {
    $adress = $message->getFrom()->getAddress();
    $domname =  explode("@", $adress)[1];
    $adname = explode("@", $adress)[0];

    try{
        $connection->getMailbox($domname);
    } catch(MailboxDoesNotExistException $e) {
        $connection->createMailbox($domname);
    }

    $toMailbox = $connection->getMailbox($domname);
    $message->move($toMailbox);
}

echo "finished.";
exit(0);