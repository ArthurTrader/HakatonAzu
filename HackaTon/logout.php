<?php
// Начинаем сессию
session_start();

// Удаляем все сессионные переменные
session_unset();

// Уничтожаем сессию
session_destroy();

// Перенаправляем на страницу авторизации (login.php)
header("Location: login.php");
exit;
?>