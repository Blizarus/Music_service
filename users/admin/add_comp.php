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

    <div class="music-progress">
      <audio id="audioPlayer" controls style="display: none">
      </audio>
    </div>
  </header>
  <main class="main">
    <div class="container">
      <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

      <section class="content">
        <form action="add_comp.php" method="post" enctype="multipart/form-data">
          <div class="content-head_add">
            <button type="button" class="content-search__button" id="selectMusicButton">Выбрать музыку</button>
            <input type="file" name="music_file" id="music_file" accept=".mp3" style="display: none">
            <p><a class="settings__link" id="file"></a></p>
            <button type="button" class="content-search__button" id="selectCoverButton">Выбрать обложку</button>
            <input type="file" name="cover_file" id="cover_file" accept=".png" style="display: none">
            <p><a class="settings__link" id="cover"></a></p>
            <div class="content-cover">
              <img class="content-wrapper__image" id="coverImage" style="display: none">
            </div>
          </div>
          <div class="content-main">


            <div class="content_selects">
              <h3 class="content-wrapper__text">Название</h3>
              <input class="content-head_add_input" type="text" name="name_composition" id="composition"
                placeholder="Введите название">

              <h3 class="content-wrapper__text">Исполнитель</h3>
              <p><select name="artist" id="artist" class="content-head_add_input">
                  <option selected disabled hidden>Выберете исполнителя</option>
                  <?php
                  $sql = "select * from artist order by name";
                  $Query = $conn->query($sql);
                  while ($row = mysqli_fetch_row($Query)) {
                    echo '<option value="' . $row[0] . '|' . $row[1] . '">' . $row[1] . '</option>';
                  }
                  ?>
                </select>

              <h3 class="content-wrapper__text">Жанр</h3>
              <p><select name="genre" class="content-head_add_input">
                  <option selected disabled hidden>Выберете жанр</option>
                  <?php
                  $sql = "select * from genre order by name";
                  $Query = $conn->query($sql);
                  while ($row = mysqli_fetch_row($Query)) {
                    echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                  }
                  ?>
                </select>

              <h3 class="content-wrapper__text">Наличие голоса</h3>
              <p><select name="presencevoice" class="content-head_add_input">
                  <option value="0">Инструментальная музыка</option>
                  <option value="1" selected>Вокальная музыка</option>
                </select>

              <h3 class="content-wrapper__text">Тональность</h3>
              <p><select name="tonality" id="tonality" class="content-head_add_input">
                  <option selected disabled hidden>Выберете жанр</option>
                  <?php
                  $sql = "select * from tonality";
                  $Query = $conn->query($sql);
                  while ($row = mysqli_fetch_row($Query)) {
                    echo '<option value="' . $row[0] . '" id="' . $row[1] . '">' . $row[1] . '</option>';
                  }
                  ?>
                </select>

              <h3 class="content-wrapper__text">BPM</h3>
              <input class="content-head_add_input" type="text" name="BPM" id="BPM" placeholder="Введите BPM">

              <br></br>
              <p><input class="content-search__button" type="submit" value="Добавить композицию"></strong></p>
            </div>
          </div>
        </form>
      </section>
    </div>
  </main>
</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $sql = "select max(genreid) from genre";
  $result = $conn->query($sql);
  $id = $result->fetch_row();
  $id = $id[0] + 1;

  $composition = $_POST['name_composition'];

  $artist = $_POST['artist'];
  $artist = explode('|', $artist);
  $artist = str_replace(' ', '_', $artist);


  $genre = $_POST['genre'];
  $presencevoice = $_POST['presencevoice'];
  $tonality = $_POST['tonality'];
  $BPM = $_POST['BPM'];

  $name = $artist[1] . "-" . str_replace(' ', '_', $composition);
  $dateupload = date("Y-m-d");
  $coverpath = "C:\\Games\\xampp\\htdocs\\musicmedia\\composition\\" . $artist[1] . "\\" . $composition . ".png";

  $musicFile = $_FILES['music_file'];
  $filepath = "C:\\Games\\xampp\\htdocs\\musicmedia\\composition\\" . $artist[1] . "\\";
  $musicFileName = $name . ".mp3";
  $targetFile = $filepath . $musicFileName;
  move_uploaded_file($musicFile['tmp_name'], $targetFile);

  require_once ($_SERVER['DOCUMENT_ROOT'] . '/libraries/getID3-1.9.23/getid3/getid3.php');
  $getID3 = new getID3;
  $fileinfo = $getID3->analyze($targetFile);
  $duration_seconds = round($fileinfo['playtime_seconds']);
  if (isset ($fileinfo['comments']['picture'][0]['data'])) {
    $coverData = $fileinfo['comments']['picture'][0]['data'];

  }
  if (!empty ($_FILES['cover_file']['tmp_name'])) {
    $coverFile = $_FILES['cover_file'];
    move_uploaded_file($coverFile['tmp_name'], $coverpath);
  } else {
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/libraries/getID3-1.9.23/getid3/getid3.php');


    $image = imagecreatefromstring($coverData);
    if ($image !== false) {

      file_put_contents($coverpath, $coverData);
    }
  }


  $// Вставка записи в таблицу audiofiles
    $stmt = $conn->prepare("INSERT INTO audiofiles (filesize, filepath, dateupload, coverpath) VALUES (?, ?, ?, ?)");
  if ($stmt) {
    $stmt->bind_param("dsss", $filesize, $targetFile, $dateupload, $coverpath);
    $filesize = round(filesize($targetFile) / 1024);
    $stmt->execute();
    $inserted_audiofiles_id = $stmt->insert_id; // Получаем идентификатор вставленной записи
    $stmt->close();
  } else {
    die ("Error in audiofiles query: " . $conn->error);
  }

  // Вставка записи в таблицу composition
  $stmt = $conn->prepare("INSERT INTO composition (artistid, genreid, compositionname, audiofilesid) VALUES (?, ?, ?, ?)");
  if ($stmt) {
    $stmt->bind_param("iisi", $artist[0], $genre, $composition, $inserted_audiofiles_id);
    $stmt->execute();
    $stmt->close();
  } else {
    die ("Error in composition query: " . $conn->error);
  }

  // Вставка записи в таблицу сharacteristics_music
  $stmt = $conn->prepare("INSERT INTO сharacteristics_music (tonalityid, BPM, duration, presencevoice) VALUES (?, ?, ?, ?)");
  if ($stmt) {
    $stmt->bind_param("iiid", $tonality, $BPM, $duration_seconds, $presencevoice);
    $stmt->execute();
    $stmt->close();
  } else {
    die ("Error in сharacteristics_music query: " . $conn->error);
  }

}
?>
<!-- <script>
  $(document).ready(function () {
    $('#artist').change(function () {
      var selectedOption = $(this).val();
      var inputValue = $('#composition').val(); // Получаем значение из поля input
      $.ajax({
        type: 'GET',
        url: 'get_data.php',
        data: { selectedOption: selectedOption, inputValue: inputValue },
        dataType: 'json',
        success: function (data) {
          $('#BPM').val(data.valueForInput);
          // Выбираем похожую опцию в selectResult
          $('#tonality option').each(function () {
            if (this.id.startsWith(data.selectedValue)) {
              $(this).prop('selected', true);
              return false; // Прерываем цикл после выбора первой подходящей опции
            }
          });
        },
        error: function () {
          alert('Произошла ошибка при получении данных.');
        }
      });
    });
  });
</script> -->
<script src="/scripts_add.js"></script>