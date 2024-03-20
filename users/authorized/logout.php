<?php
session_start();

// Удаляем все данные сессии
session_unset();
session_destroy();

// Перенаправляем пользователя на предыдущую страницу (HTTP_REFERER - встроенная переменная, содержащая URL предыдущей страницы)
header("Location: /general_page.php");
exit();
?>