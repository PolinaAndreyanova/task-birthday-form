<?php
function postDataHandler(): array
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

function validateName(string $name): bool
{
    $regex = '/^([а-яА-ЯёЁ]+)$/su';
    return preg_match($regex, $name);
}

function validateEmail(string $email): bool
{
    $regex = '/(^([a-zA-Z0-9][a-zA-Z0-9._-]{0,}[a-zA-Z0-9]{0,})@(?:[a-zA-Z0-9][a-zA-Z0-9_-]{0,}[a-zA-Z0-9]{0,}\.)+[a-zA-Z0-9][a-zA-Z0-9_-]{0,}[a-zA-Z0-9]{0,}$)/s';
    return preg_match($regex, $email);
}

function validatePhone(string $phone): bool
{
    $regex = '/^(\+[0-9]{11})$/s';
    return preg_match($regex, $phone);
}

function validateMessage(string $message): bool
{
    return $message !== "";
}

function validateDateOfBirth(string $date): bool
{
    return strtotime($date) <= strtotime("now");
}

function handleValidation(array $arUserData): array
{
    $arValidationErrors = [];

    if (!validatePhone($arUserData["phone"])) {
        $arValidationErrors["phone"] = "Поле не соответствует формату номера телефона (+XXXXXXXXXXX)";
    }

    if (!validateName($arUserData["surname"])) {
        $arValidationErrors["surname"] = "Поле должно быть заполнено кириллицей";
    }

    if (!validateName($arUserData["name"])) {
        $arValidationErrors["name"] = "Поле должно быть заполнено кириллицей";
    }

    if (!validateName($arUserData["secondName"])) {
        $arValidationErrors["secondName"] = "Поле должно быть заполнено кириллицей";
    }

    if (!validateEmail($arUserData["email"])) {
        $arValidationErrors["email"] = "Поле не соответствует формату email";
    }

    if (!validateMessage($arUserData["message"])) {
        $arValidationErrors["message"] = "Сообщение не может быть пустым";
    }

    if (!validateDateOfBirth($arUserData["dateOfBirth"])) {
        $arValidationErrors["dateOfBirth"] = "День рождения не может быть в будущем!";
    }

    return $arValidationErrors;
}

function handleError(string $key, array $arValidationErrors): string
{
    if (array_key_exists($key, $arValidationErrors)) { 
        return $arValidationErrors[$key];
    }
    return "";
}

function isBirthdayToday(string $date): int
{
    $today = new DateTime();
    $targetDate = new DateTime($date);

    $targetDate->setDate($today->format("Y"), $targetDate->format("m"), $targetDate->format("d"));

    $diff = $today->diff($targetDate);

    if ($diff->invert) {
        return 365 - $diff->days;
    }

    return $diff->days + 1;
}

function handleIsBirthdayToday(array $arUserData): string
{
    $daysTilBirthday = isBirthdayToday($arUserData["dateOfBirth"]);

    if ($daysTilBirthday === 365) {
        return " С Днем Рождения, " . $arUserData["name"] . "!";
    } else {
        return " До Вашего Дня Рождения " . $daysTilBirthday . " дней.";
    }
}

function sendEmail(array $arUserData): bool
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