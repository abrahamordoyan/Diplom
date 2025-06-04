<?php
session_start();

// Подключение к базе данных
$mysqli = new mysqli("localhost", "root", "", "diplom");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['delete_id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
    $delete_id = (int)$_GET['delete_id'];
    $mysqli->query("DELETE FROM restaurants WHERE id_restaurant = $delete_id");
    header("Location: Restaurants.php");
    exit;
}

// Обработка POST-запроса для добавления/редактирования ресторана
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
    // Получаем данные из формы и экранируем для безопасности
    $id_restaurant = isset($_POST['id_restaurant']) ? (int)$_POST['id_restaurant'] : 0;
    $name = $mysqli->real_escape_string($_POST['name']);
    $cuisine = $mysqli->real_escape_string($_POST['cuisine']);
    $description = $mysqli->real_escape_string($_POST['description']);
    $image_restaurant = $mysqli->real_escape_string($_POST['image_restaurant']);
    $types_of_dishes = $mysqli->real_escape_string($_POST['types_of_dishes']);
    $specialized_menu = $mysqli->real_escape_string($_POST['specialized_menu']);
    $image_food = $mysqli->real_escape_string($_POST['image_food']);
    $adress = $mysqli->real_escape_string($_POST['adress']);
    $working_hours = $mysqli->real_escape_string($_POST['working_hours']);
    $telephone = $mysqli->real_escape_string($_POST['telephone']);
    $url = $mysqli->real_escape_string($_POST['url']);
    $tags = $mysqli->real_escape_string($_POST['tags']);


    if ($id_restaurant > 0) {
        // Обновление существующего ресторана
        $query = "UPDATE restaurants SET 
            name='$name', 
            cuisine='$cuisine', 
            description='$description', 
            image_restaurant='$image_restaurant', 
            types_of_dishes='$types_of_dishes', 
            specialized_menu='$specialized_menu',
            image_food='$image_food', 
            adress='$adress', 
            working_hours='$working_hours', 
            telephone='$telephone', 
            url='$url',
            tags='$tags'
            WHERE id_restaurant=$id_restaurant";
    } else {
        // Добавление нового ресторана
        $query = "INSERT INTO restaurants 
            (name, cuisine, description, image_restaurant, types_of_dishes, specialized_menu, image_food, adress, working_hours, telephone, url, tags)
            VALUES
            ('$name', '$cuisine', '$description', '$image_restaurant', '$types_of_dishes', '$specialized_menu', '$image_food', '$adress', '$working_hours', '$telephone', '$url', '$tags')";
    }

    if ($mysqli->query($query)) {
        // Успешно добавлено/обновлено — редирект на страницу ресторанов
        header("Location: Restaurants.php");
        exit;
    } else {
        // Ошибка запроса
        echo "Ошибка базы данных: " . $mysqli->error;
        exit;
    }
}

// Ниже идет остальной код страницы, который выводит форму, список ресторанов и модальные окна...
