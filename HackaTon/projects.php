<?php
session_start();
include('db.php');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT p.*, c.name AS community_name 
        FROM projects p 
        LEFT JOIN communities c ON p.community_id = c.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кейсы и проекты</title>
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
        <h1>Кейсы и проекты</h1>
        <div class="projects-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="project-card">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <p><b>Навыки:</b> <?= htmlspecialchars($row['skills_required']) ?></p>
                    <p><b>Сообщество:</b> <?= htmlspecialchars($row['community_name']) ?></p>
                    <p><b>Дедлайн:</b> <?= htmlspecialchars($row['deadline']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer style="background-color: rgb(0, 47, 255); padding: 10px;">
        <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
    </footer>
</body>
</html>