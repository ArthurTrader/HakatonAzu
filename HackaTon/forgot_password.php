<?php
include('db.php');

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Проверим, существует ли пользователь с таким email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Генерация уникального токена для сброса пароля
        $token = bin2hex(random_bytes(50)); // Генерация токена длиной 50 байт

        // Сохраняем токен в базе данных с сроком действия (например, 1 час)
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));
        $sql = "INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $token, $expiry);
        $stmt->execute();

        // Формирование ссылки для сброса пароля
        $reset_link = "http://yourdomain.com/reset_password.php?token=" . $token;

        // Отправка письма с ссылкой
        $subject = "Восстановление пароля";
        $message_body = "Для восстановления пароля перейдите по следующей ссылке:\n" . $reset_link;
        $headers = "From: no-reply@yourdomain.com";

        if (mail($email, $subject, $message_body, $headers)) {
            $message = "✅ Письмо с инструкциями отправлено на ваш email.";
        } else {
            $message = "❌ Ошибка при отправке письма.";
        }
    } else {
        $message = "❌ Пользователь с таким email не найден.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Восстановление пароля</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="forgot_password.php">
            <label for="email">Электронная почта:</label>
            <input type="email" id="email" name="email" required><br><br>

            <button type="submit">Отправить ссылку для восстановления</button>
        </form>
    </div>
</body>
</html>