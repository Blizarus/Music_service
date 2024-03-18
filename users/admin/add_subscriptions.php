<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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

    </header>
    <main class="main">
        <div class="container">
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

            <section class="content">
                <div class="content-main">
                <form action="add_subscriptions.php" method="post" enctype="multipart/form-data">
                    <div class="content_selects">
                        <h3 class="content-wrapper__text">Тема рассылки</h3>
                        <input class="content-head_add_input" type="text" name="name_mailing" id="mailing" placeholder="Введите название">

                        <p><input class="content-search__button" type="submit" value="Добавить рассылку"></strong></p>
                    </div>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
<!-- <?php echo php_ini_loaded_file(); ?> -->

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mailing = $_POST['name_mailing'];


    $stmt = $conn->prepare("INSERT INTO mailing VALUES (NULL, ?)");
    if ($stmt) {
        $stmt->bind_param("s", $mailing);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Error in mailing  query: " . $conn->error);
    }
}

?>