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

      <section class="content-news">
        <article class="news-block">
          <h2 class="content-wrapper_text">Новые композиции</h2>
          <p class="content-news_description">Подборка новых композиций в сервисе</p>
          <a href="search_music.php?criteria=news1" class="news-block-news_link"><img src="/media/image/link.svg"
              alt="ИконкаПерехода"></a>
        </article>
        <article class="news-block">
          <h2 class="content-wrapper_text">Топ прослушиваемых композиций</h2>
          <p class="content-news_description">Наиболее прослушиваемые композиции пользователями сервиса</p>
          <a href="search_music.php?criteria=news2" class="news-block-news_link"><img src="/media/image/link.svg"
              alt="ИконкаПерехода"></a>
        </article>
        <?php
        if (isset ($_SESSION['login'])) {
          echo '
          <article class="news-block">
          <h2 class="content-wrapper_text">Для Вас </h2>
          <p class="content-news_description">Случайные музыкальные композиции, подобранные согласно вашим предпочтениям</p>
          <a href="search_music.php?criteria=news3" class="news-block-news_link"><img src="/media/image/link.svg" alt="ИконкаПерехода"></a>
        </article>
        <article class="news-block">
          <h2 class="content-wrapper_text">Новое для Вас</h2>
          <p class="content-news_description">Новые музыкальные композиции, подобранные согласно вашим предпочтениям</p>
          <a href="search_music.php?criteria=news4" class="news-block-news_link"><img src="/media/image/link.svg" alt="ИконкаПерехода"></a>
        </article>
          ';
        }
        ?>
      </section>
    </div>
  </main>
</body>

</html>