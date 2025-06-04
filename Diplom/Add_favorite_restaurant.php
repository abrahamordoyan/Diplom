<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: Login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_restaurant'])) {
    $user_id = $_SESSION['user_id'];
    $id_restaurant = (int)$_POST['id_restaurant'];

    $mysqli = new mysqli("localhost", "root", "", "diplom");
    if ($mysqli->connect_error) {
        die("Ошибка подключения: " . $mysqli->connect_error);
    }

    // Проверяем, есть ли уже в избранном
    $check = $mysqli->prepare("SELECT id_favorite FROM favorites_restaurant WHERE id_user = ? AND id_restaurant = ?");
    $check->bind_param("ii", $user_id, $id_restaurant);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $stmt = $mysqli->prepare("INSERT INTO favorites_restaurant (id_user, id_restaurant) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $id_restaurant);
        $stmt->execute();
    }

    $mysqli->close();
    header("Location: Restaurants.php");
    exit();
}
?>
