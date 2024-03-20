<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}
$id = $_GET['id'];
$sql = "select * from artist where artistid = " . $id;
$result = $conn->query($sql);
$artist = $result->fetch_row();
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
                    <form action="update_artist.php?id=<?php echo urlencode($id); ?>" method="post"
                        enctype="multipart/form-data">
                        <button type="button" class="content-search__button" id="selectCoverButton">Изменить
                            обложку</button>
                        <input type="file" name="cover_file" id="cover_file" accept=".png" style="display: none">
                        <p><a class="settings__link" id="cover"></a></p>
                        <div class="content-cover">
                            <img class="content-wrapper__image" id="coverImage" style="display: none">
                        </div>
                </div>
                <div class="content-main">

                    <div>
                        <p>
                            <?php echo '<a class="settings__link">Оригинальная обложка:</a>' ?>
                        </p>
                        <div class="content-cover">
                            <img class="content-wrapper__image" id="originalImage"
                                src="<?php echo ((str_replace("/\\/", "/\/", str_replace("C:\\Games\\xampp\\htdocs\\music\\www", "", $artist[2])))) ?>">
                        </div>
                    </div>
                    <div class="content_selects">

                        <h3 class="content-wrapper__text">Исполнитель</h3>
                        <input class="content-head_add_input" type="text" name="name_artist" id="artist"
                            placeholder="Введите название" value="<?php echo $artist[1]; ?>">


                        <h3 class="content-wrapper__text">Жанр</h3>
                        <div id="checkboxContainer" style="height: 200px; overflow: auto;">

                            <?php

                            $sqlGenres = "SELECT * FROM genre  order by name";
                            $resultGenres = $conn->query($sqlGenres);

                            $sqlSelectedGenresBefore = "SELECT genreid FROM genre_artist WHERE artistid = $id";
                            $resultSelectedGenresBefore = $conn->query($sqlSelectedGenresBefore);
                            $selectedGenresBefore = array();

                            while ($rowSelectedGenresBefore = mysqli_fetch_row($resultSelectedGenresBefore)) {
                                $selectedGenresBefore[] = $rowSelectedGenresBefore[0];
                            }
                            echo '
                            <ul>';
                            while ($rowGenres = mysqli_fetch_row($resultGenres)) {
                                $isChecked = in_array($rowGenres[0], $selectedGenresBefore) ? 'checked' : '';
                                echo '
                            <li class="checkbox">
                                <input class="checkbox-pop" type="checkbox" id="' . $rowGenres[0] . '" value="' . $rowGenres[0] . '" name="options[]" ' . $isChecked . '>
                                <label for="' . $rowGenres[0] . '">' . $rowGenres[1] . '</label>
                            </li>
                            ';
                            }
                            echo '
                            </ul>';
                            ?>
                        </div>

                        <p><input class="content-search__button" type="submit" value="Изменить исполнителя"></strong>
                        </p>
                    </div>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $coverpath = "C:\\Games\\xampp\\htdocs\\music\\wwwmedia\\composition\\" . str_replace(' ', '_', $artist) . "\\" . str_replace(' ', '_', $artist) . ".png";

    if (!empty ($_FILES['cover_file']['tmp_name'])) {

        $coverFile = $_FILES['cover_file'];
        move_uploaded_file($coverFile['tmp_name'], $coverpath);

    }

    $artist = $_POST['name_artist'];

    $stmt = $conn->prepare("UPDATE artist SET name = ? WHERE artistid = ?");
    if ($stmt) {
        $stmt->bind_param("si", $artist, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        die ("Error in composition query: " . $conn->error);
    }

    $selectedGenresAfter = isset ($_POST['options']) ? (array) $_POST['options'] : array();  // Преобразование в массив

    // Определите, какие жанры были добавлены и удалены
    $addedGenres = array_diff($selectedGenresAfter, $selectedGenresBefore);
    $deletedGenres = array_diff($selectedGenresBefore, $selectedGenresAfter);
    foreach ($addedGenres as $addedGenre) {
        $stmt = $conn->prepare("INSERT INTO genre_artist VALUES (NULL, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ii", $id, $addedGenre);
            $stmt->execute();
            $stmt->close();
        } else {
            die ("Error in genre_artist query: " . $conn->error);
        }
    }

    foreach ($deletedGenres as $deletedGenre) {
        $conn->query("delete from genre_artist where genreid=" . $deletedGenre . "");
    }
}
?>
<script src="/scripts_add.js"></script>

</html>