<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получаем данные о клиентах, включая их местоположение (город)
$sql = "SELECT city, GROUP_CONCAT(CONCAT(firstname, ' ', lastname) SEPARATOR ', ') as names, COUNT(*) as count FROM customers GROUP BY city";
$result = $conn->query($sql);
$cityData = [];
while ($row = $result->fetch_assoc()) {
    $cityData[] = [
        'city' => $row['city'],
        'names' => $row['names'],
        'count' => $row['count']
    ];
}


$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Карта 2ГИС</title>
    <link rel="stylesheet" href="/style.css">
    <script src="https://maps.api.2gis.ru/2.0/loader.js?pkg=full"></script>
    <script type="text/javascript">
        var map;

        // Функция для получения координат города
        function getCoordinates(cityName, apiKey, names, count) {
            // URL для запроса к API 2ГИС
            const url = `https://catalog.api.2gis.com/3.0/items/geocode?q=${encodeURIComponent(cityName)}&fields=items.point&key=${apiKey}`;

            // Выполнение AJAX-запроса
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.result && data.result.items && data.result.items.length > 0) {
                        // Получаем координаты из ответа
                        const point = data.result.items[0].point;
                        console.log(`Координаты для города ${cityName}: Широта: ${point.lat}, Долгота: ${point.lon}`);
                        
                        // Добавляем маркер на карту
                        addMarkerToMap(point.lat, point.lon, cityName, names, count);
                    } else {
                        console.log(`Координаты для города ${cityName} не найдены.`);
                    }
                })
                .catch(error => {
                    console.error(`Ошибка при запросе к API 2ГИС: ${error}`);
                });
        }

        // Функция для добавления маркера на карту
        function addMarkerToMap(lat, lon, cityName, names, count) {
    // Создаем маркер и привязываем его к карте
    DG.then(function () {
        const marker = DG.marker([lat, lon]).addTo(map);
        marker.bindPopup(`Город: ${cityName}<br>Количество людей: ${count}<br>Имена пользователей: ${names}`);
    });
}

        // Инициализация карты
        DG.then(function () {
            map = DG.map('map', {
                center: [54.98, 82.89], // Начальные координаты центра карты
                zoom: 5 // Начальный уровень зума
            });

            // Список городов с их количеством людей
            const cityData = <?php echo json_encode($cityData); ?>;


            const apiKey = '1cd05ce5-a231-41ff-a233-ea9077104a1b'; // Ваш ключ API 2ГИС

            // Получение координат городов и добавление маркеров на карту
            cityData.forEach(data => {
    const cityName = data.city;
    const names = data.names;
    const count = data.count;

    getCoordinates(cityName, apiKey, names, count);
});

        });
    </script>
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
                    <div id="map" style="width: 900px; height: 500px;"></div>
                </div>
            </section>
        </div>
    </main>
</body>

</html>
