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
</head>

<body>
  <header class="header">
    <a href="../general_page.php">Музыкальный сервис</a>
  </header>
  <main class="main">
    <div class="container">
      <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

      <section class="content">
        <div class="content-head">
          <form action="artist_page.php" method="post" class="content-head__buttons-row">
            <?php

            $conn = new mysqli('music', 'root', '', 'music');
            if ($conn->connect_error) {
              die ("Connection failed: " . $conn->connect_error);
            }

            echo
              '<input class="content-head__input" type="text" id ="input"  placeholder="Поиск по исполнителю" name = "input">
              <button type="submit" class="content-head__button"><img class="content-head__image" src="/media/image/search2.svg" alt="ИконкаПоиска"></button>';
            ?>
          </form>
        </div>
        <div class="content-main">
          <div class="content-music" id="searchResults">
            <?php

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              $artist = $_POST['input'];
            } else
              $artist = '';


            $sql = "select artistid, name,
              (select count(liseningdate) from statistic s where s.audiofileid in 
              (select compositionid from composition c where c.artistid=a.artistid)),
              coverpath from artist a
              where LOWER(name) like LOWER('%" . $artist . "%')";
            $result = $conn->query($sql);

            $prefix = "C:\\Games\\xampp\\htdocs\\music";

            if ($result->num_rows > 0) {
              $i = 1;
              while ($row = mysqli_fetch_row($result)) {
                $url = 'search_music.php?id=' . $row[0] . '&criteria=artist&artist=' . $row[1] . '';
                $image_url = str_replace($prefix, "", $row[3]);
                if (!file_exists($prefix . $image_url)) {
                  $image_url = "/media/unknown.png";
                }
                echo '
                    <div class="content-wrapper">
                    <img class="content-wrapper__image" src="' . $image_url . '" alt="">
                    <div class="content-wrapper__info">
                      <h3 class="content-wrapper__text">' . $row[1] . '</h3>
                      <ul class="content-wrapper__list">
                        <li class="content-wrapper__list-item">
                            Число прослушиваний: ' . $row[2] . '
                        </li>
                      </ul>
                      <ul class="content-wrapper__list">
                        <li class="content-wrapper__list-item">
                            Жанры: ';
                $genresResult = $conn->query("select * from genre where genreid in (select genreid from genre_artist ga where ga.artistid=" . $row[0] . ")");
                $genres = array();
                while ($genre = mysqli_fetch_row($genresResult)) {
                  $genres[] = '<a class="settings__link" href="search_music.php?id=' . $genre[0] . '&criteria=genre&genre=' . $genre[1] . '">' . $genre[1] . '</a>';
                }
                echo implode(', ', $genres);
                echo '</li>
                      </ul>
                      <button onclick="redirectToPage(\'' . $url . '\')" class="content-wrapper__buttons" id="buttongenre' . $i . '" >Посмотреть композиции</button>
                    </div>
                    </div>
                    ';
                $i = $i + 1;
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

</html>