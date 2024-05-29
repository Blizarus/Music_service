<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_SESSION['id'];
include 'task10_functions.php';
?>
<!DOCTYPE html>
<html>
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
                    <div class="table-container"></div>
<?php
    // SQL запрос для получения данных
    $sql = "    SELECT 
    DATE_FORMAT(s.listeningdate, '%Y-%m') AS month, 
    COUNT(s.statisticid) AS order_count,
    SUM(CASE WHEN a.artistid = 13 THEN 1 ELSE 0 END) AS total_amount,
    SUM(CASE WHEN g.genreid = 6 THEN 1 ELSE 0 END) AS total_delivery
FROM 
    statistic s
LEFT JOIN 
    audiofiles af ON s.audiofileid = af.audiofileid
LEFT JOIN 
    composition c ON af.audiofileid = c.compositionid
LEFT JOIN 
    artist a ON c.artistid = a.artistid
LEFT JOIN 
    genre g ON c.genreid = g.genreid
GROUP BY 
month
ORDER BY 
month;";

    $result = $conn->query($sql);

   // Создаем массивы данных для графиков
$months = array();
$orderCounts = array();
$amounts = array();
$deliveryAmounts = array();



if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $months[] = $row['month'];
        $orderCounts[] = $row['order_count'];
        $amounts[] = $row['total_amount'];
        $deliveryAmounts[] = $row['total_delivery'];
    }
}

list($vOrdersMedian, $tOrdersMedian,$series_array_orders,$ordersMedian) = analyze_series($orderCounts);
list($vAmountsMedian, $tAmountsMedian,$series_array_amounts,$amountsMedian) = analyze_series($amounts);
list($vDeliveryAmountsMedian, $tDeliveryAmountsMedian,$series_array_delivery,$deliveryMedian) = analyze_series($deliveryAmounts);

list($vOrdersRise, $tOrdersRise,$series_array_orders_rise) = analyze_differences($orderCounts);
list($vAmountsRise, $tAmountsRise,$series_array_amounts_rise) = analyze_differences($amounts);
list($vDeliveryAmountsRise, $tDeliveryAmountsRise,$series_array_delivery_rise) = analyze_differences($deliveryAmounts);





$orderCountsFuture = $orderCounts;
$amountsFuture = $amounts;
$deliveryAmountsFuture = $deliveryAmounts;
array_push($orderCountsFuture, "null", "null");
array_push($amountsFuture, "null", "null");
array_push($deliveryAmountsFuture, "null", "null");


    $monthsFuture=$months;
     
    $newMonth1 = getNextMonth(end($monthsFuture)); // Получаем следующий месяц после последнего месяца в массиве
    $newMonth1 = strtotime($newMonth1);
    $newMonth1 = date("Y-m",$newMonth1);
    
$newMonth2 = getNextMonth($newMonth1);
$newMonth2 = strtotime($newMonth2);
    $newMonth2 = date("Y-m",$newMonth2); // Получаем месяц после следующего месяца
 

array_push($monthsFuture, $newMonth1, $newMonth2);
 
  
    $weights = array(-3, 12, 17, 12, -3);

                    
                    
    
    // Вычисляем скользящие средние
    $ordersMA3 = movingAverage($orderCounts, 1);
    $ordersMA5 = weightedMovingAverage($orderCounts, $weights);
    $ordersMA7 = movingAverage($orderCounts, 3);
    $amountsMA3 = movingAverage($amounts, 1);
    $amountsMA5 = weightedMovingAverage($amounts, $weights);
    $amountsMA7 = movingAverage($amounts, 3);
    $deliveryAmountsMA3 = movingAverage($deliveryAmounts, 1);
    $deliveryAmountsMA5 = weightedMovingAverage($deliveryAmounts, $weights);
    $deliveryAmountsMA7 = movingAverage($deliveryAmounts, 3);

    $ordersMA3Recovery = movingAverageRecovery($orderCounts, 1);
    $ordersMA5Recovery = weightedMovingAverageRecovery($orderCounts, $weights);
    $ordersMA7Recovery = movingAverageRecovery($orderCounts, 3);
    $amountsMA3Recovery = movingAverageRecovery($amounts, 1);
    $amountsMA5Recovery = weightedMovingAverageRecovery($amounts, $weights);
    $amountsMA7Recovery = movingAverageRecovery($amounts, 3);
    $deliveryAmountsMA3Recovery = movingAverageRecovery($deliveryAmounts, 1);
    $deliveryAmountsMA5Recovery = weightedMovingAverageRecovery($deliveryAmounts, $weights);
    $deliveryAmountsMA7Recovery = movingAverageRecovery($deliveryAmounts, 3);

    $ordersMA3Future = movingAverageFuture($orderCounts, 1);
    $ordersMA5Future = weightedMovingAverageFuture($orderCounts, $weights);
    $ordersMA7Future = movingAverageFuture($orderCounts, 3);
    $amountsMA3Future = movingAverageFuture($amounts, 1);
    $amountsMA5Future = weightedMovingAverageFuture($amounts, $weights);
    $amountsMA7Future = movingAverageFuture($amounts, 3);
    $deliveryAmountsMA3Future = movingAverageFuture($deliveryAmounts, 1);
    $deliveryAmountsMA5Future = weightedMovingAverageFuture($deliveryAmounts, $weights);
    $deliveryAmountsMA7Future = movingAverageFuture($deliveryAmounts, 3);

     
    $conn->close();

    ?>
</div>
    <!-- Создаем контейнеры для графиков -->
    <div id="ordersChart" style="width: 900px; height: 500px; margin: 20px auto;"></div>
    <table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>
