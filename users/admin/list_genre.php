<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 'off');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$criteria = $_GET['criteria'];
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'id';
$current_sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc';

$next_sort_order = $current_sort_order === 'asc' ? 'desc' : 'asc';
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
    <link rel="stylesheet" href="/table.css">
</head>

<body>
    <header class="header">
        <a href="../general_page.php">Музыкальный сервис</a>

    </header>
    <main class="main">
        <div class="container">
            <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

            <section class="content">
                <div class="content-head_add">
                    <a class="settings__link" href="add_genre.php">Добавление нового жанра</a>
                </div>
                <?php
                if ($criteria == 1) {
                    echo '<a class="settings__link" href="list_comp.php?criteria=1&sort_column=lisening">Статистика популярности треков</a>
                          <p><a class="settings__link" href="list_genre.php?criteria=1&sort_column=lisening">Статистика популярности жанров</a></p>
                          <p><a class="settings__link" href="list_artist.php?criteria=1&sort_column=lisening">Статистика популярности исполнителей</a></p><br>
                          <button type="button" class="content-search__button" onclick="openNewWindow(\'/users/admin/pdf_pricelist.php\')">Выписка о прослушиваниями по жанрам.</button>
                          <button class="content-search__button" onclick="redirectToPage(\'chart_org\')" class="content-wrapper__buttons" >Организационная диаграмма </button>';
                }

                ?>
                <div class="content-main">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <?php
                                    echo '
                                    <th><a href="?criteria=' . $criteria . '&sort_column=id&sort_order=' . $next_sort_order . '">ID</a></th>
                                    <th>Обложка</a></th>
                                    <th><a href="?criteria=' . $criteria . '&sort_column=genre&sort_order=' . $next_sort_order . '">Жанр</th>';
                                    if ($criteria == 1) {
                                        echo '<th><a href="?criteria=' . $criteria . '&sort_column=lisening&sort_order=' . $next_sort_order . '">Число прослушиваний</a></th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="table-content">
                                <?php
                                $sql = "SELECT genreid id, name genre, 
                                coverpath,
                                (select count(listeningdate) from statistic s where s.audiofileid in 
                                (select compositionid from composition c where c.genreid=g.genreid)) lisening
                                from genre g ORDER BY " . $sort_column . " " . $current_sort_order;

                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_row($result)) {
                                        $url_delete = 'delete_genre_process.php?id=' . $row[0];
                                        $image_url = str_replace("C:\\Games\\xampp\\htdocs\\music", "", $row[2]);
                                        if (!file_exists($prefix . $image_url)) {
                                            $image_url = "/media/unknown.png";
                                        }
                                        echo '
                                            <tr>
                                            <td>' . $row[0] . '</td>
                                            <td><img class="content-wrapper__image" ondblclick="changeImage(' . $row[0] . ')" src="' . $image_url . '" alt=""></td>
                                            <td class="editable" data-id="' . $row[0] . '">' . $row[1] . '</td>';
                                        if ($criteria == 1) {
                                            echo '<td>' . $row[3] . '</td>';
                                        }
                                        echo '<td><button onclick="redirectToPage(\'' . $url_delete . '\')" class="content-wrapper__buttons" >Удалить</button></td>    
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
<script>

    function changeImage(genreId) {
        // Создаем input для выбора файла
        var input = $("<input type='file' accept='.png, .jpg, .jpeg' style='display:none;' />");

        // При изменении значения в input выполняем действия
        input.on("change", function (e) {
            var file = e.target.files[0];
            if (file) {
                var formData = new FormData();
                formData.append('imageData', file);
                formData.append('id', genreId);

                // Отправляем изображение на сервер и обновляем базу данных
                $.ajax({
                    type: 'POST',
                    url: 'update_genre_image.php?id=' + genreId, // Обновленный URL с передачей id в запросе
                    data: formData,
                    processData: false, // Обязательно для FormData
                    contentType: false, // Обязательно для FormData
                    success: function (response) {
                        console.log('Изображение обновлено успешно!');
                    },
                    error: function () {
                        console.log('Произошла ошибка при сохранении изображения.');
                    }
                });
            }
        });


        // Кликаем на input, чтобы выбрать файл
        input.click();
    }

    $(document).ready(function () {
        var oldText, newText;

        $(".content-main").on("mouseenter", ".editable", function () {
            $(this).addClass("editHover");
        });

        $(".content-main").on("mouseleave", ".editable", function () {
            $(this).removeClass("editHover");
        });

        $(".content-main").on("dblclick", ".editable", function () {
            if ($(this).hasClass("editing")) {
                // Если элемент уже редактируется, ничего не делаем
                return;
            }

            oldText = $(this).text();
            $(this).addClass("noPad editing")
                .html("")
                .html("<form><input type=\"text\" class=\"editBox\" value=\"" + oldText + "\" /> </form><a href=\"#\" class=\"btnSave\">Сохранить</a> <a href=\"#\" class=\"btnDiscard\">Отменить</a>")
                .unbind('dblclick', replaceHTML);
        });

        $(".content-main").on("click", ".btnSave", function () {
            var saveButton = $(this);
            newText = saveButton.siblings("form")
                .children(".editBox")
                .val().replace(/"/g, "&quot;");

            var genreId = saveButton.closest("tr").find(".editable").data("id");

            $.ajax({
                type: 'POST',
                url: 'update_genre.php',
                data: { genre: newText, id: genreId },
                success: function (response) {
                    console.log(newText);
                    console.log(genreId);
                    console.log('Изменения сохранены успешно!');
                    // Обновляем текст в ячейке после успешного сохранения
                    $(".editable[data-id='" + genreId + "']").text(newText);
                    saveButton.parent().html(newText).removeClass("noPad editing").bind("dblclick", replaceHTML);
                },
                error: function () {
                    console.log('Произошла ошибка при сохранении изменений.');
                }
            });
        });

        $(".content-main").on("click", ".btnDiscard", function () {
            var discardButton = $(this);
            discardButton.parent()
                .html(oldText)
                .removeClass("noPad editing")
                .bind("dblclick", replaceHTML);
        });

        function replaceHTML() {
            oldText = $(this).html()
                .replace(/"/g, "&quot;");
            $(this).addClass("noPad editing")
                .html("")
                .html("<form><input type=\"text\" class=\"editBox\" value=\"" + oldText + "\" /> </form><a href=\"#\" class=\"btnSave\">Сохранить</a> <a href=\"#\" class=\"btnDiscard\">Отменить</a>")
                .unbind('dblclick', replaceHTML);
        }
    });

</script>
<script src="/scripts.js"></script>

</html>