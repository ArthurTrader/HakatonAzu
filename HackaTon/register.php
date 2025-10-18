<?php
include('db.php');

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Проверка на уникальность имени пользователя
    $stmt_username = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt_username->bind_param("s", $username);
    $stmt_username->execute();
    $result_username = $stmt_username->get_result();

    // Проверка на уникальность email
    $stmt_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt_email->bind_param("s", $email);
    $stmt_email->execute();
    $result_email = $stmt_email->get_result();

    if ($result_username->num_rows > 0) {
        $message = "❌ Пользователь с таким именем уже существует.";
    } elseif ($result_email->num_rows > 0) {
        $message = "❌ Этот email уже используется.";
    } else {
        // Хешируем пароль
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $created_at = date("Y-m-d H:i:s");

        // Вставка нового пользователя
        $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $created_at);

        if ($stmt_insert->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $message = "❌ Ошибка при регистрации: " . $conn->error;
        }

        $stmt_insert->close();
    }

    // Закрываем все остальные запросы
    $stmt_username->close();
    $stmt_email->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-box">
            <h2>Регистрация</h2>

            <?php if (!empty($message)): ?>
                <div class="auth-message"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php" class="auth-form">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Электронная почта:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Зарегистрироваться</button>
            </form>
            <p>Уже есть аккаунт? <a href="login.php" class="register-link">Войти</a></p>
        </div>
    </div>
</body>
</html>