<?php
// Функция для добавления строки в таблицу
function addTableRow($t, $yi, $l3, $l7, $weighted_l5) {
echo "<tr>";
echo "<td>$t</td>";
echo "<td>$yi</td>";
echo "<td>" . ($l3 !== "null" ? number_format($l3, 0) : "-") . "</td>";
echo "<td>" . ($l7 !== "null" ? number_format($l7, 0) : "-") . "</td>";
echo "<td>" . ($weighted_l5 !== "null" ? number_format($weighted_l5, 0) : "-") . "</td>";
echo "</tr>";
}
function addTableColumnSeries($t, $series_array) {
    echo "<tr>";
    echo "<td>$t</td>";
    echo "<td>$series_array</td>";
    echo "</tr>";
    }
// Функция для добавления строки в таблицу
function addTableRow2($t, $yi, $l3, $l7, $weighted_l5) {
    echo "<tr>";
    echo "<td>$t</td>";
    echo "<td>$yi</td>";
    echo "<td>" . ($l3 !== "null" ? number_format($l3, 3) : "-") . "</td>";
    echo "<td>" . ($l7 !== "null" ? number_format($l7, 3) : "-") . "</td>";
    echo "<td>" . ($weighted_l5 !== "null" ? number_format($weighted_l5, 3) : "-") . "</td>";
    echo "</tr>";
    } 
function addTableRowParameters($n,$t, $yi, $ytt, $tt, $yttt,$tttt,$lnyt,$lnytt) {
        echo "<tr>";
        echo "<td>$n</td>";
        echo "<td>$t</td>";
        echo "<td>$yi</td>";
        echo "<td>" . ($ytt !== "null" ? number_format($ytt, 0) : "-") . "</td>";
        echo "<td>" . ($tt !== "null" ? number_format($tt, 0) : "-") . "</td>";
        echo "<td>" . ($yttt !== "null" ? number_format($yttt, 0) : "-") . "</td>";
        echo "<td>" . ($tttt !== "null" ? number_format($tttt, 0) : "-") . "</td>";
        echo "<td>" . ($lnyt !== "null" ? number_format($lnyt, 4) : "-") . "</td>";
        echo "<td>" . ($lnytt !== "null" ? number_format($lnytt, 4) : "-") . "</td>";

        echo "</tr>";
        }
// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
$t = $months[$i];
$yi = $orderCounts[$i];
$l3 = isset($ordersMA3[$i]) ? $ordersMA3[$i] : "-";
$l7 = isset($ordersMA7[$i]) ? $ordersMA7[$i] : "-";
$weighted_l5 = isset($ordersMA5[$i]) ? $ordersMA5[$i] : "-";
addTableRow2($t, $yi, $l3, $l7, $weighted_l5);
}
?>
</tbody>
</table>

<div id="ordersChart2" style="width: 900px; height: 500px; margin: 20px auto;"></div>

<table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>
<?php
 

 

// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
$t = $months[$i];
$yi = $orderCounts[$i];
$l3 = isset($ordersMA3Recovery[$i]) ? $ordersMA3Recovery[$i] : "-";
$l7 = isset($ordersMA7Recovery[$i]) ? $ordersMA7Recovery[$i] : "-";
$weighted_l5 = isset($ordersMA5Recovery[$i]) ? $ordersMA5Recovery[$i] : "-";
addTableRow2($t, $yi, $l3, $l7, $weighted_l5);
}
?>
</tbody>
</table>
<div id="ordersChart3" style="width: 900px; height: 500px; margin: 20px auto;"></div>

<table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>
<?php
 

 

 $count = count($monthsFuture);
 for ($i = 0; $i < $count; $i++) {
 $t = $monthsFuture[$i];
 if ($i < $count - 2) {
 $yi = $orderCounts[$i];
 } else {
 $yi = "-";
 }
 $l3 = isset($ordersMA3Future[$i]) ? $ordersMA3Future[$i] : "-";
 $l7 = isset($ordersMA7Future[$i]) ? $ordersMA7Future[$i] : "-";
 $weighted_l5 = isset($ordersMA5Future[$i]) ? $ordersMA5Future[$i] : "-";
 
 addTableRow2($t, $yi, $l3, $l7, $weighted_l5);
 }
 
?>
</tbody>
</table>
<p class="content-news_description"><strong>Тренд</strong>: Вероятно, будет показывать устойчивый рост с течением времени, так как общее количество прослушиваний увеличивается. <br>
                <strong>Сезонность</strong>: Будет выражена в виде пиков в зимние месяцы (декабрь и январь), связано с зимними праздниками. <br>
                <strong>Остаток</strong>: Случайные колебания, не объясняемые трендом или сезонностью.
                    </p>

<?php
echo '<div class="content-news_description"><p> Значение медианы серии Me = ';
echo ''. $ordersMedian .'</p>';
echo '</div>';
?>
<table>
<thead>
<tr>
<th>i</th>
<th>oi</th>
</tr>
</thead>
<tbody>
<?php
 $arr = range(1,count($series_array_orders));
// Вывод остальных значений
for ($i = 0; $i < count($series_array_orders); $i++) {
$t = $arr[$i];
addTableColumnSeries($t, $series_array_orders[$i]);
}
?>
</tbody>
</table> 
<?php

list($inequality1median, $inequality2median)=check_inequalities_median($vOrdersMedian,$tOrdersMedian,count($orderCounts));
echo '<div class="content-news_description">';
echo '<p>' . $vOrdersMedian . ' > [1/2*('.count($orderCounts).'+1-1,96*sqrt('.count($orderCounts).'-1)]=['.(0.5 * (count($orderCounts) + 1 - 1.96 * sqrt(count($orderCounts) - 1))).']='.floor(0.5 * (count($orderCounts) + 1 - 1.96 * sqrt(count($orderCounts) - 1))).'</p>';
echo '<p>' . $tOrdersMedian . ' <[1.43*ln('.count($orderCounts).'+1)]=['.(1.43 * log(count($orderCounts) + 1)).']='.floor(1.43 * log(count($orderCounts) + 1)).'</p>';
echo '</div>';

