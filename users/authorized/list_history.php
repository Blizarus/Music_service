<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <header class="header">
        <a href="../general_page.php">Музыкальный сервис</a>

    </header>
    <main class="main">
        <div class="container">
            <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

            <section class="content">
                <div class="content-main">
                    <div class="table-container">
                        <button type="button" class="content-search__button" onclick="showTable(1)">История поиска</button>
                        <button type="button" class="content-search__button" onclick="showTable(2)">История анализа композиций</button>
                        <button type="button" class="content-search__button" onclick="showTable(3)">История прослушивания композиций</button>
                        <button type="button" class="content-search__button" onclick="showTable(4)">Круговая диаграмма прослушиваемых жанров</button>
                        <div id="table1">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Дата поиска</th>
                                        <th>Критерий поиска</th>
                                        <th>Текст поиска</th>
                                    </tr>
                                </thead>
                                <tbody id="table-content">
                                    <?php
                                    $sql = "select * from search_history INNER JOIN criteria on search_history.criteriad = criteria.criteriad  where customerid  = " . $id;
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = mysqli_fetch_row($result)) {
                                            echo '
                                            <tr>
                                            <td>' . $row[2] . '</td>
                                            <td> Поиск ' . $row[6] . '</td>
                                            <td>' . $row[4] . '</td>
                                            </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="table3" style="display: none">
                            <button type="button" class="content-search__button" onclick="openNewWindow('/users/authorized/pdf_hiltory.php')">Выписка о прослушиваниях</button>
                            <button type="button" class="content-search__button" onclick="redirectToPage('chart_timeline')">Хронология</button>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Дата прослушивания</th>
                                        <th>Исполнитель</th>
                                        <th>Название</th>
                                    </tr>
                                </thead>
                                <tbody id="table-content">
                                    <?php
                                    $sql = "select liseningdate, 
                                    (select name from artist where artist.artistid = (select artistid from composition where composition.compositionid = statistic.audiofileid)) Artist,
                                    (select name from composition where composition.compositionid = statistic.audiofileid) Name
                                    from statistic where customerid  = " . $id;
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = mysqli_fetch_row($result)) {
                                            echo '
                                            <tr>
                                            <td>' . $row[0] . '</td>
                                            <td>' . $row[1] . '</td>
                                            <td>' . $row[2] . '</td>
                                            </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="table2" style="display: none">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Дата анализа</th>
                                        <th>Имя анализируемого файла</th>
                                        <th>Тональность</th>
                                        <th>BPM</th>
                                        <th>Продолжительность в секундах</th>
                                        <th>Жанр</th>
                                    </tr>
                                </thead>
                                <tbody id="table-content">
                                    <?php
                                    $sql = "select * from analysis_history  INNER JOIN song_analysis_result on analysis_history.resultid = song_analysis_result.resultid where analysis_history.customerid = " . $id;
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = mysqli_fetch_row($result)) {
                                            $sql = "select * from tonality where tonalityid=" . $row[6] . "";
                                            $result1 = $conn->query($sql);
                                            $tonality = $result1->fetch_row();

                                            $sql = "select * from genre where genreid=" . $row[9] . "";
                                            $result1 = $conn->query($sql);
                                            $genre = $result1->fetch_row();
                                            echo '
                                            <tr>
                                            <td>' . $row[2] . '</td>
                                            <td>' . $row[4] . '</td>
                                            <td>' . $tonality[1] . '</td>
                                            <td>' . $row[7] . '</td>
                                            <td>' . $row[8] . '</td>
                                            <td>' . $genre[1] . '</td>
                                            </tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="table4" style="display: none">
                            <?php 
                            $sql = "SELECT g.name AS genre_name, COUNT(s.statisticid) AS listens
                            FROM statistic s
                            JOIN composition c ON s.audiofileid = c.compositionid
                            JOIN genre g ON c.genreid = g.genreid
                            WHERE s.customerid = $id
                            GROUP BY g.name";
                            $result = $conn->query($sql);
                            
                            // Создание массива данных для круговой диаграммы
                            $data = array();
                            while ($row = $result->fetch_assoc()) {
                                $data[] = array($row['genre_name'], intval($row['listens']));
                            }
                            $conn->close();
                            ?>
                            <div id="chart_div" style="width: 900px; height: 500px;"></div>
                        </div>
                    </div>
                </div>
                </form>
            </section>
        </div>
    </main>
</body>
<script src="/scripts_add.js"></script>
<script src="/scripts.js"></script>
<script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Жанр');
            data.addColumn('number', 'Прослушивания');
            data.addRows(<?php echo json_encode($data); ?>);

            var options = {
                title: 'Процентное соотношение прослушиваний по жанрам',
                pieHole: 0.4,
                width: 1000, // Установка ширины графика
                height: 800, // Установка высоты графика
                backgroundColor: 'transparent', // Цвет фона
                legend: {
                    textStyle: {color: '#FFFFFF'} // Цвет текста легенды
                },
                titleTextStyle: {
                    color: '#FFFFFF' // Цвет заголовка
                },
                pieSliceTextStyle: {
                    color: '#FFFFFF' // Цвет текста секторов
                }
            };

            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>

</html>