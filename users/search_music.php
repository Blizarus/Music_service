<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/style_search.css">
</head>

<body>
    <header class="header">
        <?php
        ?>
        <a href="../general_page.php">Музыкальный сервис</a>

        <div class="music-progress">
            <audio id="audioPlayer" controls style="display: none">
            </audio>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <?php require_once ('../settings.php');
            ini_set('display_errors', 'off');


            $conn = new mysqli('music', 'root', '', 'music');
            if ($conn->connect_error) {
                die ("Connection failed: " . $conn->connect_error);
            }
            $customerid = $_SESSION['id'];
            $criteria = isset ($_GET['criteria']) ? $_GET['criteria'] : '';
            $id = isset ($_GET['id']) ? $_GET['id'] : '';
            if ($criteria === 'genre') {
                $Query = $conn->query("select name from genre where genreid = " . $id);
                $genre = $_GET['genre'];
            } else if ($criteria === 'artist') {
                $Query = $conn->query("select name from artist where artistid = " . $id);
                $artist = $_GET['artist'];
            } else {
                $id = -1;
                $Query = $conn->query("select name from composition where compositionid = " . $id);
            }
            $Data = mysqli_fetch_row($Query);
            ?>
            <section class="content">
                <div class="content-head">
                    <form id="searchForm"
                        action="search_music.php?id=<?php echo urlencode($id); ?>&criteria=<?php echo urlencode($criteria); ?>"
                        method="post" class="content-head__buttons-row">
                        <?php
                        echo '<h3 class="content-wrapper__text">' . $Data[0] . '</h3>';

                        $sql = "select * from criteria";
                        $result = $conn->query($sql);

                        $i = 1;
                        while ($row = mysqli_fetch_row($result)) {
                            if ($row[2] == $criteria) {
                                $sql = "select name from " . $criteria . " where " . $criteria . "id = " . $id . "";
                                $value = mysqli_fetch_row($conn->query($sql));
                                echo '<input class="content-head__input" type="text" id ="input' . $i . '" style="display: none" placeholder="Поиск ' . $row[1] . '" name = "input' . $i . '" value=' . $value[0] . '>';
                            } else {
                                echo '<button type="button" class="content-search__button" onclick="hideButtonAndShowInput(this.id, ' . $i . ')" id ="button' . $i . '"> <img src="/media/image/plus.svg" alt="ИконкаДобавления" >Поиск ' . $row[1] . '</button> ';
                                echo '<input class="content-head__input" type="text" id ="input' . $i . '" style="display: none" placeholder="Поиск ' . $row[1] . '" name = "input' . $i . '">';
                            }
                            echo
                                '
                            <button type="button" onclick="hideInputAndShowButton(this.id, ' . $i . ')" class="content-head__button" id ="button_hide' . $row[0] . '" style="display: none" >
                                <img class="content-head__image" src="/media/image/cross.svg" alt="ИконкаКрестика">
                            </button>';
                            $i = $i + 1;
                        }
                        echo
                            ' <button type="submit" class="content-head__button"><img class="content-head__image" src="/media/image/search2.svg" alt="ИконкаПоиска"></button>'
                            ?>
                    </form>
                </div>
                <div class="content-main">
                    <div class="content-music" id="searchResults">
                        <?php

                        if (!isset ($genre))
                            $genre = $_POST['input1'];
                        $name = $_POST['input2'];
                        if (!isset ($artist))
                            $artist = $_POST['input3'];
                        $sql = "
                            select c.name,
                            (select name from genre g where g.genreid = c.genreid),
                            (select name from artist a where a.artistid = c.artistid),
                            (select duration from сharacteristics_music cm where cm.audiofileid = c.compositionid),
                            (select t.name from tonality t, сharacteristics_music cm where t.tonalityid = cm.tonality and
                            cm.audiofileid = c.compositionid) ,
                            (select bpm from сharacteristics_music cm where cm.audiofileid = c.compositionid) ,
                            (select count(liseningdate) from statistic s where s.audiofileid = c.compositionid) lisening,
                            (select presencevoice from сharacteristics_music cm where cm.audiofileid = c.compositionid),
                            (select coverpath from audiofiles a where a.audiofileid = c.compositionid),
                            (select dateupload from audiofiles a where a.audiofileid = c.compositionid) date,
                            c.artistid,
                            c.genreid,
                            (select filepath from audiofiles a where a.audiofileid = c.compositionid),
                            c.compositionid
                            from composition c
                            where artistid in (select artistid from artist where LOWER(name) like LOWER('%" . $artist . "%'))
                            and genreid in (select genreid from genre where LOWER(name) like LOWER('%" . $genre . "%'))
                            and LOWER(name) like LOWER('%" . $name . "%') ";

                        if ($criteria == 'news1')
                            $sql = $sql . "order by date DESC limit 25";
                        if ($criteria == 'news2')
                            $sql = $sql . "order by lisening DESC limit 25";
                        if ($criteria == 'news3')
                            $sql = $sql . "
                                and c.genreid in (
                                select genreid from genre_artist
                                where artistid in(
                                select artistid from composition cmp
                                where cmp.compositionid in (
                                select audiofileid from statistic s
                                where s.customerid = (select customerid from login_password where login='" . $_SESSION['login'] . "'))
                                )
                                )
                                and c.compositionid not in(
                                select compositionid from composition cmp
                                where cmp.compositionid in (
                                select audiofileid from statistic s
                                where s.customerid = '" . $_SESSION['login'] . "')
                                )
                                order by rand() limit 25";
                        if ($criteria == 'news4')
                            $sql = $sql . "
                                and c.genreid in (
                                select genreid from genre_artist
                                where artistid in(
                                select artistid from composition cmp
                                where cmp.compositionid in (
                                select audiofileid from statistic s
                                where s.customerid = (select customerid from login_password where login='" . $_SESSION['login'] . "'))
                                )
                                )
                                and c.compositionid not in(
                                select compositionid from composition cmp
                                where cmp.compositionid in (
                                select audiofileid from statistic s
                                where s.customerid = (select customerid from login_password where login='" . $_SESSION['login'] . "'))
                                )
                                order by date DESC limit 25";
                        $result = $conn->query($sql);

                        $prefix = "C:\\Games\\xampp\\htdocs\\music\\www";

                        if ($result->num_rows > 0) {
                            while ($row = mysqli_fetch_row($result)) {
                                $image_url = str_replace($prefix, "", $row[8]);
                                $audio_url = rawurlencode((str_replace("/\\/", "/\/", str_replace($prefix, "", $row[12]))));
                                if (!file_exists($prefix . $image_url)) {
                                    $image_url = "/media/unknown.png";
                                }
                                echo '
                                    <div class="content-wrapper">
                                    <img class="content-wrapper__image" src="' . $image_url . '" alt="">
                                    <div class="content-wrapper__info">
                                        <h3 class="content-wrapper__text">' . $row[0] . '</h3>
                                        <ul class="content-wrapper__list">
                                            <li class="content-wrapper__list-item">
                                                Исполнитель: <a class="settings__link" href="search_music.php?id=' . $row[10] . '&criteria=artist&artist=' . $row[2] . '">' . $row[2] . '</a>
                                            </li>
                                            <li class="content-wrapper__list-item">
                                                Жанр: <a class="settings__link" href="search_music.php?id=' . $row[11] . '&criteria=genre&genre=' . $row[1] . '">' . $row[1] . '</a>
                                            </li>
                                            <li class="content-wrapper__list-item">
                                                Количество прослушиваний: ' . $row[6] . '
                                            </li>
                                            <li class="content-wrapper__list-item" style="display: none" id="info1' . $row[13] . '"> 
                                                Продолжительность: ' . $row[3] . '
                                            </li>
                                            <li class="content-wrapper__list-item" style="display: none" id="info2' . $row[13] . '">
                                                Наличие голоса: ' . $row[7] . '
                                            </li>
                                            <li class="content-wrapper__list-item" style="display: none" id="info3' . $row[13] . '">
                                                Тональность: ' . $row[4] . '
                                            </li>
                                            <li class="content-wrapper__list-item" style="display: none" id="info4' . $row[13] . '">
                                                BPM (Ударов в минуту): ' . $row[5] . '
                                            </li>
                                        </ul>
                                        <button onclick="hideButtonAndShowInfo(this.id, ' . $row[13] . ')" class="content-wrapper__buttons" id="buttoninfo' . $row[13] . '" >Показатель все характеристики</button>
                                    </div>
                                    <button class="content-play__button" onclick="playAudio(\'' . $audio_url . '\', ' . $row[13] . ')">
                                        <img class="content-head__image" src="/media/image/play.png" alt="ИконкаПлей">
                                    </button>
                                    </div>
                                    ';
                            }
                        }

                        $conn->close();
                        ?>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