if ($inequality1median && $inequality2median)
{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда принимается с помощью критерия, основанного на медиане</p></div>';

}
else{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда отвергается с вероятностью ошибки 0.05<а<0.0975 с помощью критерия, основанного на медиане. Следовательно, подтверждается наличие зависящей от времени неслучайной составляющей.</p></div>';
}
?>
<table>
<thead>
<tr>
<th>i</th>
<th>oi</th>
</tr>
</thead>
<tbody>
<?php
 $arr = range(1,count($series_array_orders_rise));
// Вывод остальных значений
for ($i = 0; $i < count($series_array_orders_rise); $i++) {
$t = $arr[$i];
addTableColumnSeries($t, $series_array_orders_rise[$i]);
}
?>
</tbody>
</table> 
<?php
echo '<div class="content-news_description">';
echo '<p>' . $vOrdersRise . ' > [1/3*(2*'.count($orderCounts).
'-1)-1,96*sqrt((16*'.count($orderCounts).'-29)/90)]=['.((1/3) * (2 * count($orderCounts) - 1) - 1.96 * sqrt((16 * count($orderCounts) - 29) / 90)).
']='.floor((1/3) * (2 * count($orderCounts) - 1) - 1.96 * sqrt((16 * count($orderCounts) - 29) / 90)).'</p>';
echo '<p>' . $tOrdersRise . ' < 26</p>';
echo '</div>';
list($inequality1rise, $inequality2rise)=check_inequalities_rise($vOrdersRise,$tOrdersRise,count($orderCounts));
if ($inequality1rise && $inequality2rise)
{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда принимается с помощью критерия "восходящих и нисходящих" серий.</p></div>';

}
else{
    echo '<div class="content-news_description"><p> Нулевая гипотеза о случайности ряда отвергается с помощью критерия "восходящих и нисходящих" серий. Следовательно, подтверждается наличие зависящей от времени неслучайной составляющей.</p></div>';
}
?>

<table>
<thead>
<tr>
<th>№</th>
<th>yt</th>
<th>t</th>
<th>yt*t</th>
<th>t^2</th>
<th>y*t^2</th>
<th>t^4</th>
<th>ln(yt)</th>
<th>ln((yt)*t)</th>
</tr>
</thead>
<tbody>
<?php
$array = range(-6, 6);
$orderCountsYtt=array();
$orderCountsTt=array();
$orderCountsTttt=array();
$orderCountsYttt=array();
$orderCountsLnyt=array();
$orderCountsLnytt=array();

// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
    $t = $array[$i];
    $n=$array[$i]+7;
    $yi = $orderCounts[$i];
    $ytt = $yi*$t;
    $orderCountsYtt[]=$ytt;
    $tt = $t*$t;
    $orderCountsTt[]=$tt;
    $yttt = $tt*$yi;
    $orderCountsYttt[]=$yttt;
    $tttt = $tt*$tt;
    $orderCountsTttt[]=$tt;
    $lnyt=log($yi);
    $orderCountsLnyt[]=$lnyt;
    $lnytt=$lnyt*$t;
    $orderCountsLnytt[]=$lnytt;
    addTableRowParameters($n,$t, $yi, $ytt, $tt, $yttt,$tttt,$lnyt,$lnytt);
    }
    ?>
    </tbody>
    </table>
    <?php 
    $orderCountsSum = array_sum($orderCounts);
    $a0_orders=$orderCountsSum/13;
    $orderCountsYttSum = array_sum($orderCountsYtt);
    $orderCountsTtSum =array_sum($orderCountsTt);
    $a1_orders=$orderCountsYttSum/$orderCountsTtSum;
    
    echo '<div class="content-news_description"><p> Уравнение линейного тренда yt= '.number_format($a0_orders,3).'+'.number_format($a1_orders,3).'*t</p></div>';
    $orderCountsTtttSum=array_sum($orderCountsTttt);
    $orderCountsYtttSum = array_sum($orderCountsYttt);
    $orderCountsSum = array_sum($orderCounts);

    $a2_orders=(13*$orderCountsYtttSum-$orderCountsTtSum*$orderCountsSum)/(13*$orderCountsTtttSum-($orderCountsTtSum*$orderCountsTtSum));
    $a0_orders_=$a0_orders-($orderCountsTtSum/13)*$a2_orders;
    
    echo '<div class="content-news_description"><p> Уравнение параболического тренда yt= '.number_format($a0_orders_,3).'+'.number_format($a1_orders,3).'*t+'.number_format($a2_orders,2).'*t2</p></div>';

    $orderCountsLnytSum=array_sum($orderCountsLnyt);
    $orderCountsLnyttSum = array_sum($orderCountsLnytt);
    $lna=$orderCountsLnytSum/13;
    $lnb=$orderCountsLnyttSum/$orderCountsTtSum;
    $a_orders=exp($lna);
    $b_orders=exp($lnb);
    echo '<div class="content-news_description"><p> Уравнение показательного тренда yt= '.number_format($a_orders,3).'*'.number_format($b_orders,2).'^t</p></div>';

    ?>
    <div id="ordersChart4" style="width: 900px; height: 500px; margin: 20px auto;"></div>

    <div id="amountChart" style="width: 900px; height: 500px; margin: 20px auto;"></div>
    <table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>
<?php
 
// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
$t = $months[$i];
$yi = $amounts[$i];
$l3 = isset($amountsMA3[$i]) ? $amountsMA3[$i] : "-";
$l7 = isset($amountsMA7[$i]) ? $amountsMA7[$i] : "-";
$weighted_l5 = isset($amountsMA5[$i]) ? $amountsMA5[$i] : "-";
addTableRow($t, $yi, $l3, $l7, $weighted_l5);
}
?>
</tbody>
</table> 
<div id="amountChart2" style="width: 900px; height: 500px; margin: 20px auto;"></div>
<table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>
<?php
 
// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
$t = $months[$i];
$yi = $amounts[$i];
$l3 = isset($amountsMA3Recovery[$i]) ? $amountsMA3Recovery[$i] : "-";
$l7 = isset($amountsMA7Recovery[$i]) ? $amountsMA7Recovery[$i] : "-";
$weighted_l5 = isset($amountsMA5Recovery[$i]) ? $amountsMA5Recovery[$i] : "-";
addTableRow($t, $yi, $l3, $l7, $weighted_l5);
}
?>
</tbody>
</table> 
<div id="amountChart3" style="width: 900px; height: 500px; margin: 20px auto;"></div>
 
<table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>
<?php
 

 $count = count($monthsFuture);
 for ($i = 0; $i < $count; $i++) {
 $t = $monthsFuture[$i];
 if ($i < $count - 2) {
 $yi = $amounts[$i];
 } else {
 $yi = "-";
 }
 $l3 = isset($amountsMA3Future[$i]) ? $amountsMA3Future[$i] : "-";
 $l7 = isset($amountsMA7Future[$i]) ? $amountsMA7Future[$i] : "-";
 $weighted_l5 = isset($amountsMA5Future[$i]) ? $amountsMA5Future[$i] : "-";
 
 addTableRow($t, $yi, $l3, $l7, $weighted_l5);
 }
?>
</tbody>
</table> 

<p class="content-news_description"><strong>Тренд</strong>: Вероятно, будет показывать устойчивый рост с течением времени, так как общее количество прослушиваний увеличивается. <br>
                <strong>Сезонность</strong>: Будет выражена в виде пиков в зимние месяцы (декабрь и январь), связано с зимними праздниками. <br>
                <strong>Остаток</strong>: Случайные колебания, не объясняемые трендом или сезонностью.
                    </p>
<?php
echo '<div class="content-news_description"><p> Значение медианы серии Me = ';
echo ''. $amountsMedian .'</p>';
echo '</div>';
?>
<table>
<thead>
<tr>
<th>i</th>
<th>oi</th>
</tr>
</thead>
<tbody>
<?php
 $arr = range(1,count($series_array_amounts));
// Вывод остальных значений
for ($i = 0; $i < count($series_array_amounts); $i++) {
$t = $arr[$i];
addTableColumnSeries($t, $series_array_amounts[$i]);
}
?>
</tbody>
</table> 
<?php
list($inequality1median, $inequality2median)=check_inequalities_median($vAmountsMedian,$tAmountsMedian,count($amounts));
echo '<div class="content-news_description">';
echo '<p>' . $vAmountsMedian . ' > [1/2*('.count($amounts).'+1-1,96*sqrt('.count($amounts).'-1)]=['.(0.5 * (count($amounts) + 1 - 1.96 * sqrt(count($amounts) - 1))).']='.floor(0.5 * (count($amounts) + 1 - 1.96 * sqrt(count($amounts) - 1))).'</p>';
echo '<p>' . $tAmountsMedian . ' <[1.43*ln('.count($amounts).'+1)]=['.(1.43 * log(count($amounts) + 1)).']='.floor(1.43 * log(count($amounts) + 1)).'</p>';
echo '</div>';
if ($inequality1median && $inequality2median)
{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда принимается с помощью критерия, основанного на медиане.</p></div>';

}
else{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда отвергается с вероятностью ошибки 0.05<а<0.0975 с помощью критерия, основанного на медиане. Следовательно, подтверждается наличие зависящей от времени неслучайной составляющей.</p></div>';
}
?>
<table>
<thead>
<tr>
<th>i</th>
<th>oi</th>
</tr>
</thead>
<tbody>
<?php
 $arr = range(1,count($series_array_amounts_rise));
// Вывод остальных значений
for ($i = 0; $i < count($series_array_amounts_rise); $i++) {
$t = $arr[$i];
addTableColumnSeries($t, $series_array_amounts_rise[$i]);
}
?>
</tbody>
</table> 
<?php
list($inequality1rise, $inequality2rise)=check_inequalities_rise($vAmountsRise,$tAmountsRise,count($amounts));
echo '<div class="content-news_description">';
echo '<p>' . $vAmountsRise . ' > [1/3*(2*'.count($amounts).
'-1)-1,96*sqrt((16*'.count($amounts).'-29)/90)]=['.((1/3) * (2 * count($amounts) - 1) - 1.96 * sqrt((16 * count($amounts) - 29) / 90)).
']='.floor((1/3) * (2 * count($amounts) - 1) - 1.96 * sqrt((16 * count($amounts) - 29) / 90)).'</p>';
echo '<p>' . $tAmountsRise . ' < 26</p>';
echo '</div>';
if ($inequality1rise && $inequality2rise)
{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда принимается с помощью критерия "восходящих и нисходящих" серий.</p></div>';

}
else{
    echo '<div class="content-news_description"><p> Нулевая гипотеза о случайности ряда отвергается с помощью критерия "восходящих и нисходящих" серий. Следовательно, подтверждается наличие зависящей от времени неслучайной составляющей.</p></div>';
}
?>
<table>
<thead>
<tr>
<th>№</th>
<th>yt</th>
<th>t</th>
<th>yt*t</th>
<th>t^2</th>
<th>y*t^2</th>
<th>t^4</th>
<th>ln(yt)</th>
<th>ln((yt)*t)</th>
</tr>
</thead>
<tbody>
<?php
$array = range(-6, 6);
$amountsYtt=array();
$amountsTt=array();
$amountsTttt=array();
$amountsYttt=array();
$amountsLnyt=array();
$amountsLnytt=array();

// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
    $t = $array[$i];
    $n=$array[$i]+7;
    $yi = $amounts[$i];
    $ytt = $yi*$t;
    $amountsYtt[]=$ytt;
    $tt = $t*$t;
    $amountsTt[]=$tt;
    $yttt = $tt*$yi;
    $amountsYttt[]=$yttt;
    $tttt = $tt*$tt;
    $amountsTttt[]=$tt;
    $lnyt=log($yi);
    $amountsLnyt[]=$lnyt;
    $lnytt=$lnyt*$t;
    $amountsLnytt[]=$lnytt;
    addTableRowParameters($n,$t, $yi, $ytt, $tt, $yttt,$tttt,$lnyt,$lnytt);
    }
    ?>
    </tbody>
    </table>
    <?php 
    $amountsSum = array_sum($amounts);
    $a0_amounts=$amountsSum/13;
    $amountsYttSum = array_sum($amountsYtt);
    $amountsTtSum =array_sum($amountsTt);
    $a1_amounts=$amountsYttSum/$amountsTtSum;
    
    echo '<div class="content-news_description"><p> Уравнение линейного тренда yt= '.number_format($a0_amounts,3).'+'.number_format($a1_amounts,3).'*t</p></div>';
    $amountsTtttSum=array_sum($amountsTttt);
    $amountsYtttSum = array_sum($amountsYttt);
    $amountsSum = array_sum($amounts);

    $a2_amounts=(13*$amountsYtttSum-$amountsTtSum*$amountsSum)/(13*$amountsTtttSum-($amountsTtSum*$amountsTtSum));
    $a0_amounts_=$a0_amounts-($amountsTtSum/13)*$a2_amounts;
    
    echo '<div class="content-news_description"><p> Уравнение параболического тренда yt= '.number_format($a0_amounts_,3).'+'.number_format($a1_amounts,3).'*t+'.number_format($a2_amounts,2).'*t2</p></div>';

    $amountsLnytSum=array_sum($amountsLnyt);
    $amountsLnyttSum = array_sum($amountsLnytt);
    $lna=$amountsLnytSum/13;
    $lnb=$amountsLnyttSum/$amountsTtSum;
    $a_amounts=exp($lna);
    $b_amounts=exp($lnb);
    echo '<div class="content-news_description"><p> Уравнение показательного тренда yt= '.number_format($a_amounts,3).'*'.number_format($b_amounts,2).'^t</p></div>';

    ?>
    <div id="amountChart4" style="width: 900px; height: 500px; margin: 20px auto;"></div>
    <div id="deliveryChart" style="width: 900px; height: 500px; margin: 20px auto;"></div>
    <table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>

<?php
 

// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
$t = $months[$i];
$yi = $deliveryAmounts[$i];
$l3 = isset($deliveryAmountsMA3[$i]) ? $deliveryAmountsMA3[$i] : "-";
$l7 = isset($deliveryAmountsMA7[$i]) ? $deliveryAmountsMA7[$i] : "-";
$weighted_l5 = isset($deliveryAmountsMA5[$i]) ? $deliveryAmountsMA5[$i] : "-";
addTableRow($t, $yi, $l3, $l7, $weighted_l5);
}
?>
</tbody>
</table>

<div id="deliveryChart2" style="width: 900px; height: 500px; margin: 20px auto;"></div>
<table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>
<?php
 

// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
$t = $months[$i];
$yi = $deliveryAmounts[$i];
$l3 = isset($deliveryAmountsMA3Recovery[$i]) ? $deliveryAmountsMA3Recovery[$i] : "-";
$l7 = isset($deliveryAmountsMA7Recovery[$i]) ? $deliveryAmountsMA7Recovery[$i] : "-";
$weighted_l5 = isset($deliveryAmountsMA5Recovery[$i]) ? $deliveryAmountsMA5Recovery[$i] : "-";
addTableRow($t, $yi, $l3, $l7, $weighted_l5);
}
?>
</tbody>
</table>
<div id="deliveryChart3" style="width: 900px; height: 500px; margin: 20px auto;"></div>
<table>
<thead>
<tr>
<th>t</th>
<th>yi</th>
<th>l=3</th>
<th>l=7</th>
<th>Взвешенная l=5</th>
</tr>
</thead>
<tbody>
<?php
 

 $count = count($monthsFuture);
 for ($i = 0; $i < $count; $i++) {
 $t = $monthsFuture[$i];
 if ($i < $count - 2) {
 $yi = $deliveryAmounts[$i];
 } else {
 $yi = "-";
 }
 $l3 = isset($deliveryAmountsMA3Future[$i]) ? $deliveryAmountsMA3Future[$i] : "-";
 $l7 = isset($deliveryAmountsMA7Future[$i]) ? $deliveryAmountsMA7Future[$i] : "-";
 $weighted_l5 = isset($deliveryAmountsMA5Future[$i]) ? $deliveryAmountsMA5Future[$i] : "-";
 
 addTableRow($t, $yi, $l3, $l7, $weighted_l5);
 }
?>
</tbody>
</table> 
<p class="content-news_description"><strong>Тренд</strong>: Вероятно, будет показывать устойчивый рост с течением времени, так как общее количество прослушиваний увеличивается. <br>
                <strong>Сезонность</strong>: Будет выражена в виде пиков в зимние месяцы (декабрь и январь), связано с зимними праздниками. <br>
                <strong>Остаток</strong>: Случайные колебания, не объясняемые трендом или сезонностью.
                    </p><?php
echo '<div class="content-news_description"><p> Значение медианы серии Me = ';
echo ''. $deliveryMedian .'</p>';
echo '</div>';
?>
<table>
<thead>
<tr>
<th>i</th>
<th>oi</th>
</tr>
</thead>
<tbody>
<?php
 $arr = range(1,count($series_array_delivery));
