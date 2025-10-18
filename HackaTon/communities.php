<?php
session_start();
include('db.php');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Обработка кнопки «Вступить»
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['join_community'])) {
    $community_id = intval($_POST['community_id']);

    // Проверяем, не состоит ли уже пользователь в сообществе
    $check_sql = "SELECT * FROM user_communities WHERE user_id = ? AND community_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $community_id);
    $stmt->execute();
    $result_check = $stmt->get_result();

    if ($result_check->num_rows == 0) {
        // Если не состоит, добавляем с ролью 'member'
        $insert_sql = "INSERT INTO user_communities (user_id, community_id, role) VALUES (?, ?, 'member')";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("ii", $user_id, $community_id);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    $stmt->close();
}

$sql = "SELECT c.*, u.username AS owner_name 
        FROM communities c 
        LEFT JOIN users u ON c.owner_id = u.id 
        ORDER BY c.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сообщества</title>
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
        <h1>Все сообщества</h1>
        <div class="communities-list">
            <?php while ($row = $result->fetch_assoc()): ?>

                <?php
                // Считаем участников
                $count_sql = "SELECT COUNT(*) AS member_count FROM user_communities WHERE community_id = ?";
                $stmt_count = $conn->prepare($count_sql);
                $stmt_count->bind_param("i", $row['id']);
                $stmt_count->execute();
                $result_count = $stmt_count->get_result();
                $member_data = $result_count->fetch_assoc();
                $member_count = $member_data['member_count'];
                $stmt_count->close();
                ?>

                <?php
                // Проверяем, состоит ли пользователь в текущем сообществе
                $check_member_sql = "SELECT * FROM user_communities WHERE user_id = ? AND community_id = ?";
                $stmt_check = $conn->prepare($check_member_sql);
                $stmt_check->bind_param("ii", $user_id, $row['id']);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                $already_member = $result_check->num_rows > 0;
                $stmt_check->close();
                ?>
                
                <div class="community-card">
                    <?php if ($row['logo']): ?>
                        <img src="<?= htmlspecialchars($row['logo']) ?>" alt="Логотип" style="width:100px; height:auto;">
                    <?php endif; ?>
                    <h3>
                        <a href="community_profile.php?id=<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </a>
                    </h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <p><b>Направление:</b> <?= htmlspecialchars($row['direction']) ?></p>
                    <p><b>Владелец:</b> <?= htmlspecialchars($row['owner_name']) ?></p>
                    <p><b>Участников:</b> <?= $member_count ?></p>
                    <?php if (!$already_member): ?>
                        <form method="POST" style="margin-top:10px;">
                            <input type="hidden" name="community_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="join_community">Вступить</button>
                        </form>
                        <?php else: ?>
                            <p style="color:green;"><b>Вы участник сообщества</b></p>
                        <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer style="background-color: rgb(0, 47, 255); padding: 10px;">
        <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
    </footer>
</body>
</html>