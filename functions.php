<?php
function PostDataHandler(): array
{
    return [
        "dateOfBirth" => $_POST["dateOfBirth"],
        "phone" => $_POST["phone"], 
        "email" => $_POST["email"], 
        "message" => $_POST["message"], 
        "surname" => $_POST["surname"], 
        "name" => $_POST["name"], 
        "secondName" => $_POST["secondName"]
    ];
}

function SendEmail(array $arUserData): bool
{
    $username = "Rti2paBWiMuH";
    $password = "H2kB2ibz5zMR";
    $host = "smtp.mailsnag.com";
    $port = "2525";
    $charset = "UTF-8";

    $from = $arUserData["email"];

    $smtp = new SendMailSmtpClass($username, $password, $host, $from, $port, $charset);

    $site = $_SERVER['SERVER_NAME'];
    $subject = "На сайте $site зарегестрирован новый пользователь";

    $message = "Сообщение пользователя " . $arUserData["surname"] . " " . $arUserData["name"] . " " . $arUserData["secondName"] . ":\n\n";
    $message .= $arUserData["message"];

    $mailTo = "chuck@example.com";
    $nameTo = "Chuck Berry";

    $headers = "To: $nameTo <$mailTo>\r\nReply-To: $from\r\nContent-Type: text/plain; charset=UTF-8\r\n";

    return $smtp->send($mailTo, $subject, $message, $headers);
}