// Вывод остальных значений
for ($i = 0; $i < count($series_array_delivery); $i++) {
$t = $arr[$i];
addTableColumnSeries($t, $series_array_delivery[$i]);
}
?>
</tbody>
</table> 
<?php
list($inequality1median, $inequality2median)=check_inequalities_median($vDeliveryAmountsMedian,$tDeliveryAmountsMedian,count($deliveryAmounts));
echo '<div class="content-news_description">';
echo '<p>' . $vDeliveryAmountsMedian . ' > [1/2*('.count($deliveryAmounts).'+1-1,96*sqrt('.count($deliveryAmounts).'-1)]=['.(0.5 * (count($deliveryAmounts) + 1 - 1.96 * sqrt(count($amounts) - 1))).']='.floor(0.5 * (count($deliveryAmounts) + 1 - 1.96 * sqrt(count($deliveryAmounts) - 1))).'</p>';
echo '<p>' . $tDeliveryAmountsMedian . ' <[1.43*ln('.count($deliveryAmounts).'+1)]=['.(1.43 * log(count($deliveryAmounts) + 1)).']='.floor(1.43 * log(count($deliveryAmounts) + 1)).'</p>';
echo '</div>';
if ($inequality1median && $inequality2median)
{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда принимается с помощью критерия, основанного на медиане.</p></div>';

}
else{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда отвергается с вероятностью ошибки 0.05<а<0.0975 с помощью критерия, основанного на медиане. Следовательно, подтверждается наличие зависящей от времени неслучайной составляющей.</p></div>';
}
?>
<table>
<thead>
<tr>
<th>i</th>
<th>oi</th>
</tr>
</thead>
<tbody>
<?php
 $arr = range(1,count($series_array_delivery_rise));
// Вывод остальных значений
for ($i = 0; $i < count($series_array_delivery_rise); $i++) {
$t = $arr[$i];
addTableColumnSeries($t, $series_array_delivery_rise[$i]);
}
?>
</tbody>
</table> 
<?php
list($inequality1rise, $inequality2rise)=check_inequalities_rise($vDeliveryAmountsRise,$tDeliveryAmountsRise,count($deliveryAmounts));
echo '<div class="content-news_description">';
echo '<p>' . $vDeliveryAmountsRise . ' > [1/3*(2*'.count($deliveryAmounts).
'-1)-1,96*sqrt((16*'.count($deliveryAmounts).'-29)/90)]=['.((1/3) * (2 * count($deliveryAmounts) - 1) - 1.96 * sqrt((16 * count($deliveryAmounts) - 29) / 90)).
']='.floor((1/3) * (2 * count($deliveryAmounts) - 1) - 1.96 * sqrt((16 * count($deliveryAmounts) - 29) / 90)).'</p>';
echo '<p>' . $tDeliveryAmountsRise . ' < 26</p>';
echo '</div>';
if ($inequality1rise && $inequality2rise)
{
    echo '<div class="content-news_description"><p>Гипотеза о случайности ряда принимается с помощью критерия "восходящих и нисходящих" серий.</p></div>';

}
else{
    echo '<div class="content-news_description"><p> Нулевая гипотеза о случайности ряда отвергается с помощью критерия "восходящих и нисходящих" серий. Следовательно, подтверждается наличие зависящей от времени неслучайной составляющей.</p></div>';
}
?>
<table>
<thead>
<tr>
<th>№</th>
<th>yt</th>
<th>t</th>
<th>yt*t</th>
<th>t^2</th>
<th>y*t^2</th>
<th>t^4</th>
<th>ln(yt)</th>
<th>ln((yt)*t)</th>
</tr>
</thead>
<tbody>
<?php
$array = range(-6, 6);
$deliveryYtt=array();
$deliveryTt=array();
$deliveryTttt=array();
$deliveryYttt=array();
$deliveryLnyt=array();
$deliveryLnytt=array();

// Вывод остальных значений
for ($i = 0; $i < count($months); $i++) {
    $t = $array[$i];
    $n=$array[$i]+7;
    $yi = $deliveryAmounts[$i];
    $ytt = $yi*$t;
    $deliveryYtt[]=$ytt;
    $tt = $t*$t;
    $deliveryTt[]=$tt;
    $yttt = $tt*$yi;
    $deliveryYttt[]=$yttt;
    $tttt = $tt*$tt;
    $deliveryTttt[]=$tt;
    $lnyt=log($yi);
    $deliveryLnyt[]=$lnyt;
    $lnytt=$lnyt*$t;
    $deliveryLnytt[]=$lnytt;
    addTableRowParameters($n,$t, $yi, $ytt, $tt, $yttt,$tttt,$lnyt,$lnytt);
    }
    ?>
    </tbody>
    </table>
    <?php 
    $deliverySum = array_sum($deliveryAmounts);
    $a0_delivery=$deliverySum/13;
    $deliveryYttSum = array_sum($deliveryYtt);
    $deliveryTtSum =array_sum($deliveryTt);
    $a1_delivery=$deliveryYttSum/$deliveryTtSum;
    
    echo '<div class="content-news_description"><p> Уравнение линейного тренда yt= '.number_format($a0_delivery,3).'+'.number_format($a1_delivery,3).'*t</p></div>';
    $deliveryTtttSum=array_sum($deliveryTttt);
    $deliveryYtttSum = array_sum($deliveryYttt);
    $deliverySum = array_sum($deliveryAmounts);

    $a2_delivery=(13*$deliveryYtttSum-$deliveryTtSum*$deliverySum)/(13*$deliveryTtttSum-($deliveryTtSum*$deliveryTtSum));
    $a0_delivery_=$a0_delivery-($deliveryTtSum/13)*$a2_delivery;
    
    echo '<div class="content-news_description"><p> Уравнение параболического тренда yt= '.number_format($a0_delivery_,3).'+'.number_format($a1_delivery,3).'*t+'.number_format($a2_delivery,2).'*t2</p></div>';

    $deliveryLnytSum=array_sum($deliveryLnyt);
    $deliveryLnyttSum = array_sum($deliveryLnytt);
    $lna=$deliveryLnytSum/13;
    $lnb=$deliveryLnyttSum/$deliveryTtSum;
    $a_delivery=exp($lna);
    $b_delivery=exp($lnb);
    echo '<div class="content-news_description"><p> Уравнение показательного тренда yt= '.number_format($a_delivery,3).'*'.number_format($b_delivery,2).'^t</p></div>';

    ?>
    <div id="deliveryChart4" style="width: 900px; height: 500px; margin: 20px auto;"></div>
    <script>
        // Функция для загрузки библиотеки Google Charts
        google.charts.load('current', {'packages':['corechart']});

        // Функция для построения графика прослушиваний
        google.charts.setOnLoadCallback(drawOrdersChart);
        function drawOrdersChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Количество прослушиваний');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php

