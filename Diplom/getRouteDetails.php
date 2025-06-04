<?php
$mysqli = new mysqli("localhost", "root", "", "diplom");

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Ошибка соединения с БД"]);
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $result = $mysqli->query("SELECT * FROM routes WHERE id_route = $id");
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Маршрут не найден"]);
    }
} else {
    echo json_encode(["error" => "Некорректный ID"]);
}

$mysqli->close();
?>
