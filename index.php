<?php
include_once("smtp.php");
include_once("functions.php");

session_start();

if (!isset($_SESSION["users"])) {
    $_SESSION["users"] = [];
}

$arUserData = [
    "dateOfBirth" => "",
    "phone" => "", 
    "email" => "", 
    "message" => "", 
    "surname" => "", 
    "name" => "", 
    "secondName" => ""
];

$arValidationErrors = [];

$feedback = "";

if ($_POST) {
    $arUserData = PostDataHandler();
    $arValidationErrors = HandleValidation($arUserData);

    if (!in_array($arUserData, $_SESSION["users"])) {
        if (!$arValidationErrors) {
            if (SendEmail($arUserData)) {
                $feedback = "Сообщение успешно отправлено!<br>";
                $feedback .= HandleIsBirthdayToday($arUserData);
                $_SESSION["users"][] = $arUserData;
                $arValidationErrors = [];
            } else {
                $feedback = "При отправке произошла ошибка!";
            }
        } else {
            $feedback = "Данные не прошли валидацию!";
        }
    } else {
        $feedback = "Такие данные уже отправлены!<br>";
        $feedback .= HandleIsBirthdayToday($arUserData);
        $arValidationErrors = [];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./style.css" />
    <title>Форма</title>
</head>
<body class="content">
    <form class="content__form" method="post">
        <h1 class="content__header">Форма</h1>
        
        <input class="content__input" type="date" name="dateOfBirth" value="<?=$arUserData["dateOfBirth"]?>" placeholder="14.11.1985" required />
        <p class="content__feedback content__feedback_type_error"><?=HandleError("dateOfBirth", $arValidationErrors)?></p>
        
        <input class="content__input" type="tel" name="phone" value="<?=$arUserData["phone"]?>" placeholder="+79275643843" required/>
        <p class="content__feedback content__feedback_type_error"><?=HandleError("phone", $arValidationErrors)?></p>
        
        <input class="content__input" type="email" name="email" value="<?=$arUserData["email"]?>" placeholder="alex85@mail.ru" required/>
        <p class="content__feedback content__feedback_type_error"><?=HandleError("email", $arValidationErrors)?></p>

        <textarea class="content__textarea" name="message" placeholder="Текст сообщения" required><?=$arUserData["message"]?></textarea>
        <p class="content__feedback content__feedback_type_error"><?=HandleError("message", $arValidationErrors)?></p>

        <input class="content__input" type="text" name="surname" value="<?=$arUserData["surname"]?>" placeholder="Смирнов" required/>
        <p class="content__feedback content__feedback_type_error"><?=HandleError("surname", $arValidationErrors)?></p>

        <input class="content__input" type="text" name="name" value="<?=$arUserData["name"]?>" placeholder="Александр" required/>
        <p class="content__feedback content__feedback_type_error"><?=HandleError("name", $arValidationErrors)?></p>

        <input class="content__input" type="text" name="secondName" value="<?=$arUserData["secondName"]?>" placeholder="Иванович" required/>
        <p class="content__feedback content__feedback_type_error"><?=HandleError("secondName", $arValidationErrors)?></p>

        <button class="content__button" type="submit">Отправить</button>
        <p class="content__feedback content__feedback_type_success"><?=$feedback?></p>
    </form>
</body>
</html>