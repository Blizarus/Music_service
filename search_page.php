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
      <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

      <section class="content">
        <div class="content-head">
          <form action="search_page.php" method="post" class="content-head__buttons-row">
            <?php
            $conn = new mysqli($servername, $username, $password, $dbname);

            $conn = new mysqli('music', 'root', '', 'music');
            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }


            $sql = "select * from criteria";
            $result = $conn->query($sql);
            while ($row = mysqli_fetch_row($result)) {
              echo '
                    <button type="button" class="content-search__button" onclick="hideButtonAndShowInput(this.id)" id ="button' . $row[0] . '"> <img src="/media/image/plus.svg" alt="ИконкаДобавления" >Поиск ' . $row[1] . '</button>

                    ';
            }

            mysqli_data_seek($result, 0);

            while ($row = mysqli_fetch_row($result)) {
              echo
                '<input class="content-head__input" type="text" id ="input' . $row[0] . '" style="display: none" placeholder="Поиск ' . $row[1] . '" name = "input' . $row[0] . '">
                    <button type="button" onclick="hideInputAndShowButton(this.id)" class="content-head__button" id ="button_hide' . $row[0] . '" style="display: none" >
                      <img class="content-head__image" src="/media/image/cross.svg" alt="ИконкаКрестика">
                    </button>';

            }
            echo
              '<p><input type="image" name="submit" class="entrance-btn" src="/media/image/search1.svg"></p>'
              ?>
          </form>
        </div>
        <div class="content-main">
          <div class="content-music" id="searchResults">
            <?php

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

              // $searchtype = $_POST['searchtype'];
              $genre = $_POST['input1'];
              $name = $_POST['input2'];
              $artist = $_POST['input3'];
              
              $conn = new mysqli('music', 'root', '', 'music');
              if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
              }

              $sql = "
              select c.name,
              (select name from genre g where g.genreid = c.genreid),
              (select name from artist a where a.artistid = c.artistid),
              (select duration from сharacteristics_music cm where cm.audiofileid = c.audiofileid),
              (select t.name from tonality t, сharacteristics_music cm where t.tonalityid = cm.tonality and
              cm.audiofileid = c.audiofileid) ,
              (select bpm from сharacteristics_music cm where cm.audiofileid = c.audiofileid) ,
              (select count(liseningdate) from statistic s where s.audiofileid = c.audiofileid),
              (select presencevoice from сharacteristics_music cm where cm.audiofileid = c.audiofileid),
              (select coverpath from audiofiles a where a.audiofileid = c.audiofileid)
              from composition c
              where artistid in (select artistid from artist where LOWER(name) like LOWER('%" . $artist . "%'))
              and genreid in (select genreid from genre where LOWER(name) like LOWER('%" . $genre . "%'))
              and LOWER(name) like LOWER('%" . $name . "%')";
              $result = $conn->query($sql);

              $prefix = "C:\\WebServers\\home\\music\\www";

              if ($result->num_rows > 0) {
                $i = 1;
                while ($row = mysqli_fetch_row($result)) {
                  $url = str_replace($prefix, "", $row[8]);
                  echo '
                  <div class="content-wrapper">
                  <img class="content-wrapper__image" src="' . $url . '" alt="">
                  <div class="content-wrapper__info">
                      <h3 class="content-wrapper__text">' . $row[0] . '</h3>
                      <ul class="content-wrapper__list">
                          <li class="content-wrapper__list-item">
                              Исполнитель: ' . $row[2] . '
                          </li>
                          <li class="content-wrapper__list-item">
                              Жанр: ' . $row[1] . '
                          </li>
                          <li class="content-wrapper__list-item">
                              Количество прослушиваний: ' . $row[7] . '
                          </li>
                          <li class="content-wrapper__list-item" style="display: none" id="info1' . $i . '"> 
                              Продолжительность: ' . $row[3] . '
                          </li>
                          <li class="content-wrapper__list-item" style="display: none" id="info2' . $i . '">
                              Наличие голоса: ' . $row[6] . '
                          </li>
                          <li class="content-wrapper__list-item" style="display: none" id="info3' . $i . '">
                              Тональность: ' . $row[4] . '
                          </li>
                          <li class="content-wrapper__list-item" style="display: none" id="info4' . $i . '">
                              BPM (Ударов в минуту): ' . $row[5] . '
                          </li>
                      </ul>
                      <button onclick="hideButtonAndShowInfo(this.id)" class="content-wrapper__buttons" id="buttoninfo' . $i . '" >Показатель все характеристики</button>
                  </div>
                  </div>
                  ';
                  $i = $i + 1;
                }
              }

              $conn->close();
            }
            ?>
          </div>
        </div>
      </section>
    </div>
  </main>
</body>
<script src="/scripts.js"></script>

</html>