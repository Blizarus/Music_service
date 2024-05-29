<?php
function calculate_median($massive) {
    // Сортируем массив
    sort($massive);
    
    // Количество элементов в массиве
    $n = count($massive);
    
    // Проверяем, четное или нечетное количество элементов
    if ($n % 2 == 0) {
        // Количество элементов четное
        $m = $n / 2;
        $median = ($massive[$m - 1] + $massive[$m]) / 2;
    } else {
        // Количество элементов нечетное
        $m = ($n - 1) / 2;
        $median = $massive[$m];
    }
    
    return $median;
}

function analyze_series($massive) {
    $median = calculate_median($massive);
    
    // Создаем массив с '+' и '-'
    $series_array = array();
    foreach ($massive as $mass) {
        if ($mass > $median) {
            $series_array[] = '+';
        } elseif ($mass < $median) {
            $series_array[] = '-';
        }
    }

    // Подсчет числа серий (v) и длины самой длинной серии (t)
    $v = 0;
    $t = 0;
    $current_series_length = 0;
    $current_series_value = null;

    foreach ($series_array as $value) {
        if ($value === $current_series_value) {
            $current_series_length++;
        } else {
            if ($current_series_length > 0) {
                $v++;
                if ($current_series_length > $t) {
                    $t = $current_series_length;
                }
            }
            $current_series_value = $value;
            $current_series_length = 1;
        }
    }
    
    // Проверка последней серии
    if ($current_series_length > 0) {
        $v++;
        if ($current_series_length > $t) {
            $t = $current_series_length;
        }
    }

    return array($v, $t, $series_array,$median);
}

function analyze_differences($massive) {
    // Создаем массив с '+' и '-'
    $series_array = array();
    $n = count($massive);

    for ($i = 0; $i < $n - 1; $i++) {
        $difference = $massive[$i + 1] - $massive[$i];
         
        if ($difference > 0) {
            $series_array[] = '+';
        } elseif ($difference < 0) {
            $series_array[] = '-';
        }
    }

    // Подсчет числа серий (v) и длины самой длинной серии (t)
    $v = 0;
    $t = 0;
    $current_series_length = 0;
    $current_series_value = null;

    foreach ($series_array as $value) {
     
        if ($value === $current_series_value) {
            $current_series_length++;
        } else {
            if ($current_series_length > 0) {
                $v++;
                if ($current_series_length > $t) {
                    $t = $current_series_length;
                    
                    
                }
            }
            $current_series_value = $value;
            $current_series_length = 1;
        }
    }

    // Проверка последней серии
    if ($current_series_length > 0) {
        $v++;
        if ($current_series_length > $t) {
            $t = $current_series_length;
        }
    }

    return array($v, $t, $series_array);
}

function check_inequalities_rise($v, $t, $n) {
    // Вычисление значений для проверки неравенств
    $inequality1 = $v > floor((1/3) * (2 * $n - 1) - 1.96 * sqrt((16 * $n - 29) / 90));
    $inequality2 = false; // Инициализация переменной, чтобы избежать неопределенности

    if ( $n <= 26)
    {
        $inequality2 = $t < 5;
    }
    if ($n>26)
    { 
        if($n<=156)
        {
            $inequality2 = $t < 6;
        }
    }
    if (($n>153) && ($n<=1170))
    {
        $inequality2 = $t < 7;
    }

    return array($inequality1, $inequality2);
}

function check_inequalities_median($v, $t, $n) {
    // Вычисление значений для проверки неравенств
    $inequality1 = $v > floor(0.5 * ($n + 1 - 1.96 * sqrt($n - 1)));
    $inequality2 = $t < floor(1.43 * log($n + 1));

    return array($inequality1, $inequality2);
}


