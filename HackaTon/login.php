<?php
session_start();
include('db.php');

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Подготовка SQL-запроса с использованием подготовленных выражений
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $message = "❌ Неверный пароль.";
        }
    } else {
        $message = "❌ Пользователь не найден.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-box">
            <h2>Вход</h2>

            <?php if (!empty($message)): ?>
                <div class="auth-message"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="auth-form">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Войти</button>
            </form>

            <a class="auth-link" href="register.php">Нет аккаунта? Зарегистрируйтесь</a>
        </div>
    </div>
</body>
</html>