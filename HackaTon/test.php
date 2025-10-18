<?php
$servername = "localhost";
$username = "root";
$password = "12345678"; // или "" если без пароля
$dbname = "user_auth";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Ошибка подключения: " . $conn->connect_error);
}
echo "✅ Успешное подключение к MySQL!";
?>