<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
$id = $_GET['id'];
$sql = "select * from customers where customers.customerid = " . $id;

$result = $conn->query($sql);
$artist = $result->fetch_row();
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
   <style>
      .content-head__textarea {
         border: 1px solid transparent;
         border-radius: 5px;
         background-color: white;
         padding-left: 10px;
      }
   </style>
</head>

<body>
   <header class="header">
          <a href="../general_page.php">Музыкальный сервис</a>

   </header>
   <main class="main">
      <div class="container">
         <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

         <section class="content">
            <h2 class="content-wrapper_text">Форма обратной связи</h2>
            <form action="reply_mail.php?id=<?php echo urlencode($id); ?>" method="post">
               <fieldset>
                  <legend class="content-news_description">Оставьте сообщение:</legend>
                  <br></br>
                  <div>
                     <label for="name" class="content-news_description">Ваше имя:</label>
                     <input class="content-head__input" type="text" name="name"
                        value="Администрация сайта 'Музыкальный сервис'">
                  </div>
                  <br></br>
                  <div>
                     <label for="email" class="content-news_description">E-mail:</label>
                     <input class="content-head__input" type="text" name="email" value="music.service.rus@xmail.ru">
                  </div>
                  <br></br>
                  <div>
                     <label for="message" class="content-news_description">Сообщение:</label>:
                     <textarea class="content-head__textarea" rows="10" cols="45" name="message">
               <?php echo 'Уважаемый ' . strval($artist[1]) . ' ' . strval($artist[2]) . '!&#13;&#10;Текст сообщения.&#13;&#10;С уважением, Администрация сайта "Музыкальный сервис"' ?>
               </textarea>
                  </div>
                  <br></br>
                  <div>
                     <input type="submit" class="content-search__button" value="Отправить сообщение">
                  </div>

               </fieldset>
            </form>
      </div>
      </section>
   </main>
</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $name = $_POST['name'];
   $email = $_POST['email'];
   $message = $_POST['message'];

   $retval = mail($artist[3], "Сообщение от поддержки сайта 'Музыкальный сервис'", $message);
   if ($retval == false) {
      echo "Ошибка отправки сообщения!";
   } else {
      $customerid = $_SESSION['id'];
      $datemail = date("Y-m-d");

      $stmt = $conn->prepare("INSERT INTO mail  VALUES (?, ?, ?)");
      if ($stmt) {
         $stmt->bind_param("iss", $customerid, $datemail, $message);
         $stmt->execute();
         $stmt->close();
      } else {
         die("Error in mail query: " . $conn->error);
      }
   }
}
?>