for ($i = 0; $i < count($months); $i++) {
    
echo "['" . $months[$i] . "', " . $orderCounts[$i] . ", " . $ordersMA3[$i] . ", " . $ordersMA5[$i] . ", " . $ordersMA7[$i] . "],";
}
?>
]);

            var options = {
                title: 'Количество прослушиваний по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                trendlines: {
      0: {
        type: 'linear',
        visibleInLegend: true,
      }
    },
            series: {
                1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] }// Для линии скользящего среднего  
        }
                
            };

            

            var chart = new google.visualization.LineChart(document.getElementById('ordersChart'));
            chart.draw(data, options);
        }


// Функция для построения графика прослушиваний c краевыми значениями
google.charts.setOnLoadCallback(drawOrdersChart2);
        function drawOrdersChart2() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Количество прослушиваний');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php
for ($i = 0; $i < count($months); $i++) {
echo "['" . $months[$i] . "', " . $orderCounts[$i] . ", " . $ordersMA3Recovery[$i] . ", " . $ordersMA5Recovery[$i] . ", " . $ordersMA7Recovery[$i] . "],";
}
?>
]);

            var options = {
                title: 'Количество прослушиваний по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                trendlines: {
      0: {
        type: 'linear',
        visibleInLegend: true,
      }
    },
            series: {
                1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] } // Для линии скользящего среднего
        }
                
            };

            

            var chart = new google.visualization.LineChart(document.getElementById('ordersChart2'));
            chart.draw(data, options);
        }

// Функция для построения графика прослушиваний c прогнозными значениями
google.charts.setOnLoadCallback(drawOrdersChart3);
        function drawOrdersChart3() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Количество прослушиваний');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php

for ($i = 0; $i < count($monthsFuture); $i++) {
echo "['" . $monthsFuture[$i] . "', " . $orderCountsFuture[$i] . ", " . $ordersMA3Future[$i] . ", " . $ordersMA5Future[$i] . ", " . $ordersMA7Future[$i] . "],";
}
?>
]);

            var options = {
                title: 'Количество прослушиваний по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                trendlines: {
      0: {
        type: 'linear',
        visibleInLegend: true,
      }
    },
            series: {
                1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] } // Для линии скользящего среднего
        }
                
            };

            

            var chart = new google.visualization.LineChart(document.getElementById('ordersChart3'));
            chart.draw(data, options);
        }

        google.charts.setOnLoadCallback(drawOrdersChart4);
        function drawOrdersChart4() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Количество прослушиваний yt');
            data.addColumn('number', 'Линейный тренд');
data.addColumn('number', 'Параболический тренд');
data.addColumn('number', 'Показательный тренд');
data.addRows([
<?php
$ncount=range(1,13);
for ($i = 0; $i < count($months); $i++) {
    $line=$a0_orders+$a1_orders*($i-5);
    $parabol=$a0_orders_+$a1_orders*($i-5)+$a2_orders*($i-5)*($i-5);
    $exp=$a_orders*pow($b_orders,($i-5));
echo "['" . $ncount[$i] . "', " . $orderCounts[$i] . ", " . $line . ", " . $parabol . ", " . $exp . "],";
}
?>
]);

            var options = {
                title: 'Фактические и расчетные уровни ряда динамики для количества прослушиваний',
                curveType: 'function',
                legend: { position: 'bottom' },
                trendlines: {
      0: {
        type: 'linear',
        visibleInLegend: true,
      }
    },
            series: {
                1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] },// Для линии скользящего среднего
4: { lineDashStyle: [4, 2, 3, 2] } 
  
        }
                
            };

            

            var chart = new google.visualization.LineChart(document.getElementById('ordersChart4'));
            chart.draw(data, options);
        }

        // Построение графика суммы за товары
        google.charts.setOnLoadCallback(drawAmountChart);
        function drawAmountChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Прослушиваний для инсполнителя "The Beatles"');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php
for ($i = 0; $i < count($months); $i++) {
echo "['" . $months[$i] . "', " . $amounts[$i] . ", " . $amountsMA3[$i] . ", " . $amountsMA5[$i] . ", " . $amountsMA7[$i] . "],";
}
?>
]);

            var options = {
                title: 'Прослушиваний для инсполнителя "The Beatles" по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] } // Для линии скользящего среднего
}
            };

            var chart = new google.visualization.LineChart(document.getElementById('amountChart'));
            chart.draw(data, options);
        }

// Построение графика суммы за товары c краевыми значенями
google.charts.setOnLoadCallback(drawAmountChart2);
        function drawAmountChart2() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Прослушиваний для инсполнителя "The Beatles"');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php
for ($i = 0; $i < count($months); $i++) {
echo "['" . $months[$i] . "', " . $amounts[$i] . ", " . $amountsMA3Recovery[$i] . ", " . $amountsMA5Recovery[$i] . ", " . $amountsMA7Recovery[$i] . "],";
}
?>
]);

            var options = {
                title: 'Прослушиваний для инсполнителя "The Beatles" по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] } // Для линии скользящего среднего
}
            };

            var chart = new google.visualization.LineChart(document.getElementById('amountChart2'));
            chart.draw(data, options);
        }


// Построение графика суммы за товары c прогнозными значениями
google.charts.setOnLoadCallback(drawAmountChart3);
        function drawAmountChart3() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Прослушиваний для инсполнителя "The Beatles"');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php
