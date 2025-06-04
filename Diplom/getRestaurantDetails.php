<?php
// Подключение к базе данных
$mysqli = new mysqli("localhost", "root", "", "diplom"); // Замените данные для подключения

// Проверка на ошибки соединения
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Получаем ID ресторана из параметров запроса
$id_restaurant = $_GET['id'];

// Запрос к базе данных для получения информации о ресторане
$result = $mysqli->query("SELECT * FROM restaurants WHERE id_restaurant = $id_restaurant");

if ($result) {
    $restaurant = $result->fetch_assoc();
    echo json_encode($restaurant);
} else {
    echo json_encode(['error' => 'Restaurant not found']);
}

$mysqli->close();
?>
