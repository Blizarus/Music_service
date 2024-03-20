<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 'off');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="sort_/table.css">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/style_add.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
                    <form action="analize_page.php" method="post" enctype="multipart/form-data">
                        <div class="table-container">
                            <button type="button" class="content-search__button" id="selectMusicButton">Выбрать
                                музыку</button>
                            <input type="file" name="music_file" id="music_file" accept=".mp3" style="display: none">
                            <p><a class="content-wrapper__text" id="file"></a></p>
                        </div>
                        <div>
                            <?php
                            if (isset ($_SESSION['login'])) {
                                echo '
                            <input type="checkbox" name="auto_send" id="auto_send" checked>
                            <label for="auto_send">Автоматическая отправка результатов анализа на почту
                                пользователя</label>';
                            }

                            ?>
                        </div>
                        <br></br>
                        <p><input class="content-search__button" type="submit" value="Провести анализ"></strong></p>
                    </form>
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $musicFile = $_FILES['music_file'];

                        $filename = $musicFile['name'];

                        $targetFile = "C:\\Games\\xampp\\htdocs\\music\\wwwanalize\\tmp\\tmp.mp3";
                        move_uploaded_file($musicFile['tmp_name'], $targetFile);
                        require_once ($_SERVER['DOCUMENT_ROOT'] . '/analize/call_python.php');
                        $predicted_genre = isset ($genre) ? $genre : 'Не проведен анализ';
                        $predicted_bpm = isset ($bpm) ? $bpm : 'Не проведен анализ';
                        $predicted_tone = isset ($tone) ? implode(', ', $tone) : 'Не проведен анализ';
                        require_once ($_SERVER['DOCUMENT_ROOT'] . '/analize/add_statistic_analize.php');
                        require_once ($_SERVER['DOCUMENT_ROOT'] . '/users/admin/add_mailrequest.php');
                        $conn->close(); ?>
                        <p class="content-wrapper__text">Предположительный жанр:
                            <?php echo $predicted_genre; ?>
                        </p>
                        <p class="content-wrapper__text">Предположительный BPM:
                            <?php echo $predicted_bpm; ?>
                        </p>
                        <p class="content-wrapper__text">Предположительная тональность:
                            <?php echo $predicted_tone; ?>
                        </p>
                    <?php } ?>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
<script src="/scripts_add.js"></script>