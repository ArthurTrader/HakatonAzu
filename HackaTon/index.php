<?php
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Если нет — перенаправляем на страницу входа
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="index-page">
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
        <h1>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    </div>

    <div class="base">
        <h2>Данный сайт предназначен для оказания помощи студентам и выпускникам в работе над актуальными кейсами от предприятий.
        <p>Мы понимаем, как важно иметь доступ к качественным источникам информации, поэтому сайт сделан максимально удобным и понятным.
        </ul>
        </h2>
        <img src="img/stud_search.jpg" alt="1">
    </div>

    <footer style="background-color: rgb(0, 47, 255); padding: 10px;">
        <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
    </footer>
</body class="index-page">
</html>