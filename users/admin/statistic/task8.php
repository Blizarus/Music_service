<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/table.css">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/style_add.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <header class="header">
        <a href="../general_page.php">Музыкальный сервис</a>

    </header>
    <main class="main">
        <div class="container">
            <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

            <section class="content">
                <div class="content-main">
                    <div class="table-container">
                            <a class="content-search__button" href="task8.0.php?value=1" target="_blank">Расчет скользащих для общей статистики прослушиваний </a>
                            <a class="content-search__button" href="task8.0.php?value=2" target="_blank" >Расчет скользащих для жанра "Хип-хоп"</a>
                            <a class="content-search__button" href="task8.0.php?value=3" target="_blank" >Расчет скользащих для исполнителя "The Beatles"</a><br><br>
                            <a class="content-search__button" href="task8.1.php?value=1" target="_blank" >Восстановленные краевые значения для общей статистики прослушиваний</a>
                            <a class="content-search__button" href="task8.1.php?value=2" target="_blank" >Восстановленные краевые значения для жанра "Хип-хоп"</a>
                            <a class="content-search__button" href="task8.1.php?value=3" target="_blank" >Восстановленные краевые значения для исполнителя "The Beatles"</a><br><br>
                            <a class="content-search__button" href="task8.2.php?value=1" target="_blank" >Прогнозирование значений для общей статистики прослушиваний</a>
                            <a class="content-search__button" href="task8.2.php?value=2" target="_blank" >Прогнозирование значений для жанра "Хип-хоп"</a>
                            <a class="content-search__button" href="task8.2.php?value=3" target="_blank" >Прогнозирование значений для исполнителя "The Beatles"</a><br><br>

                            <a class="content-search__button" href="task9.php?value=3" target="_blank" >Задание 9</a><br><br>

                            <a class="content-search__button" href="task10.php?value=3" target="_blank" >Задание 10</a>
                       
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>

<script>
        function openPageWithValue(value) {
            var url1 = 'task8.0.php?value=' + value;
            var url2 = 'task8.1.php?value=' + value;
            var url3 = 'task8.2.php?value=' + value;

            window.open(url1, '_blank');
            window.open(url2, '_blank');
            window.open(url3, '_blank');
        }
    </script>

</html>