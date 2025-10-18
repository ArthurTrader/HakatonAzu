<?php
include('db.php');

$message = '';

// Проверка наличия токена в URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Проверка токена в базе данных
    $sql = "SELECT * FROM password_resets WHERE token = ? AND expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Токен действителен, форма для ввода нового пароля
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['password'];

            // Хешируем новый пароль
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Получаем email пользователя по токену
            $row = $result->fetch_assoc();
            $email = $row['email'];

            // Обновляем пароль в базе данных
            $sql_update = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                // Удаляем токен после использования
                $sql_delete = "DELETE FROM password_resets WHERE token = ?";
                $stmt = $conn->prepare($sql_delete);
                $stmt->bind_param("s", $token);
                $stmt->execute();

                $message = "✅ Пароль успешно обновлён!";
            } else {
                $message = "❌ Ошибка при обновлении пароля.";
            }
        }
    } else {
        $message = "❌ Токен недействителен или срок его действия истёк.";
    }
} else {
    $message = "❌ Не был указан токен.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Сброс пароля</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="reset_password.php?token=<?php echo $_GET['token']; ?>">
            <label for="password">Новый пароль:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Обновить пароль</button>
        </form>
    </div>
</body>
</html>