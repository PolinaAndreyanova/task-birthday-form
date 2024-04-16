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

function NameValidation(string $name): bool
{
    $regex = '/^([а-яА-ЯёЁ]+)$/s';
    return preg_match($regex, $name);
}

function EmailValidation(string $email): bool
{
    $regex = '/(^([a-zA-Z0-9][a-zA-Z0-9._-]{0,}[a-zA-Z0-9]{0,})@(?:[a-zA-Z0-9][a-zA-Z0-9_-]{0,}[a-zA-Z0-9]{0,}\.)+[a-zA-Z0-9][a-zA-Z0-9_-]{0,}[a-zA-Z0-9]{0,}$)/s';
    return preg_match($regex, $email);
}

function PhoneValidation(string $phone): bool
{
    $regex = '/^(\+[0-9]{11})$/s';
    return preg_match($regex, $phone);
}

function HandleValidation(array $arUserData): array
{
    $arValidationErrors = [];

    if (!PhoneValidation($arUserData["phone"])) {
        $arValidationErrors["phone"] = "Поле не соответствует формату номера телефона";
    }

    if (!NameValidation($arUserData["surname"])) {
        $arValidationErrors["surname"] = "Поле должно быть заполнено кириллицей";
    }

    if (!NameValidation($arUserData["name"])) {
        $arValidationErrors["name"] = "Поле должно быть заполнено кириллицей";
    }

    if (!NameValidation($arUserData["secondName"])) {
        $arValidationErrors["secondName"] = "Поле должно быть заполнено кириллицей";
    }

    if (!EmailValidation($arUserData["email"])) {
        $arValidationErrors["email"] = "Поле не соответствует формату email";
    }

    if (!$arUserData["message"]) {
        $arValidationErrors["message"] = "Сообщение не может быть пустым";
    }

    if (strtotime($arUserData["dateOfBirth"]) > strtotime("now")) {
        $arUserData["dateOfBirth"] = "День рождения не может быть в будущем!";
    }

    return $arValidationErrors;
}

function HandleError(string $key, array $arValidationErrors): string
{
    if (array_key_exists($key, $arValidationErrors)) { 
        return $arValidationErrors[$key];
    }
    return "";
}

function IsBirthdayToday(string $date): int
{
    $today = new DateTime();
    $targetDate = new DateTime($date);

    $targetDate->setDate($today->format("Y"), $targetDate->format("m"), $targetDate->format("d"));

    $diff = $today->diff($targetDate);
    
    if ($diff->invert) {
        $targetDate->modify("+1 year");
        $diff = $today->diff($targetDate);
    }
    
    return $diff->days + 1;
}

function HandleIsBirthdayToday(array $arUserData): string
{
    $isBirthdayToday = IsBirthdayToday($arUserData["dateOfBirth"]);

    if ($isBirthdayToday === 365) {
        return " С Днем Рождения, " . $arUserData["name"] . "!";
    } else {
        return " До Вашего Дня Рождения " . $isBirthdayToday . " дней";
    }
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
    $subject = "На сайте $site новое сообщение";

    $message = "Сообщение пользователя " . $arUserData["surname"] . " " . $arUserData["name"] . " " . $arUserData["secondName"] . ":\n\n";
    $message .= $arUserData["message"];

    $mailTo = "chuck@example.com";
    $nameTo = "Chuck Berry";

    $headers = "To: $nameTo <$mailTo>\r\nReply-To: $from\r\nContent-Type: text/plain; charset=UTF-8\r\n";

    return $smtp->send($mailTo, $subject, $message, $headers);
}