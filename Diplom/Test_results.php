<?php
// test_results.php

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['tags'])) {
    echo json_encode(['error' => 'Нет данных для обработки']);
    exit;
}

$tags = $data['tags'];

// Подключение к базе
$mysqli = new mysqli("localhost", "root", "", "diplom");
$mysqli->set_charset("utf8");

if ($mysqli->connect_errno) {
    echo json_encode(['error' => 'Ошибка соединения с БД']);
    exit;
}

// Подготовка запросов к маршрутам и ресторанам
// Здесь делай фильтрацию по тегам (пример с LIKE или IN) — адаптируй под свою логику

// Пример простой выборки маршрутов с учетом тегов
// Для более сложной фильтрации надо делать полноценные запросы с JOIN или WHERE

// Пример: получим все маршруты, у которых в extra_text или name встречается любой из тегов
$tagsForSql = array_map(function($t) use ($mysqli) {
    return $mysqli->real_escape_string($t);
}, $tags);

$likeParts = array_map(function($tag) {
    return "(name LIKE '%$tag%' OR extra_text LIKE '%$tag%')";
}, $tagsForSql);

$whereRoutes = implode(' OR ', $likeParts);

$queryRoutes = "SELECT id_route, name, url, description FROM routes WHERE $whereRoutes";
$resultRoutes = $mysqli->query($queryRoutes);
$routes = [];
if ($resultRoutes) {
    while ($row = $resultRoutes->fetch_assoc()) {
        $routes[] = $row;
    }
}

// Аналогично для ресторанов — фильтруем по тегам в name, cuisine или description
$likePartsRest = array_map(function($tag) {
    return "(name LIKE '%$tag%' OR cuisine LIKE '%$tag%' OR description LIKE '%$tag%')";
}, $tagsForSql);

$whereRestaurants = implode(' OR ', $likePartsRest);

$queryRestaurants = "SELECT id_restaurant, name, cuisine, description, image_restaurant FROM restaurants WHERE $whereRestaurants";
$resultRestaurants = $mysqli->query($queryRestaurants);
$restaurants = [];
if ($resultRestaurants) {
    while ($row = $resultRestaurants->fetch_assoc()) {
        $restaurants[] = $row;
    }
}

echo json_encode([
    'routes' => $routes,
    'restaurants' => $restaurants
]);
$mysqli->close();