for ($i = 0; $i < count($monthsFuture); $i++) {
echo "['" . $monthsFuture[$i] . "', " . $amountsFuture[$i] . ", " . $amountsMA3Future[$i] . ", " . $amountsMA5Future[$i] . ", " . $amountsMA7Future[$i] . "],";
}
?>
]);

            var options = {
                title: 'Прослушиваний для инсполнителя "The Beatles" по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] } // Для линии скользящего среднего
}
            };

            var chart = new google.visualization.LineChart(document.getElementById('amountChart3'));
            chart.draw(data, options);
        }

        google.charts.setOnLoadCallback(drawAmountChart4);
        function drawAmountChart4() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Прослушиваний для инсполнителя "The Beatles" yt');
            data.addColumn('number', 'Линейный тренд');
data.addColumn('number', 'Параболический тренд');
data.addColumn('number', 'Показательный тренд');
data.addRows([
<?php
$ncount=range(1,13);
for ($i = 0; $i < count($months); $i++) {
    $line=$a0_amounts+$a1_amounts*($i-5);
    $parabol=$a0_amounts_+$a1_amounts*($i-5)+$a2_amounts*($i-5)*($i-5);
    $exp=$a_amounts*pow($b_amounts,($i-5));
echo "['" . $ncount[$i] . "', " . $amounts[$i] . ", " . $line . ", " . $parabol . ", " . $exp . "],";
}
?>
]);

            var options = {
                title: 'Фактические и расчетные уровни ряда динамики прослушиваний для инсполнителя "The Beatles"',
                curveType: 'function',
                legend: { position: 'bottom' },
                trendlines: {
      0: {
        type: 'linear',
        visibleInLegend: true,
      }
    },
            series: {
                1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] },// Для линии скользящего среднего
4: { lineDashStyle: [4, 2, 3, 2] } 
  
        }
                
            };

            

            var chart = new google.visualization.LineChart(document.getElementById('amountChart4'));
            chart.draw(data, options);
        }
        // Построение графика суммы за доставку
        google.charts.setOnLoadCallback(drawDeliveryChart);
        function drawDeliveryChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Прослушивания для жанра "Хип-хоп"');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php
for ($i = 0; $i < count($months); $i++) {
echo "['" . $months[$i] . "', " . $deliveryAmounts[$i] . ", " . $deliveryAmountsMA3[$i] . ", " . $deliveryAmountsMA5[$i] . ", " . $deliveryAmountsMA7[$i] . "],";
}
?>
]);

            var options = {
                title: 'Прослушивания для жанра "Хип-хоп" по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
                    1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] } // Для линии скользящего среднего
}
            };

            var chart = new google.visualization.LineChart(document.getElementById('deliveryChart'));
            chart.draw(data, options);
        } 

         // Построение графика суммы за доставку с краевыми значениями
         google.charts.setOnLoadCallback(drawDeliveryChart2);
        function drawDeliveryChart2() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Прослушивания для жанра "Хип-хоп"');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php
for ($i = 0; $i < count($months); $i++) {
echo "['" . $months[$i] . "', " . $deliveryAmounts[$i] . ", " . $deliveryAmountsMA3Recovery[$i] . ", " . $deliveryAmountsMA5Recovery[$i] . ", " . $deliveryAmountsMA7Recovery[$i] . "],";
}
?>
]);

            var options = {
                title: 'Прослушивания для жанра "Хип-хоп" по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
                    1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] } // Для линии скользящего среднего
}
            };

            var chart = new google.visualization.LineChart(document.getElementById('deliveryChart2'));
            chart.draw(data, options);
        } 

        // Построение графика суммы за доставку с прогнозными значениями
        google.charts.setOnLoadCallback(drawDeliveryChart3);
        function drawDeliveryChart3() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Прослушивания для жанра "Хип-хоп"');
            data.addColumn('number', 'l=3');
data.addColumn('number', 'l=5');
data.addColumn('number', 'l=7');
data.addRows([
<?php
for ($i = 0; $i < count($monthsFuture); $i++) {
echo "['" . $monthsFuture[$i] . "', " . $deliveryAmountsFuture[$i] . ", " . $deliveryAmountsMA3Future[$i] . ", " . $deliveryAmountsMA5Future[$i] . ", " . $deliveryAmountsMA7Future[$i] . "],";
}
?>
]);

            var options = {
                title: 'Прослушивания для жанра "Хип-хоп" по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
                    1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] } // Для линии скользящего среднего
}
            };

            var chart = new google.visualization.LineChart(document.getElementById('deliveryChart3'));
            chart.draw(data, options);
        } 
        google.charts.setOnLoadCallback(drawDeliveryChart4);
        function drawDeliveryChart4() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Прослушивания для жанра "Хип-хоп" yt');
            data.addColumn('number', 'Линейный тренд');
data.addColumn('number', 'Параболический тренд');
data.addColumn('number', 'Показательный тренд');
data.addRows([
<?php
$ncount=range(1,13);
for ($i = 0; $i < count($months); $i++) {
    $line=$a0_delivery+$a1_delivery*($i-5);
    $parabol=$a0_delivery_+$a1_delivery*($i-5)+$a2_delivery*($i-5)*($i-5);
    $exp=$a_delivery*pow($b_delivery,($i-5));
echo "['" . $ncount[$i] . "', " . $deliveryAmounts[$i] . ", " . $line . ", " . $parabol . ", " . $exp . "],";
}
?>
]);

            var options = {
                title: 'Фактические и расчетные уровни ряда динамики для прослушиваний для жанра "Хип-хоп"',
                curveType: 'function',
                legend: { position: 'bottom' },
                trendlines: {
      0: {
        type: 'linear',
        visibleInLegend: true,
      }
    },
            series: {
                1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего
3: { lineDashStyle: [14, 2, 7, 2] },// Для линии скользящего среднего
4: { lineDashStyle: [4, 2, 3, 2] } 
  
        }
                
            };

            

            var chart = new google.visualization.LineChart(document.getElementById('deliveryChart4'));
            chart.draw(data, options);
        }
    </script>


</div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
