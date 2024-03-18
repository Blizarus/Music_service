<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_SESSION['id'];
$sql = "select * from customers INNER JOIN login_password on customers.customerid = login_password.customerid where customers.customerid = " . $id;

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

                </div>
                <div class="content-main">
                    <div class="content-cover">
                        <h3 class="content-wrapper__text">Пользователь:
                            <?php echo $artist[6]; ?>
                        </h3>
                    </div>
                    <div class="content_selects">
                        <form action="update_profile.php?id=<?php echo urlencode($id); ?>" method="post"
                            enctype="multipart/form-data">
                            <h3 class="content-wrapper__text">Имя пользователя</h3>
                            <input type="hidden" name="method" value="method1">
                            <input class="content-head_add_input" type="text" name="name" id="name"
                                placeholder="Введите имя" value="<?php echo $artist[1]; ?>">

                            <h3 class="content-wrapper__text">Фамилия пользователя</h3>
                            <input class="content-head_add_input" type="text" name="lname" id="lname"
                                placeholder="Введите фамилию" value="<?php echo $artist[2]; ?>">

                            <h3 class="content-wrapper__text">Логин</h3>
                            <input class="content-head_add_input" type="text" name="login" id="login"
                                placeholder="Введите логин" value="<?php echo $artist[6]; ?>">

                            <p><input class="content-search__button" type="submit" value="Сохранить изменений"></strong>
                        </form>
                        <form action="update_profile.php?id=<?php echo urlencode($id); ?>" method="post"
                            enctype="multipart/form-data">
                            <input type="hidden" name="method" value="method2">
                            <h3 class="content-wrapper__text">Пароль пользователя</h3>
                            <a>Для изменения пароля введите старый пароль:</a>
                            <p><input class="content-head_add_input" type="text" name="old_pass" id="old_pass"
                                    placeholder="Старый пароль"></p>

                            <a>Новый пароль:</a>
                            <p><input class="content-head_add_input" type="text" name="new_pass" id="new_pass"
                                    placeholder="Новый пароль"></p>

                            <p><input class="content-search__button" type="submit" value="Сохранить изменений"></strong>
                        </form>
                        <?php 
                            $url_delete = 'delete_profile_process.php?id=' . $id;
                            echo '<p><button onclick="redirectToPage(\'' . $url_delete . '\')" class="content-search__button" >Удалить</button></p>';
                            ?>
                    </div>
                </div>

            </section>
        </div>
    </main>
</body>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $method = $_POST['method'];

    if ($method === 'method1') {
        $login = $_POST['login'];
        $name = $_POST['name'];
        $lname = $_POST['lname'];

        $stmt = $conn->prepare("UPDATE customers SET firstname = ?, lastname = ? WHERE customerid = ?");
        if ($stmt) {
            $stmt->bind_param("ssi", $name, $lname, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error in composition query: " . $conn->error);
        }

        $stmt = $conn->prepare("UPDATE login_password SET login  = ? WHERE customerid = ?");
        if ($stmt) {
            $stmt->bind_param("si", $login, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error in composition query: " . $conn->error);
        }
    } else {
        $old_pass = $_POST['old_pass'];
        $new_pass = sha1($_POST['new_pass']);

        $sql = "select * from login_password where customerid = '$id' and password = sha1('$old_pass')";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {

            $stmt = $conn->prepare("UPDATE login_password SET password = ? WHERE customerid = ?");
            if ($stmt) {
                $stmt->bind_param("si", $new_pass, $id);
                $stmt->execute();
                $stmt->close();
            } else {
                die("Error in composition query: " . $conn->error);
            }
        } else {
            echo 'Неправильно введен старый пароль!';
        }
    }
}
?>

<script>
    var selectedАdministratorId = <?php echo json_encode($artist[4]); ?>;

    $('#administrator option').each(function () {
        if (this.id == selectedАdministratorId) {
            $(this).prop('selected', true);
            return false;
        }
    });
</script>
<script src="/scripts.js"></script>
</html>