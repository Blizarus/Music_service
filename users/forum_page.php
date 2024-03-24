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
  <link rel="stylesheet" href="/style_forum.css">
  <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
</head>

<body>
  <header class="header">
    <a href="../general_page.php">Музыкальный сервис</a>

  </header>
  <main class="main">
    <div class="container">
      <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

      <section class="content">

        <div class="panel panel-primary">
          <span class="entrance-main_lable">Веб-форум</span>
        </div>

        <ul id="chat">

          <?php
          $conn = new mysqli('music', 'root', '', 'music');
          if ($conn->connect_error) {
            die ("Connection failed: " . $conn->connect_error);
          }

          $sql = "SELECT m.messageid, m.customerid, CONCAT(c.firstname, ' ', c.lastname) AS customer_name , m.messagetext, m.datemessage, m.parentmessage
          FROM message m
          JOIN customers c ON m.customerid = c.customerid";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              $html = '<li class="' . ($_SESSION['id'] == $row['customerid'] ? 'me' : 'you') . '">';
              $html .= '<div class="entete">';
              $html .= '<h2>' . $row['customer_name'] . '</h2>';
              $html .= '<h3>' . $row['datemessage'] . '</h3>';
              $html .= '</div>';
              $html .= '<div class="message">' . $row['messagetext'] . '</div>';
              $html .= '<div class="entete">';
              $html .= '<h2>Ответить</h2>';

              // Выполнение запроса на подсчет суммы реакций
              $reaction_sql = "SELECT SUM(reaction) AS total_reaction FROM reactions WHERE messageid=?";
              $stmt = $conn->prepare($reaction_sql);
              $stmt->bind_param("i", $row['messageid']);
              $stmt->execute();
              $reaction_result = $stmt->get_result();
              $reaction_row = $reaction_result->fetch_assoc();
              $total_reaction = isset ($reaction_row['total_reaction']) ? $reaction_row['total_reaction'] : 0;

              $user_reaction_sql = "SELECT reaction FROM reactions WHERE messageid=? AND customerid=?";
              $user_reaction_stmt = $conn->prepare($user_reaction_sql);
              $user_reaction_stmt->bind_param("ii", $row['messageid'], $_SESSION['id']);
              $user_reaction_stmt->execute();
              $user_reaction_result = $user_reaction_stmt->get_result();
              $user_reaction_row = $user_reaction_result->fetch_assoc();
              $user_reaction = isset ($user_reaction_row['reaction']) ? true : false;

              $upvote_image = $user_reaction ? ($user_reaction_row['reaction'] == 1 ? "/media/image/row_up_active.svg" : "/media/image/row_up.svg") : "/media/image/row_up.svg";
              $downvote_image = $user_reaction ? ($user_reaction_row['reaction'] == -1 ? "/media/image/row_down_active.svg" : "/media/image/row_down.svg") : "/media/image/row_down.svg";

              $upvote_status = $user_reaction && $user_reaction_row['reaction'] == 1 ? "upvote reaction-arrow active" : "upvote reaction-arrow non-active";
              $downvote_status = $user_reaction && $user_reaction_row['reaction'] == -1 ? "downvote reaction-arrow active" : "downvote reaction-arrow non-active";

              $html .= '<img class="' . $upvote_status . '" src="' . $upvote_image . '" data-message-id="' . $row['messageid'] . '" alt="ИконкаВверх" style="width: 20px; height: 20px;">';

              $html .= '<h2 class="total_reaction';
              $html .= $total_reaction > 0 ? ' green' : ($total_reaction < 0 ? ' red' : ' white');
              $html .= '">' . $total_reaction . '</h2>';


              $html .= '<img class="' . $downvote_status . '" src="' . $downvote_image . '" data-message-id="' . $row['messageid'] . '" alt="ИконкаВниз" style="width: 20px; height: 20px;">';



              $html .= '</div>';
              $html .= '</li>';
              echo $html;
            }
          }

          ?>
        </ul>

        <footer>
          <textarea placeholder="Type your message"></textarea>
          <a href="#" id="send">Send</a>
        </footer>
    </div>

    </section>
    </div>
  </main>
</body>

