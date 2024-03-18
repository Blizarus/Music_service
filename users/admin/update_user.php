<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_GET['id'];
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
                    <form action="update_user.php?id=<?php echo urlencode($id); ?>" method="post"
                        enctype="multipart/form-data">
                </div>
                <div class="content-main">
                    <div class="content-cover">
                    </div>
                    <div class="content_selects">

                        <h3 class="content-wrapper__text">Логин</h3>
                        <input class="content-head_add_input" type="text" name="login" id="login"
                            placeholder="Введите название" value="<?php echo $artist[6]; ?>">

                        <h3 class="content-wrapper__text">Имя</h3>
                        <input class="content-head_add_input" type="text" name="name" id="name"
                            placeholder="Введите название" value="<?php echo $artist[1]; ?>">

                        <h3 class="content-wrapper__text">Фамилия</h3>
                        <input class="content-head_add_input" type="text" name="lname" id="lname"
                            placeholder="Введите название" value="<?php echo $artist[2]; ?>">

                        <h3 class="content-wrapper__text">Почта</h3>
                        <input class="content-head_add_input" type="text" name="email" id="email"
                            placeholder="Введите название" value="<?php echo $artist[3]; ?>">

                        <h3 class="content-wrapper__text">Наличие прав администратора</h3>
                        <p><select name="administrator" id="administrator" class="content-head_add_input">
                                <option value="0" id="0">Пользователь</option>
                                <option value="1" id="1">Администратор</option>
                            </select>

                        <p><input class="content-search__button" type="submit" value="Изменить пользователя"></strong>
                        </p>
                    </div>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login = $_POST['login'];
    $name = $_POST['name'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $administrator = $_POST['administrator'];

    $stmt = $conn->prepare("UPDATE customers SET firstname = ?, lastname = ?, email = ?, administrator = ?  WHERE customerid = ?");
    if ($stmt) {
        $stmt->bind_param("sssii", $name, $lname, $email, $administrator, $id);
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

</html>