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

$community_id = intval($_GET['id']);

// Получаем данные сообщества
$stmt = $conn->prepare("SELECT c.*, u.username AS owner_name FROM communities c LEFT JOIN users u ON c.owner_id = u.id WHERE c.id = ?");
$stmt->bind_param("i", $community_id);
$stmt->execute();
$result = $stmt->get_result();
$community = $result->fetch_assoc();
$stmt->close();

// Получаем список участников
$stmt_members = $conn->prepare("
    SELECT uc.user_id, u.username, u.avatar
    FROM user_communities uc
    JOIN users u ON uc.user_id = u.id
    WHERE uc.community_id = ?
");
$stmt_members->bind_param("i", $community_id);
$stmt_members->execute();
$result_members = $stmt_members->get_result();
$members = [];
while ($row = $result_members->fetch_assoc()) {
    // Формируем правильный путь к аватарке
    $row['avatar'] = !empty($row['avatar']) && file_exists('uploads/avatars/' . $row['avatar'])
                     ? 'uploads/avatars/' . $row['avatar']
                     : 'uploads/avatars/default.png';
    $members[] = $row;
}
$stmt_members->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль сообщества</title>
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
    <h1><?= htmlspecialchars($community['name']) ?></h1>
    <?php if ($community['logo']): ?>
        <img src="<?= htmlspecialchars($community['logo']) ?>" alt="Логотип" style="width:150px; height:auto;">
    <?php endif; ?>
    <p><?= htmlspecialchars($community['description']) ?></p>
    <p><b>Направление:</b> <?= htmlspecialchars($community['direction']) ?></p>
    <p><b>Владелец:</b> <?= htmlspecialchars($community['owner_name']) ?></p>

    <h2>Участники (<?= count($members) ?>)</h2>
    <ul>
        <?php foreach ($members as $member): ?>
            <li>
                <a href="view_profile.php?id=<?= $member['user_id'] ?>">
                    <img src="<?= htmlspecialchars($member['avatar']) ?>" alt="Аватар" style="width:30px; height:30px; border-radius:50%; margin-right:5px;">
                    <?= htmlspecialchars($member['username']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<footer style="background-color: rgb(0, 47, 255); padding: 10px;">
    <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
</footer>
</body>
</html>