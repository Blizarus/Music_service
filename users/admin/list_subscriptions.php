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
                <div class="content-head_add">
                    <a class="settings__link" href="add_subscriptions.php">Добавление новой рассылки</a>
                </div>
                <div class="content-main">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    
                                <?php
                                echo '
                                    <th>ID</th>
                                    <th>Тема рассылки</th>
                                    <th>Пользователи</th>
                                    <th>Статистика<br/>За месяц/За все время</th>';
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="table-content">
                                <?php
                                
                                $sql = "select * from mailing";

                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_row($result)) {
                                        $url_send = 'send_mailing.php?id=' . $row[0];
                                        $url_delete = 'delete_subscriptions_process.php?id=' . $row[0];
                                        echo '
                                            <tr>
                                            <td>' . $row[0] . '</td>
                                            <td>' . $row[1] . '</td>
                                            <td>';
                                        $usersResult = $conn->query("select * from subscriptions where mailingid=". $row[0]);
                                        $users = array();
                                        while ($user = mysqli_fetch_row($usersResult)) {
                                            $users[] = '<p>' . $user[0] . '</p>';
                                        }
                                        echo implode(' ', $users);

                                        $monthSql = "SELECT COUNT(*) FROM mailing_statistics WHERE DATE_FORMAT(sent_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') and mailingid=". $row[0];
                                        $totalSql = "SELECT COUNT(*) FROM mailing_statistics WHERE mailingid=". $row[0];

                                        $monthResult = $conn->query($monthSql);
                                        $totalResult = $conn->query($totalSql);

                                        $monthCount = ($monthResult->num_rows > 0) ? $monthResult->fetch_row()[0] : 0;
                                        $totalCount = ($totalResult->num_rows > 0) ? $totalResult->fetch_row()[0] : 0;
                                        
                                        echo '</td>
                                        <td>'.$monthCount.' / '.$totalCount.'</td>';
                                        echo '<td><button onclick="redirectToPage(\'' . $url_send . '\')" class="content-wrapper__buttons" >Отправить рассылку</button></td>    
                                        <td><button onclick="redirectToPage(\'' . $url_delete . '\')" class="content-wrapper__buttons" >Удалить</button></td>    
                                            </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
<script src="/scripts.js"></script>
<script src="sort_table.js"></script>

</html>