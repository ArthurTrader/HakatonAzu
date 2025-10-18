<?php
session_start();
include('db.php');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $direction = trim($_POST['direction']);
    $owner_id = $_SESSION['user_id'];

    // Загрузка логотипа
    $logo = "";
    if (!empty($_FILES['logo']['name'])) {
        $targetDir = "uploads/logos/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $logo = $targetDir . basename($_FILES["logo"]["name"]);
        move_uploaded_file($_FILES["logo"]["tmp_name"], $logo);
    }

    $stmt = $conn->prepare("INSERT INTO communities (name, description, owner_id, logo, direction) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $description, $owner_id, $logo, $direction);

    if ($stmt->execute()) {
    // Получаем ID только что созданного сообщества
    $community_id = $conn->insert_id;

    // Добавляем создателя в user_communities с ролью admin
    $sql_member = "INSERT INTO user_communities(user_id, community_id, role) VALUES (?, ?, 'admin')";
    $stmt_member = $conn->prepare($sql_member);
    $stmt_member->bind_param("ii", $owner_id, $community_id);
    $stmt_member->execute();
    $stmt_member->close();

    $message = "✅ Сообщество успешно создано!";
    } else {
    $message = "❌ Ошибка: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать сообщество</title>
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
        <h1>Создать новое сообщество</h1>
        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Название сообщества:</label><br>
            <input type="text" name="name" required><br><br>

            <label>Описание:</label><br>
            <textarea name="description" rows="4"></textarea><br><br>

            <label>Направление:</label><br>
            <input type="text" name="direction" placeholder="AI, робототехника и т.д."><br><br>

            <label>Логотип:</label><br>
            <input type="file" name="logo" accept="image/*"><br><br>

            <button type="submit">Создать</button>
        </form>
    </div>

    <footer style="background-color: rgb(0, 47, 255); padding: 10px;">
        <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
    </footer>
</body>
</html>