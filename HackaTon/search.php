<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Поиск</title>
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
                <li><a href="search.php">Поиск</a></li>
                <li><a href="about.php">О нас</a></li>
                <li><a href="logout.php">Выход</a></li>
            </ul>
        </nav>
    </div>

    <div class="content">
        <h1>Поиск материалов</h1>
        <form method="GET" action="search.php">
            <label for="query">Введите запрос:</label>
            <input type="text" id="query" name="query" placeholder="Поиск материалов..." required>
            <button type="submit">Найти</button>
        </form>

        <?php
        if (isset($_GET['query'])) {
            $query = strtolower(trim($_GET['query']));
            $directory = "uploads"; // Папка, где хранятся материалы
            $files = scandir($directory);

            echo "<h2>Результаты поиска для: \"" . htmlspecialchars($query) . "\"</h2>";

            $results = [];
            foreach ($files as $file) {
                if ($file !== "." && $file !== "..") {
                    if (strpos(strtolower($file), $query) !== false) {
                        $results[] = $file;
                    }
                }
            }

            if (count($results) > 0) {
                echo '<div class="materials-grid">';
                foreach ($results as $result) {
                    echo '<div class="material-item">';
                    echo '<a href="' . $directory . '/' . urlencode($result) . '" target="_blank">' . htmlspecialchars($result) . '</a>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo "<p>Ничего не найдено.</p>";
            }
        }
        ?>
    </div>

    <footer style="background-color: rgb(0, 47, 255); padding: 10px;">
        <p><a style="color: rgb(255, 255, 255)" href="https://hackathon.otkroimosprom.ru/">Сайт для Хакатон Открой#Моспром</a></p>
    </footer>
</body>
</html>