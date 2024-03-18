<?php
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
      <div class="entrance">
        <h1 class="entrance-main_lable">Вход в учетную запись</h1>
        <p><label class="entrance-minor_lable">Логин пользователя</label></p>
        <input class="content-head_add_input" type="text" name="login" id="login">
        <p><label class="entrance-minor_lable">Пароль пользователя</label></p>
        <input type="password" class="content-head_add_input" name="password" id="password">
        <p id="warning" style="color: red;"></p>
        <p><button class="content-search__button" type="button" onclick="checkCredentials()">Выполнить вход</button></p>
        <form action="registration.html">
          <p><button class="content-search__button" type="submit" name="submit"
              class="entrance-btn">Зарегистрироваться</button></p>
      </div>
    </div>
  </main>
  <script>
    function checkCredentials() {
      var login = document.getElementById("login").value;
      var password = document.getElementById("password").value;

      if (!login || !password) {
        document.getElementById("warning").innerText = 'Заполните все обязательные поля.';
        return;
      }
      fetch('process.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'login=' + encodeURIComponent(login) + '&password=' + encodeURIComponent(password),
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Перенаправление на другую страницу
            window.location.href = 'general_page.php';
          } else {
            // Отображение предупреждения
            document.getElementById("warning").innerText = 'Пользователь не найден.';
          }
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    }
  </script>
</body>