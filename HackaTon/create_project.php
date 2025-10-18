<?php
session_start();
include('db.php');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

// Получаем список сообществ для выбора в форме
$communities = [];
$sql = "SELECT id, name FROM communities ORDER BY name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $communities[] = $row;
    }
}

// Обработка отправки формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $skills_required = trim($_POST['skills_required']);
    $community_id = intval($_POST['community_id']);
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("INSERT INTO projects (title, description, skills_required, community_id, deadline) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $title, $description, $skills_required, $community_id, $deadline);

    if ($stmt->execute()) {
        $message = "✅ Кейс успешно создан!";
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
    <title>Создать кейс</title>
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
        <h1>Создать новый кейс/проект</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Название кейса:</label><br>
            <input type="text" name="title" required><br><br>

            <label>Описание:</label><br>
            <textarea name="description" rows="4"></textarea><br><br>

            <label>Требуемые навыки:</label><br>
            <input type="text" name="skills_required" placeholder="Например: Python, CAD"><br><br>

            <label>Сообщество:</label><br>
            <select name="community_id" required>
                <option value="">Выберите сообщество</option>
                <?php foreach ($communities as $community): ?>
                    <option value="<?= $community['id'] ?>"><?= htmlspecialchars($community['name']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label>Дедлайн:</label><br>
            <input type="date" name="deadline"><br><br>

            <button type="submit">Создать кейс</button>
        </form>
    </div>

    <footer style="background-color: rgb(0, 47, 255); padding: 10px;">
        <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
    </footer>
</body>
</html>