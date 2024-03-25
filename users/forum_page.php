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
  <link rel="stylesheet" href="/style_hint.css">
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

              $role = $_SESSION['id'] == $row['customerid'] ? 'me' : 'you';
              $html = '<li class="' . $role . '">';
              $html .= '<div class="entete">';
              $html .= '<h2>' . $row['customer_name'] . '</h2>';
              $html .= '<h3>' . $row['datemessage'] . '</h3>';
              $html .= '</div>';
              $html .= '<div class="message">' . $row['messagetext'] . '</div>';
              $html .= '<div class="entete">';
              $html .= '<button class="reply-btn" data-message-id="' . $row["messageid"] . '">Ответить</button>';

              // Выполнение запроса на подсчет суммы реакций
              $reaction_sql = "SELECT 
              customer_name, (positive_reactions + negative_reactions) AS total_reaction FROM (
              SELECT 
                  CONCAT(c.firstname, ' ', c.lastname) AS customer_name,
                  SUM(CASE WHEN r.reaction = 1 THEN 1 ELSE 0 END) AS positive_reactions,
                  SUM(CASE WHEN r.reaction = -1 THEN -1 ELSE 0 END) AS negative_reactions
              FROM reactions r JOIN customers c ON r.customerid = c.customerid
              WHERE r.messageid = " . $row['messageid'] . " GROUP BY c.firstname, c.lastname) AS reactions_summary;";
              $reaction_result = $conn->query($reaction_sql);

              $total_reaction = 0;
              $positive_reaction = [];
              $negative_reaction = [];

              if ($reaction_result->num_rows > 0) {
                while ($row_reaction = $reaction_result->fetch_assoc()) {
                  $total_reaction += $row_reaction['total_reaction'];
                  if ($row_reaction['total_reaction'] == 1) {
                    $positive_reaction[] = $row_reaction['customer_name'];
                  } else if ($row_reaction['total_reaction'] == -1) {
                    $negative_reaction[] = $row_reaction['customer_name'];
                  }
                }
              }

              $positive_reaction_text = count($positive_reaction) > 0 ? count($positive_reaction) . " плюсов: " . implode(', ', $positive_reaction) . "\n" : "0 плюсов \n";
              $negative_reaction_text = count($negative_reaction) > 0 ? count($negative_reaction) . " минусов: " . implode(', ', $negative_reaction) . "\n" : "0 минусов \n";


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


              $html .= '<span class="total_reaction';
              $html .= $total_reaction > 0 ? ' green' : ($total_reaction < 0 ? ' red' : ' white');
              $html .= '"><abbr class ="hint" data-title="' . $positive_reaction_text . $negative_reaction_text . '">' . $total_reaction . '</abbr></span>';


              $html .= '<img class="' . $downvote_status . '" src="' . $downvote_image . '" data-message-id="' . $row['messageid'] . '" alt="ИконкаВниз" style="width: 20px; height: 20px;">';

              $html .= '</div>';
              $html .= '</li>';
              echo $html;

              $parentMessageId = $row['parentmessage'];
              $padding_parent = 15;
              do {
                if ($parentMessageId !== null) {
                  $parentSql = "SELECT m.messageid, m.customerid, CONCAT(c.firstname, ' ', c.lastname) AS customer_name, m.messagetext, m.datemessage, m.parentmessage
                                    FROM message m
                                    JOIN customers c ON m.customerid = c.customerid
                                    WHERE m.messageid = ?";
                  $parentStmt = $conn->prepare($parentSql);
                  $parentStmt->bind_param("i", $parentMessageId);
                  $parentStmt->execute();
                  $parentResult = $parentStmt->get_result();

                  if ($parentResult->num_rows > 0) {
                    $parentRow = $parentResult->fetch_assoc();
                    $style = $role == "me" ? "right" : "left";
                    // Вывод родительского сообщения
                    echo '<li class="parent ' . $role . '" style="margin-' . $style . ': ' . $padding_parent . 'px;">';
                    echo '<div class="entete">';
                    echo '<h2>' . $parentRow['customer_name'] . '</h2>';
                    echo '<h3>' . $parentRow['datemessage'] . '</h3>';
                    echo '</div>';
                    echo '<div class="message">' . $parentRow['messagetext'] . '</div>';
                    echo '</li>';

                    // Переходим к следующему родительскому сообщению
                    $parentMessageId = $parentRow['parentmessage'];
                    $padding_parent += 15;

                  } else {
                    // Если родительское сообщение не найдено, выходим из цикла
                    break;
                  }
                } else {
                  // Если нет родительского сообщения, выходим из цикла
                  break;
                }
              } while (true);
            }
          }

          ?>
        </ul>

        <footer>
          <div class="reply-box">
            <div class="original-message" style="display: none; color: white;">
              <p>Вы отвечаете на сообщение:</p>
              <p id="reply" data-reply-id=""></p>
            </div>
            <textarea placeholder="Введите свое сообщение"></textarea>
            <a href="#" id="send">Отправить</a>
          </div>

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

      var replyId = $("#reply").data("reply-id") == "" ? null : $("#reply").data("reply-id");
      console.error($("#reply").data());
      console.error(replyId);

      // Отправляем данные на сервер
      $.ajax({
        url: '/users/authorized/add_message.php', // Путь к файлу обработчику
        type: 'POST',
        data: {
          message: messageText,
          replyId: replyId // Передаем значение data-reply-id на сервер
        },
        success: function (response) {
          $("#chat").append(response);

          // Прокручиваем контейнер с сообщениями вниз
          var chatContainer = document.getElementById("chat");
          chatContainer.scrollTop = chatContainer.scrollHeight;

          $("textarea").val(""); // Очищаем текстовое поле после отправки

          location.reload();
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
    $("#chat").on("click", ".reaction-arrow", function () {
      var $arrow = $(this);
      var messageId = $arrow.data("message-id");
      var reactionType = $arrow.hasClass("upvote") ? 1 : -1;

      var isMyMessage = $(this).closest('li').hasClass('me');
      if (isMyMessage) {
        alert("Вы не можете ставить реакции на свои собственные сообщения!");
        return; // Прерываем выполнение функции
      }
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
        var newReactionCount = parseInt($reactionCount.text()) + (status ? -reactionType : reactionType); // Обновляем количество реакций
        $reactionCount.text(newReactionCount); // Устанавливаем новое значение количества реакций

        // Обновляем классы подсказки, если необходимо
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

<script>
  $(document).ready(function () {
    // При нажатии на кнопку "Ответить"
    $("#chat").on("click", ".reply-btn", function () {
      var messageId = $(this).data("message-id");
      var originalMessage = $(this).closest("li").find(".message").text();

      // Показываем блок original-message
      $(".original-message").show();

      // Устанавливаем текст сообщения ответа
      $("#reply").text(originalMessage);

      // Устанавливаем идентификатор сообщения в data-reply-id
      $("#reply").data("reply-id", messageId);
      // console.error($("#reply").data());

    });
  });
</script>

</html>