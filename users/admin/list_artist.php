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
                    <a class="settings__link" href="add_artist.php">Добавление нового исполнителя</a>
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
                                    <th><a href="?criteria=' . $criteria . '&sort_column=name&sort_order=' . $next_sort_order . '">Исполнитель</a></th>
                                    <th>Жанры</th>';
                                    if ($criteria == 1) {
                                        echo '<th><a href="?criteria=' . $criteria . '&sort_column=lisening&sort_order=' . $next_sort_order . '">Число прослушиваний</a></th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="table-content">
                                <?php
                                $sql = "SELECT artistid id, name name, coverpath,
                                (select count(liseningdate) from statistic s where s.audiofileid in 
                                (select compositionid from composition c where c.artistid=a.artistid)) lisening
                                 from artist a ORDER BY " . $sort_column . " " . $current_sort_order;

                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_row($result)) {
                                        $url_update = 'update_artist.php?id=' . $row[0];
                                        $url_delete = 'delete_artist_process.php?id=' . $row[0];
                                        $image_url = str_replace("C:\\Games\\xampp\\htdocs\\music\\www", "", $row[2]);
                                        if (@file_get_contents($image_url) == false) {
                                            $image_url = "/media/unknown.png";
                                        }
                                        echo '
                                            <tr>
                                            <td>' . $row[0] . '</td>
                                            <td><img class="content-wrapper__image" ondblclick="changeImage(' . $row[0] . ')" src="' . $image_url . '" alt=""></td>
                                            <td>' . $row[1] . '</td>
                                            <td>';
                                        $genresResult = $conn->query("select * from genre where genreid in (select genreid from genre_artist ga where ga.artistid=" . $row[0] . ")");
                                        $genres = array();
                                        while ($genre = mysqli_fetch_row($genresResult)) {
                                            $genres[] = '<p>' . $genre[1] . '</p>';
                                        }
                                        echo implode(' ', $genres);

                                        echo '</td>';
                                        if ($criteria == 1) {
                                            echo '<td>' . $row[3] . '</td>';
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
<script src="sort_table.js"></script>

</html>