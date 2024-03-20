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
                    <form action="add_genre.php" method="post" enctype="multipart/form-data">
                        <button type="button" class="content-search__button" id="selectCoverButton">Выбрать
                            обложку</button>
                        <input type="file" name="cover_file" id="cover_file" accept=".png" style="display: none">
                        <p><a class="settings__link" id="cover"></a></p>
                        <div class="content-cover">
                            <img class="content-wrapper__image" id="coverImage" style="display: none">
                        </div>
                </div>
                <div class="content-main">

                    <div class="content_selects">

                        <h3 class="content-wrapper__text">Название жанра</h3>
                        <input class="content-head_add_input" type="text" name="name_genre" id="genre"
                            placeholder="Введите название">

                        <p><input class="content-search__button" type="submit" value="Добавить жанр"></strong></p>
                    </div>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
<!-- <?php echo php_ini_loaded_file(); ?> -->

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "select max(genreid) from genre";
    $result = $conn->query($sql);
    $id = $result->fetch_row();
    $id = $id[0] + 1;

    $genre = $_POST['name_genre'];

    $coverpath = "C:\\Games\\xampp\\htdocs\\musicmedia\\genre\\" . $id . ".png";

    $coverFile = $_FILES['cover_file'];

    move_uploaded_file($coverFile['tmp_name'], $coverpath);


    $stmt = $conn->prepare("INSERT INTO genre VALUES (NULL, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $genre, $coverpath);
        $stmt->execute();
        $stmt->close();
    } else {
        die ("Error in genre query: " . $conn->error);
    }
}

?>
<script src="/scripts_add.js"></script>