<?php
session_start();
include('db.php');

// Проверка, что пользователь авторизован
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Получаем текущие данные пользователя
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$message = '';

// Обработка формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    $avatar_updated = false;

    // Проверяем, не занят ли новый username или email (если их меняли)
    if (($new_username !== $user['username']) || ($new_email !== $user['email'])) {
        $check_sql = "SELECT * FROM users WHERE (username = ? AND id != ?) OR (email = ? AND id != ?)";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("sisi", $new_username, $user_id, $new_email, $user_id);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result->num_rows > 0) {
            $message = "❌ Пользователь с таким именем или email уже существует.";
        }
    }

    // Если нет ошибок уникальности — продолжаем обновление
    if (empty($message)) {
        $updates = [];
        $params = [];
        $types = '';

        // Если имя изменилось
        if ($new_username !== $user['username']) {
            $updates[] = "username = ?";
            $params[] = $new_username;
            $types .= 's';
            $_SESSION['username'] = $new_username;
        }

        // Если email изменился
        if ($new_email !== $user['email']) {
            $updates[] = "email = ?";
            $params[] = $new_email;
            $types .= 's';
        }

        // Если указан новый пароль
        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $updates[] = "password = ?";
            $params[] = $hashed;
            $types .= 's';
        }

        // Если загружен новый аватар
        if (!empty($_FILES['avatar']['name'])) {
            $avatar_name = basename($_FILES['avatar']['name']);
            $target_path = 'uploads/avatars/' . $avatar_name;

            // Проверка типа файла
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['avatar']['type'], $allowed_types)) {
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                    $updates[] = "avatar = ?";
                    $params[] = $avatar_name;
                    $types .= 's';
                    $avatar_updated = true;
                } else {
                    $message .= "❌ Ошибка при загрузке файла аватара.<br>";
                }
            } else {
                $message .= "❌ Разрешены только JPG, PNG, GIF.<br>";
            }
        }

        // Выполняем обновление, если есть что обновлять
        if (!empty($updates)) {
            $sql_update = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            $params[] = $user_id;
            $types .= 'i';

            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $message .= "✅ Изменения успешно сохранены!";
                // Обновляем данные пользователя на странице
                $sql = "SELECT * FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
            } else {
                $message .= "❌ Ошибка при сохранении изменений.";
            }
        } else {
            if (empty($message)) {
                $message = "⚠️ Нет изменений для сохранения.";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
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
        <h1>Редактировать профиль</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?= $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="profile.php" enctype="multipart/form-data">
            <label>Аватар:</label><br>
            <img src="uploads/avatars/<?= htmlspecialchars($user['avatar'] ?? 'default.png'); ?>" 
                 alt="Аватар" style="width:100px;height:100px;border-radius:50%;object-fit:cover;"><br><br>
            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif"><br><br>

            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required><br><br>

            <label for="email">Электронная почта:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required><br><br>

            <label for="password">Новый пароль:</label>
            <input type="password" id="password" name="password"><br>
            <small>Оставьте поле пустым, если не хотите менять пароль.</small><br><br>

            <button type="submit">Сохранить изменения</button>
        </form>
    </div>

    <footer style="background-color: rgb(0, 47, 255); padding: 10px;">
        <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
    </footer>
</body>
</html>