<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: communities.php");
    exit;
}

$user_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT username, email, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Правильный путь к аватару
$avatar = !empty($user['avatar']) && file_exists('uploads/avatars/' . $user['avatar'])
          ? 'uploads/avatars/' . $user['avatar']
          : 'uploads/avatars/default.png';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
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
            <li><a href="search.php">Поиск</a></li>
            <li><a href="about.php">О нас</a></li>
            <li><a href="logout.php">Выход</a></li>
        </ul>
    </nav>
</div>

<div class="content">
    <h1><?= htmlspecialchars($user['username']) ?></h1>
    <img src="<?= htmlspecialchars($avatar) ?>" alt="Аватар" style="width:150px; height:auto; border-radius:50%;">
    <p><b>Электронная почта:</b> <?= htmlspecialchars($user['email']) ?></p>
</div>

<footer style="background-color: rgb(0, 47, 255); padding: 10px;">
    <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
</footer>
</body>
</html>