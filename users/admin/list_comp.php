<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 'off');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}
$criteria = $_GET['criteria'];
$sort_column = isset ($_GET['sort_column']) ? $_GET['sort_column'] : 'id';
$current_sort_order = isset ($_GET['sort_order']) ? $_GET['sort_order'] : 'desc';

$next_sort_order = $current_sort_order === 'asc' ? 'desc' : 'asc';
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
                    <a class="settings__link" href="add_comp.php">Добавление новой композиции</a>
                </div>
                <?php
                if ($criteria == 1) {
                    echo '<a class="settings__link" href="list_comp.php?criteria=1&sort_column=lisening">Статистика популярности треков</a>
                    <p><a class="settings__link" href="list_genre.php?criteria=1&sort_column=lisening">Статистика популярности жанров</a></p>
                    <p><a class="settings__link" href="list_artist.php?criteria=1&sort_column=lisening">Статистика популярности исполнителей</a></p>
                    ';
                }
                ?>
                <div class="content-main">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <?php
                                    echo '
                                        <th><a href="?criteria=' . $criteria . '&sort_column=id&sort_order=' . $next_sort_order . '">ID</a></th>
                                        <th>Обложка</th>
                                        <th><a href="?criteria=' . $criteria . '&sort_column=name&sort_order=' . $next_sort_order . '">Название</a></th>
                                        <th><a href="?criteria=' . $criteria . '&sort_column=artist&sort_order=' . $next_sort_order . '">Исполнитель</a></th>
                                        <th><a href="?criteria=' . $criteria . '&sort_column=genre&sort_order=' . $next_sort_order . '">Жанр</a></th>
                                        <th><a href="?criteria=' . $criteria . '&sort_column=date&sort_order=' . $next_sort_order . '">Дата добавления</a></th>
                                    ';
                                    if ($criteria == 1) {
                                        echo '<th><a href="?criteria=' . $criteria . '&sort_column=lisening&sort_order=' . $next_sort_order . '">Число прослушиваний</a></th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="table-content">
                                <?php
                                $sql = "SELECT c.compositionid id, c.name name,
                                    (select name from genre g where g.genreid = c.genreid) genre,
                                    (select name from artist a where a.artistid = c.artistid) artist,
                                    (select dateupload from audiofiles a where a.audiofileid = c.compositionid) date,
                                    (select count(liseningdate) from statistic s where s.audiofileid = c.compositionid) lisening,
                                    (select coverpath  from audiofiles a where a.audiofileid = c.compositionid)
                                    FROM composition c ORDER BY " . $sort_column . " " . $current_sort_order;
                                $result = $conn->query($sql);
                                $prefix = "C:\\Games\\xampp\\htdocs\\music";

                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_row($result)) {
                                        $image_url = str_replace("C:\\Games\\xampp\\htdocs\\music", "", $row[6]);

                                        if (!file_exists($prefix . $image_url)) {
                                            $image_url = "/media/unknown.png";
                                        }
                                        $url_update = 'update_comp.php?id=' . $row[0];
                                        $url_delete = 'delete_comp_process.php?id=' . $row[0];
                                        echo '
                                            <tr>
                                            <td>' . $row[0] . '</td>
                                            <td><img class="content-wrapper__image" src="' . $image_url . '" alt=""></td>
                                            <td>' . $row[1] . '</td>
                                            <td>' . $row[3] . '</td>
                                            <td>' . $row[2] . '</td>
                                            <td>' . $row[4] . '</td>';
                                        if ($criteria == 1) {
                                            echo '<td>' . $row[5] . '</td>';
                                        }
                                        echo '<td><button onclick="redirectToPage(\'' . $url_update . '\')" class="content-wrapper__buttons" >Редактировать</button></td>    
                                            <td><button onclick="redirectToPage(\'' . $url_delete . '\')" class="content-wrapper__buttons" >Удалить</button></td>    
                                            </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
<script src="/scripts.js"></script>

</html>