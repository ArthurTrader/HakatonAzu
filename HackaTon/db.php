<?php
// Установим параметры для подключения к базе данных
$servername = "127.0.0.1";
$username = "root";
$password = ""; // текущий рабочий пароль пустой
$dbname = "user_auth";
$port = 3307;

// Создаем подключение
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Проверим соединение
if ($conn->connect_error) {
    die("Подключение не удалось: " . $conn->connect_error);
}
?>