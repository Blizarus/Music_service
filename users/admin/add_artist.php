<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/style_add.css">

</head>

<body>
    <header class="header">
        <a href="../general_page.php">Музыкальный сервис</a>

    </header>
    <main class="main">
        <div class="container">
            <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

            <section class="content">
                <div class="content-head_add">
                    <form action="add_artist.php" method="post" enctype="multipart/form-data">
                        <button type="button" class="content-search__button" id="selectCoverButton">Выбрать обложку
                            исполнителя</button>
                        <input type="file" name="cover_file" id="cover_file" accept=".png" style="display: none">
                        <p><a class="settings__link" id="cover"></a></p>
                        <div class="content-cover">
                            <img class="content-wrapper__image" id="coverImage" style="display: none">
                        </div>
                </div>
                <div class="content-main">

                    <div class="content_selects">

                        <h3 class="content-wrapper__text">Имя исполнителя</h3>
                        <input class="content-head_add_input" type="text" name="name_artist" id="artist"
                            placeholder="Введите имя">

                        <h3 class="content-wrapper__text">Жанры</h3>
                        <div id="checkboxContainer" style="height: 200px; overflow: auto;">

                            <?php
                            $sql = "select * from genre order by name";
                            $Query = $conn->query($sql);
                            while ($row = mysqli_fetch_row($Query)) {
                                echo '
                            <div>
                                <input type="checkbox" id="' . $row[0] . '" value="' . $row[0] . '" name="options[]" >
                                <label for="' . $row[0] . '">' . $row[1] . '</label>
                            </div>
                            ';
                            }
                            ?>
                        </div>

                    </div>
                    <p><input class="content-search__button" type="submit" value="Добавить исполнителя"></strong></p>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $artist = $_POST['name_artist'];

    $coverpath = "C:\\Games\\xampp\\htdocs\\music\\media\\composition\\" . str_replace(' ', '_', $artist);

    // Проверяем существование директории, если не существует, создаем
    if (!file_exists($coverpath)) {
        mkdir($coverpath, 0777, true); // 0777 - права доступа, true - рекурсивное создание директории
    }

    $coverpath .= "\\" . str_replace(' ', '_', $artist) . ".png";

    $coverFile = $_FILES['cover_file'];

    move_uploaded_file($coverFile['tmp_name'], $coverpath);

    $stmt = $conn->prepare("INSERT INTO artist VALUES (NULL, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $artist, $coverpath);
        $stmt->execute();
        $newly_created_id = $stmt->insert_id;
        $stmt->close();
    } else {
        die ("Error in artist query: " . $conn->error);
    }


    if (isset ($_POST['options'])) {
        // Получаем выбранные значения чекбоксов
        $selectedOptions = $_POST['options'];

        // Выводим выбранные значения
        foreach ($selectedOptions as $option) {
            $stmt = $conn->prepare("INSERT INTO genre_artist VALUES (NULL, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ii", $newly_created_id, $option);
                $stmt->execute();
                $stmt->close();
            } else {
                die ("Error in genre_artist query: " . $conn->error);
            }
        }
    }
}

?>
<script src="/scripts_add.js"></script>