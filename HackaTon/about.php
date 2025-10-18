<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Определяем текущую страницу
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="sidebar">
        <h2>Сообщество предприятий и молодежи</h2>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="communities.php">Сообщества</a></li>
                <li><a href="create_community.php">Создать сообщество</a></li>
                <li><a href="projects.php">Кейсы и проекты</a></li>
                <li><a href="create_project.php">Создать кейс</a></li>
                <li><a href="about.php">О нас</a></li>
                <li><a href="logout.php">Выход</a></li>
            </ul>
        </nav>
    </div>

    <div class="content">
        <h1>Добро пожаловать на платформу для совместной работы студентов и выпускников с предприятиями!</h1>
    </div>

    <div class="base">
        <h2>Наша команда состоит из энтузиастов, для которых Хакатон — это не только веселье, но и возможность показать себя.
            <p>Мы понимаем, как важно предприятиям иметь связь с новыми молодыми специалистами, особенно в условиях интенсивного дефицита кадров.
            <p>Мы хотим, чтобы студенты и выпускники имели удобный доступ к сообществу, где могли легко найти , делиться опытом и развиваться вместе.
            <p>Мы решили отказаться от громозкого сайта в пользу интуитивно понятного сайта, так как когда проще понять как работает сайт - с ним легче работать и делиться знанями с другими.
            <p>Мы придерживаемся принципов открытости, доступности и взаимопомощи.
            <p>Спасибо, что выбрали нас!
    </div>

    <footer style="background-color: rgb(0, 47, 255); padding: 10px;">
        <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
    </footer>
</body>
</html>