<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Пользователь не авторизован']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Неверный метод запроса']);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "diplom");
if ($mysqli->connect_errno) {
    echo json_encode(['error' => 'Ошибка подключения к базе данных']);
    exit;
}
$mysqli->set_charset("utf8");

$user_id = (int)$_SESSION['user_id'];

$query = "
    SELECT r.review, r.rating, r.created_at, res.name as restaurant_name
    FROM reviews_restaurants r
    JOIN restaurants res ON r.id_restaurant = res.id_restaurant
    WHERE r.id_user = ?
    ORDER BY r.created_at DESC
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode(['reviews' => $reviews]);
?>