<script>
  $(document).ready(function () {

    // Прокручиваем контейнер с сообщениями вниз
    var chatContainer = document.getElementById("chat");
    chatContainer.scrollTop = chatContainer.scrollHeight;

    $("a#send").click(function (e) {
      e.preventDefault(); // Предотвращаем переход по ссылке по умолчанию

      // Получаем текст сообщения из textarea
      var messageText = $("textarea").val();

      // Отправляем данные на сервер
      $.ajax({
        url: '/users/authorized/add_message.php', // Путь к файлу обработчику
        type: 'POST',
        data: {
          message: messageText
        },
        success: function (response) {
          $("#chat").append(response);

          // Прокручиваем контейнер с сообщениями вниз
          var chatContainer = document.getElementById("chat");
          chatContainer.scrollTop = chatContainer.scrollHeight;

          $("textarea").val(""); // Очищаем текстовое поле после отправки
        }
        ,
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    });
  });
</script>

<script>
  $(document).ready(function () {
    $(".reaction-arrow").click(function () {
      var $arrow = $(this);
      var messageId = $arrow.data("message-id");
      var reactionType = $arrow.hasClass("upvote") ? 1 : -1;

      if (checkState($arrow)) {
        if ($arrow.hasClass("non-active")) {
          toggleReaction(messageId, -reactionType, $arrow, true);
          toggleReaction(messageId, reactionType, $arrow, false);
          console.error("поставили стрелку, убрали соседнюю");

          // Устанавливаем текущую стрелку в активное состояние
          $arrow.removeClass("non-active").addClass("active");
          $arrow.attr("src", $arrow.hasClass("upvote") ? "/media/image/row_up_active.svg" : "/media/image/row_down_active.svg");

          // Устанавливаем соседние стрелки в неактивное состояние
          $arrow.siblings(".reaction-arrow").removeClass("active").addClass("non-active");
          $arrow.siblings(".reaction-arrow").attr("src", function () {
            return $(this).hasClass("upvote") ? "/media/image/row_up.svg" : "/media/image/row_down.svg";
          });
        } else {
          toggleReaction(messageId, reactionType, $arrow, true);
          console.error("убрали стрелку");

          // Устанавливаем текущую стрелку в неактивное состояние
          $arrow.removeClass("active").addClass("non-active");
          $arrow.attr("src", $arrow.hasClass("upvote") ? "/media/image/row_up.svg" : "/media/image/row_down.svg");
        }
      } else {
        toggleReaction(messageId, reactionType, $arrow, false);
        console.error("без реакции, поставили стрелку");

        // Устанавливаем текущую стрелку в активное состояние
        $arrow.removeClass("non-active").addClass("active");
        $arrow.attr("src", $arrow.hasClass("upvote") ? "/media/image/row_up_active.svg" : "/media/image/row_down_active.svg");

        // Устанавливаем соседние стрелки в неактивное состояние
        $arrow.siblings(".reaction-arrow").removeClass("active").addClass("non-active");
        $arrow.siblings(".reaction-arrow").attr("src", function () {
          return $(this).hasClass("upvote") ? "/media/image/row_up.svg" : "/media/image/row_down.svg";
        });
      }

    });
  });

  function toggleReaction(messageId, reactionType, $arrow, status) {
    var actionUrl = '';
    if (status) {
      actionUrl = '/users/authorized/delete_reaction.php';
    } else {
      actionUrl = '/users/authorized/add_reaction.php';
    }

    $.ajax({
      url: actionUrl,
      type: 'POST',
      data: {
        messageId: messageId,
        reactionType: reactionType
      },
      success: function (response) {
        // Обновляем количество реакций на странице
        var $reactionCount = $arrow.siblings(".total_reaction"); // Находим элемент, отображающий количество реакций
        console.error(parseInt($reactionCount.text()));
        console.error(reactionType);
        var newReactionCount = parseInt($reactionCount.text()) + (status ? -reactionType : reactionType); // Обновляем количество реакций
        $reactionCount.text(newReactionCount); // Устанавливаем новое значение количества реакций
        console.error(parseInt($reactionCount.text()));

        if (newReactionCount > 0) {
          $reactionCount.removeClass("red white").addClass("green");
        } else if (newReactionCount < 0) {
          $reactionCount.removeClass("green white").addClass("red");
        } else {
          $reactionCount.removeClass("green red").addClass("white");
        }
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      }
    });
  }

  function checkState($arrow) {
    var isActive = $arrow.hasClass("active");

    $arrow.siblings(".reaction-arrow").each(function () {
      if ($(this).hasClass("active")) {
        isActive = true;
        return false; // Прерываем цикл, если найдена активная стрелка
      }
    });

    return isActive;
  }
</script>




</html>