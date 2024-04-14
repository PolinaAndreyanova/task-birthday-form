<?php
include_once("functions.php");
include_once("smtp.php");

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

$feedback = "";

if ($_POST) {
    $arUserData = PostDataHandler();

    if (!in_array($arUserData, $_SESSION["users"]) && SendEmail($arUserData)) {
        $feedback = "Сообщение успешно отправлено!";
        $_SESSION["users"][] = $arUserData;
    };
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
        
        <input class="content__input" type="tel" name="phone" value="<?=$arUserData["phone"]?>" placeholder="+79275643843" required/>
        
        <input class="content__input" type="email" name="email" value="<?=$arUserData["email"]?>" placeholder="alex85@mail.ru" required/>

        <textarea class="content__textarea" name="message" placeholder="Текст сообщения" required><?=$arUserData["message"]?></textarea>

        <input class="content__input" type="text" name="surname" value="<?=$arUserData["surname"]?>" placeholder="Смирнов" required/>

        <input class="content__input" type="text" name="name" value="<?=$arUserData["name"]?>" placeholder="Александр" required/>

        <input class="content__input" type="text" name="secondName" value="<?=$arUserData["secondName"]?>" placeholder="Иванович" required/>

        <button class="content__button" type="submit">Зарегистрироваться</button>
        <p class="content__feedback content__feedback_type_success"><?=$feedback?></p>
    </form>
</body>
</html>