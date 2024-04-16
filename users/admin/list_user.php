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
            <button class="content-search__button" onclick="redirectToPage('chart_map')" class="content-wrapper__buttons" >Карта</button>
            <button class="content-search__button" onclick="redirectToPage('chart_sankey')" class="content-wrapper__buttons" >Диаграмма Санки</button>
                <div class="content-main">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Логин</th>
                                    <th>Имя</th>
                                    <th>Фамилия</th>
                                    <th>Почта</th>
                                    <th>Права администратора</th>
                                </tr>
                            </thead>
                            <tbody id="table-content">
                                <?php
                                $sql = "select * from customers INNER JOIN login_password on customers.customerid = login_password.customerid";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_row($result)) {
                                        
                                        $row[4]  = $row[4]   === '1' ? 'Администратор' : 'Пользователь';
                                        $url_update = 'update_user.php?id=' . $row[0];
                                        $url_delete = 'delete_user_process.php?id=' . $row[0];
                                        echo '
                                            <tr>
                                            <td>' . $row[0] . '</td>
                                            <td>' . $row[6] . '</td>
                                            <td>' . $row[1] . '</td>
                                            <td>' . $row[2] . '</td>
                                            <td>' . $row[3] . '</td>
                                            <td>' . $row[4] . '</td>
                                            <td><button onclick="redirectToPage(\'' . $url_update . '\')" class="content-wrapper__buttons" >Редактировать</button></td>    
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

</html>