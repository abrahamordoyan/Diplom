<?php
session_start();

// Удаляем все данные сессии
$_SESSION = [];

// Уничтожаем сессию на сервере
session_destroy();

// Перенаправляем пользователя на страницу входа
header("Location: Login.php");
exit();
