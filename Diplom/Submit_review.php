<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "diplom");
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$restaurant_id = isset($_POST['restaurant_id']) ? (int)$_POST['restaurant_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$review = isset($_POST['review']) ? trim($_POST['review']) : '';

if ($restaurant_id <= 0 || $rating <= 0 || $rating > 5 || $review === '') {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля корректно']);
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO reviews_restaurants (id_user, id_restaurant, review, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iisi", $user_id, $restaurant_id, $review, $rating);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении отзыва']);
}

$stmt->close();
$mysqli->close();
