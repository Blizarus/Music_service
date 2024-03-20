<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
  die ("Connection failed: " . $conn->connect_error);
}
$id = $_GET['id'];
$sql = "
select c.name,
(select name from genre g where g.genreid = c.genreid),
(select name from artist a where a.artistid = c.artistid),
(select t.name from tonality t, сharacteristics_music cm where t.tonalityid = cm.tonality and
cm.audiofileid = c.compositionid) ,
(select bpm from сharacteristics_music cm where cm.audiofileid = c.compositionid) ,
(select presencevoice from сharacteristics_music cm where cm.audiofileid = c.compositionid),
c.compositionid,
(select filepath from audiofiles a where a.audiofileid = c.compositionid),
(select coverpath from audiofiles a where a.audiofileid = c.compositionid),
(select duration from сharacteristics_music cm where cm.audiofileid = c.compositionid)
from composition c where compositionid = " . $id . "";
$result = $conn->query($sql);
$composition = $result->fetch_row();
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
        <div class="content-head_add">
          <form action="update_comp.php?id=<?php echo urlencode($id); ?>" method="post" enctype="multipart/form-data">
            <button type="button" class="content-search__button" id="selectMusicButton">Изменить музыку</button>
            <input type="file" name="music_file" id="music_file" accept=".mp3" style="display: none">
            <p><a class="settings__link" id="file"></a></p>
            <button type="button" class="content-search__button" id="selectCoverButton">Изменить обложку</button>
            <input type="file" name="cover_file" id="cover_file" accept=".png" style="display: none">
            <p><a class="settings__link" id="cover"></a></p>

            <div class="content-cover">
              <img class="content-wrapper__image" id="coverImage" style="display: none">
            </div>
        </div>
        <div class="content-main">
          <div>
            <p>
              <?php echo '<a class="settings__link">Оригинальный файл: ' . $composition[2] . '-' . str_replace(' ', '_', $composition[0]) . '</a>'; ?>
            </p>
            <div class="music-progress">
              <audio id="originalPlayer" controls
                src="<?php echo ((str_replace("/\\/", "/\/", str_replace("C:\\Games\\xampp\\htdocs\\music\\www", "", $composition[7])))) . '?random=' . rand() ?>">
              </audio>
            </div>
            <p>
              <?php echo '<a class="settings__link">Оригинальная обложка:</a>' ?>
            </p>
            <div class="content-cover">
              <img class="content-wrapper__image" id="originalImage"
                src="<?php echo ((str_replace("/\\/", "/\/", str_replace("C:\\Games\\xampp\\htdocs\\music\\www", "", $composition[8])))) ?>">
            </div>
          </div>
          <div class="content_selects">

            <h3 class="content-wrapper__text">Название</h3>
            <input class="content-head_add_input" type="text" name="name_composition" id="composition"
              placeholder="Введите название" value="<?php echo $composition[0]; ?>">

            <h3 class="content-wrapper__text">Исполнитель</h3>
            <p><select name="artist" id="artist" class="content-head_add_input">
                <option selected disabled hidden>Выберете исполнителя</option>
                <?php
                $sql = "select * from artist order by name";
                $Query = $conn->query($sql);
                while ($row = mysqli_fetch_row($Query)) {
                  echo '<option value="' . $row[0] . '|' . $row[1] . '" id="' . $row[1] . '">' . $row[1] . '</option>';
                }
                ?>
              </select>

            <h3 class="content-wrapper__text">Жанр</h3>
            <p><select name="genre" id="genre" class="content-head_add_input">
                <option selected disabled hidden>Выберете жанр</option>
                <?php
                $sql = "select * from genre order by name";
                $Query = $conn->query($sql);
                while ($row = mysqli_fetch_row($Query)) {
                  echo '<option value="' . $row[0] . '" id="' . $row[1] . '">' . $row[1] . '</option>';
                }
                ?>
              </select>

            <h3 class="content-wrapper__text">Наличие голоса</h3>
            <p><select name="presencevoice" id="presencevoice" class="content-head_add_input">
                <option value="0" id="0">Инструментальная музыка</option>
                <option value="1 " id="1" selected>Вокальная музыка</option>
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
            <input class="content-head_add_input" type="text" name="BPM" id="BPM" placeholder="Введите BPM"
              value="<?php echo $composition[4]; ?>">

            <br></br>
            <p><input class="content-search__button" type="submit" value="Изменить композицию"></strong></p>
          </div>
        </div>
        </form>
      </section>
    </div>
  </main>
</body>
<!-- <?php echo php_ini_loaded_file(); ?> -->
<?php require_once ('update_comp_process.php'); ?>

</html>

<script>
  $(document).ready(function () {
    var selectedTonalityId = <?php echo json_encode($composition[3]); ?>;
    var selectedGenreId = <?php echo json_encode($composition[1]); ?>;
    var selectedVoiceId = <?php echo json_encode($composition[5]); ?>;
    var selectedArtistId = <?php echo json_encode($composition[2]); ?>;

    // Используем jQuery для выбора опции в селекте
    $('#tonality option').each(function () {
      if (this.id == selectedTonalityId) {
        $(this).prop('selected', true);
        return false;
      }
    });

    $('#genre option').each(function () {
      if (this.id == selectedGenreId) {
        $(this).prop('selected', true);
        return false;
      }
    });

    $('#presencevoice option').each(function () {
      if (this.id == selectedVoiceId) {
        $(this).prop('selected', true);
        return false;
      }
    });

    $('#artist option').each(function () {
      if (this.id == selectedArtistId) {
        $(this).prop('selected', true);
        return false;
      }
    });
  });

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

  $(document).ready(function () {
    // Обработчик события клика на кнопке
    $('#selectMusicButton').click(function () {
      // Триггер события клика на скрытом инпуте
      $('#music_file').click();
    });

    $('#music_file').change(function () {
      // Получаем имя файла из пути
      var fileName = $(this).val().split('\\').pop();

      // Обновляем текст кнопки
      $('#file').text('Выбран файл: ' + fileName);

      $('#selectMusicButton').text('Файл выбран');

      // Ваш код обработки изменения значения инпута, если необходимо
      console.log('Выбран файл:', fileName);
    });
  });
</script>
<script src="/scripts_add.js"></script>