<script src="/scripts.js"></script>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset ($customerid)) {

    $conn = new mysqli('music', 'root', '', 'music');
    if ($conn->connect_error) {
        die ("Connection failed: " . $conn->connect_error);
    }

    $genre_hystory = $_POST['input1'];
    $name_hystory = $_POST['input2'];
    $artist_hystory = $_POST['input3'];

    $date = date("Y-m-d");
    if (!empty ($genre_hystory)) {
        $criteria = 1;
        $stmt = $conn->prepare("INSERT INTO search_history VALUES (NULL, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isis", $customerid, $date, $criteria, $genre_hystory);
            $stmt->execute();
            $stmt->close();
        } else {
            die ("Error in audiofiles query: " . $conn->error);
        }
    }
    if (!empty ($name_hystory)) {
        $criteria = 2;
        $stmt = $conn->prepare("INSERT INTO search_history VALUES (NULL, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isis", $customerid, $date, $criteria, $name_hystory);
            $stmt->execute();
            $stmt->close();
        } else {
            die ("Error in audiofiles query: " . $conn->error);
        }
    }
    if (!empty ($artist_hystory)) {
        $criteria = 3;
        $stmt = $conn->prepare("INSERT INTO search_history VALUES (NULL, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isis", $customerid, $date, $criteria, $artist_hystory);
            $stmt->execute();
            $stmt->close();
        } else {
            die ("Error in audiofiles query: " . $conn->error);
        }
    }
}
?>

</html>