function getNextMonth($currentMonth) {
    // Разбиваем текущий месяц на год и месяц
    list($year, $month) = explode('-', $currentMonth);
    
    // Преобразуем месяц к числу
    $month = intval($month);
    
    // Если текущий месяц - декабрь, то следующий месяц будет январь следующего года
    if ($month == 12) {
    $year++;
    $month = 1;
    } else {
    // Иначе следующий месяц будет на один больше текущего
    $month++;
    }
    
    // Возвращаем следующий месяц в формате "год/месяц"
    return "$year-$month";
}
function movingAverage($data, $p) {
    $result = array();
    $count = count($data);
    for ($t = 0; $t < $count; $t++) {
        if ($t<$p||$t>$count-$p-1)
        {
            $result[]="null";
             
        } else{
    $start = max(0, $t - $p);
    $end = min($count - 1, $t + $p);
    $sum = array_sum(array_slice($data, $start, $end - $start + 1));
    $result[] = $sum / ($end - $start + 1);
   
    }}
    return $result;
    }  
    function weightedMovingAverage($data, $weights) {
        $result = array();
        $count = count($data);
        $windowSize = count($weights);
        for ($t = 0; $t < $count; $t++) {
        if ($t < floor($windowSize / 2) || $t > $count - floor($windowSize / 2) - 1) {
        // Если это крайние значения, добавляем null в результат
        $result[] = "null";
        } else {
        $start = max(0, $t - floor($windowSize / 2));
        $end = min($count - 1, $t + floor($windowSize / 2));
        $sum = 0;
        for ($i = $start; $i <= $end; $i++) {
        $sum += $data[$i] * $weights[$i - $start];
        }
        $result[] = $sum / array_sum($weights);
        }
        }
        return $result;
        }

        function linearRegression($x, $y) {
            $n = count($x);
            $sumX = array_sum($x);
            $sumY = array_sum($y);
            $sumX2 = 0;
            $sumXY = 0;
            
            for ($i = 0; $i < $n; $i++) {
            $sumX2 += $x[$i] * $x[$i];
            $sumXY += $x[$i] * $y[$i];
            }
            
            $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
            $intercept = ($sumY - $slope * $sumX) / $n;
            
            return array($slope, $intercept);
            }
            
            function linearRegressionPredict($x, $slope, $intercept) {
            return $slope * $x + $intercept;
            }
            
            function movingAverageRecovery($data, $p) {
            $result = array();
            $count = count($data);
            
            // Аппроксимация краевых значений с помощью линейной регрессии
            $polyDegree = $p+2 ;
            $firstValues = array_slice($data, 0, $polyDegree);
            $lastValues = array_slice($data, -$polyDegree);
            
            list($firstSlope, $firstIntercept) = linearRegression(range(1, $polyDegree), $firstValues);
            list($lastSlope, $lastIntercept) = linearRegression(range(1, $polyDegree), $lastValues);
            
            // Заполнение массива результатов
            for ($t = 0; $t < $count; $t++) {
            if ($t < $p || $t > $count - $p - 1) {
            // Если это краевые значения, используем аппроксимацию
            if ($t < $p) {
            $result[] = linearRegressionPredict($t + 1, $firstSlope, $firstIntercept);
            } else {
            $result[] = linearRegressionPredict($count - $t, $lastSlope, $lastIntercept);
            }
            } else {
            $start = max(0, $t - $p);
            $end = min($count - 1, $t + $p);
            $sum = array_sum(array_slice($data, $start, $end - $start + 1));
            $result[] = $sum / ($end - $start + 1);
            }
            }
            return $result;
            }

            function weightedMovingAverageRecovery($data, $weights) {
                $result = array();
                $count = count($data);
                $windowSize = count($weights);
                
                // Аппроксимация краевых значений с помощью линейной регрессии
                $polyDegree = floor($windowSize/2)+2;
                $firstValues = array_slice($data, 0, $polyDegree);
                $lastValues = array_slice($data, -$polyDegree);
                
                list($firstSlope, $firstIntercept) = linearRegression(range(1, $polyDegree), $firstValues);
                list($lastSlope, $lastIntercept) = linearRegression(range(1, $polyDegree), $lastValues);
                
                // Заполнение массива результатов
                for ($t = 0; $t < $count; $t++) {
                if ($t < floor($windowSize / 2) || $t > $count - floor($windowSize / 2) - 1) {
                // Если это краевые значения, используем аппроксимацию
                if ($t < floor($windowSize / 2)) {
                $result[] = linearRegressionPredict($t + 1, $firstSlope, $firstIntercept);
                } else {
                $result[] = linearRegressionPredict($count - $t, $lastSlope, $lastIntercept);
                }
                } else {
                $start = max(0, $t - floor($windowSize / 2));
                $end = min($count - 1, $t + floor($windowSize / 2));
                $sum = 0;
                for ($i = $start; $i <= $end; $i++) {
                $sum += $data[$i] * $weights[$i - $start];
                }
                $result[] = $sum / array_sum($weights);
                }
                }
                return $result;
                }
                 

                function movingAverageFuture($data, $p) {
                    $result = array();
                    $count = count($data);
                    
                    $polyDegree = $p+2;
            $firstValues = array_slice($data, 0, $polyDegree);
            $lastValues = array_slice($data, -$polyDegree);
            
            list($firstSlope, $firstIntercept) = linearRegression(range(1, $polyDegree), $firstValues);
            list($lastSlope, $lastIntercept) = linearRegression(range(1, $polyDegree), $lastValues);
            
            // Заполнение массива результатов
            for ($t = 0; $t < $count; $t++) {
            if ($t < $p || $t > $count - $p - 1) {
            // Если это краевые значения, используем аппроксимацию
            if ($t < $p) {
            $result[] = linearRegressionPredict($t + 1, $firstSlope, $firstIntercept);
             
            } else {
            $result[] = linearRegressionPredict($count - $t, $lastSlope, $lastIntercept);
           
            }
            } else {
            $start = max(0, $t - $p);
            $end = min($count - 1, $t + $p);
            $sum = array_sum(array_slice($data, $start, $end - $start + 1));
            $result[] = $sum / ($end - $start + 1);
          
            }
            }

            
                    // Прогнозирование значений на два месяца вперед
                    $beforeLastValue = $result[$count-3]; // Предпоследнее значение в массиве result
                    $lastDataPoint = $data[$count-1]; // Последнее значение в массиве data
                    $beforeLastDataPoint = $data[$count-2]; // Предпоследнее значение в массиве data
                     
                    
                    
                     $mtMinusOne = $beforeLastValue + (1 / (2*$p+1)) * ($lastDataPoint - $beforeLastDataPoint); // Прогнозируемый показатель на следующий месяц
                     
                     $result[$count] = $mtMinusOne; // Прогнозируемый показатель на следующий месяц
                     $beforeLastValue2 = $result[$count-2];
                     
                     $mtMinusTwo = $beforeLastValue2 + (1 / (2*$p+1)) * ($lastDataPoint - $beforeLastDataPoint);
                     $result[$count+1] = $mtMinusTwo;
                    
                    return $result;
                    }

                    function weightedMovingAverageFuture($data, $weights) {
                        $result = array();
                        $count = count($data);
                        $windowSize = count($weights);
                        
                        // Аппроксимация краевых значений с помощью линейной регрессии
                        $polyDegree = floor($windowSize/2)+1;
                        $firstValues = array_slice($data, 0, $polyDegree);
                        $lastValues = array_slice($data, -$polyDegree);
                        
                        list($firstSlope, $firstIntercept) = linearRegression(range(1, $polyDegree), $firstValues);
                        list($lastSlope, $lastIntercept) = linearRegression(range(1, $polyDegree), $lastValues);
                        
                        // Заполнение массива результатов
                        for ($t = 0; $t < $count; $t++) {
                        if ($t < floor($windowSize / 2) || $t > $count - floor($windowSize / 2) - 1) {
                        // Если это краевые значения, используем аппроксимацию
                        if ($t < floor($windowSize / 2)) {
                        $result[] = linearRegressionPredict($t + 1, $firstSlope, $firstIntercept);
                        } else {
                        $result[] = linearRegressionPredict($count - $t, $lastSlope, $lastIntercept);
                        }
                        } else {
                        $start = max(0, $t - floor($windowSize / 2));
                        $end = min($count - 1, $t + floor($windowSize / 2));
                        $sum = 0;
                        for ($i = $start; $i <= $end; $i++) {
                        $sum += $data[$i] * $weights[$i - $start];
                        }
                        $result[] = $sum / array_sum($weights);
                        }
                        }
                         // Прогнозирование значений на два месяца вперед
                    $beforeLastValue = $result[$count-$windowSize]; // Предпоследнее значение в массиве result
                    $lastDataPoint = $data[$count-1]; // Последнее значение в массиве data
                    $beforeLastDataPoint = $data[$count-2]; // Предпоследнее значение в массиве data
                     

                     $mtMinusOne = $beforeLastValue + (1 / $windowSize) * ($lastDataPoint - $beforeLastDataPoint); // Прогнозируемый показатель на следующий месяц

                     $result[$count] = $mtMinusOne; // Прогнозируемый показатель на следующий месяц
                     $beforeLastValue2 = $result[$count - $windowSize+1];
                     $mtMinusTwo = $beforeLastValue2 + (1 / $windowSize) * ($lastDataPoint - $beforeLastDataPoint);
                     $result[$count+1] = $mtMinusTwo;
                    
                        return $result;
                        }
                    
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
?>