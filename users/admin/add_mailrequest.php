<?php
if (isset($_POST['auto_send']) && $_POST['auto_send'] == 'on') {

    $id = $_SESSION['id'];
    $sql = "select * from customers INNER JOIN login_password on customers.customerid = login_password.customerid where customers.customerid = " . $id;

    $result = $conn->query($sql);
    $artist = $result->fetch_row();

    $text = "Результат анализа аудиофайла " . $datelisening. '
    Предположительный жанр: '.$predicted_genre.'
    Предположительный BPM: '. $predicted_bpm.'
    Предположительная тональность: '. $predicted_tone.'
    Технические подробности:
    Дата анализа: '.$datelisening.'
    Имя файла: '.$filename.'
    Продолжительность аудиофайла: '.$duration_seconds;

    $retval = mail($artist[3], "Результаты анализа аудиофайла на сайте 'Музыкальный сервис'", $text);
    if ($retval == false) {
        echo "Ошибка отправки сообщения!";
    } else {
        $customerid = $_SESSION['id'];
        $datemail = date("Y-m-d");

        $stmt = $conn->prepare("INSERT INTO mail  VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iss", $customerid, $datemail, $text);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error in mail query: " . $conn->error);
        }
    }
}
?>