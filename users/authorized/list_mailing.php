<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/table.css">
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
                    <div class="table-container">
                        <button type="button" class="content-search__button">Список рассылок</button>

                        <div id="mailing">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Номер рассылки</th>
                                        <th>Тема рассылки</th>
                                    </tr>
                                </thead>
                                <tbody id="table-content">
                                    <?php
                                    $sql = "select * from mailing";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = mysqli_fetch_row($result)) {
                                            echo '
                                            <tr>
                                            <td>' . $row[0] . '</td>
                                            <td>' . $row[1] . '</td>';
                                            $isSubscribedSql = "SELECT * FROM subscriptions WHERE customerid = $id AND mailingid  = ".$row[0];
                                            $isSubscribedResult = $conn->query($isSubscribedSql);
                                            if ($isSubscribedResult->num_rows > 0) {         
                                                $url_unsubscribe = 'unsubscribe_process.php?id=' . $row[0];
                                                echo "<td><button class='content-search__button' onclick='window.location.href=\"" . $url_unsubscribe . "\";'>Отписаться</button></td>";
                                            } else {
                                                $url_subscribe = 'subscribe_process.php?id=' . $row[0];
                                                echo "<td><button class='content-search__button' onclick='window.location.href=\"" . $url_subscribe . "\";'>Подписаться</button></td>";
                                            }
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>