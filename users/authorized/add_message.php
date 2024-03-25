<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Подключение к базе данных
  $conn = new mysqli('music', 'root', '', 'music');

  // Проверка подключения
  if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
  }

  // Получение данных из формы
  $customerid = $_SESSION['id'];
  $messagetext = $_POST['message'];
  $datemessage = date("Y-m-d H:i:s"); // Текущая дата и время
  $parentmessage = empty ($_POST['replyId']) ? null : $_POST['replyId'];


  // Подготовка и выполнение запроса на добавление сообщения
  $stmt = $conn->prepare("INSERT INTO message (customerid, messagetext, datemessage, parentmessage ) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("issi", $customerid, $messagetext, $datemessage, $parentmessage);


  if ($stmt->execute()) {
    // Получаем ID только что добавленного сообщения
    $messageId = $stmt->insert_id;

    // Получаем информацию о сообщении из базы данных
    $sql = "SELECT m.messageid, m.customerid, CONCAT(c.firstname, ' ', c.lastname) AS customer_name, m.messagetext, m.datemessage, m.parentmessage
                FROM message m
                JOIN customers c ON m.customerid = c.customerid
                WHERE m.messageid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $messageId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      // Формируем HTML-разметку для нового сообщения
      $html = '<li class="' . ($_SESSION['id'] == $row['customerid'] ? 'me' : 'you') . '">';
      $html .= '<div class="entete">';
      $html .= '<h2>' . $row['customer_name'] . '</h2>';
      $html .= '<h3>' . $row['datemessage'] . '</h3>';
      $html .= '</div>';
      $html .= '<div class="message">' . $row['messagetext'] . '</div>';
      $html .= '<div class="entete">';
      $html .= '<h2>Ответить</h2>';

      $html .= '<img class="upvote reaction-arrow non-active" src="/media/image/row_up.svg" data-message-id="' . $row['messageid'] . '" alt="ИконкаВверх" style="width: 20px; height: 20px;">';

      $html .= '<span class="total_reaction white"><abbr class ="hint" data-title="0 плюсов; 0 минусов ">0</abbr></span>';
      $html .= '<img class="downvote reaction-arrow non-active" src="/media/image/row_down.svg" data-message-id="' . $row['messageid'] . '" alt="ИконкаВниз" style="width: 20px; height: 20px;">';
      $html .= '</div>';
      $html .= '</li>';
      echo $html;
    } else {
      echo "Ошибка при получении информации о сообщении";
    }
  } else {
    echo "Ошибка при добавлении сообщения: " . $conn->error;
  }

  // Закрытие соединения с базой данных
  $stmt->close();
  $conn->close();
} else {
  echo "Ошибка: неверный метод запроса";
}
?>