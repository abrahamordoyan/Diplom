<?php
session_start();

// Проверка авторизации пользователя (роль 2 — обычный пользователь)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: Login.php");
    exit();
}

$name = $_SESSION['name'] ?? '';
$surname = $_SESSION['surname'] ?? '';

// Подключение к БД
$mysqli = new mysqli("localhost", "root", "", "diplom");
$mysqli->set_charset("utf8");

$user_id = $_SESSION['user_id'] ?? null;

// Обработка удаления ресторана из избранного
if (isset($_GET['remove_favorite'])) {
    $restaurant_id = (int)$_GET['remove_favorite'];
    if ($user_id && $restaurant_id) {
        $stmtDel = $mysqli->prepare("DELETE FROM favorites_restaurant WHERE id_user = ? AND id_restaurant = ?");
        $stmtDel->bind_param("ii", $user_id, $restaurant_id);
        $stmtDel->execute();
        $stmtDel->close();
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }
}

// Обработка удаления маршрута из избранного
if (isset($_GET['remove_favorite_route'])) {
    $route_id = (int)$_GET['remove_favorite_route'];
    if ($user_id && $route_id) {
        $stmtDelRoute = $mysqli->prepare("DELETE FROM favorites_routes WHERE id_user = ? AND id_route = ?");
        $stmtDelRoute->bind_param("ii", $user_id, $route_id);
        $stmtDelRoute->execute();
        $stmtDelRoute->close();
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }
}

// Обработка удаления отеля из избранного
if (isset($_GET['remove_favorite_hotel'])) {
    $hotel_index = (int)$_GET['remove_favorite_hotel'];
    if ($user_id) {
        $stmtDelHotel = $mysqli->prepare("DELETE FROM favorites_hotels WHERE id_user = ? AND hotel_index = ?");
        $stmtDelHotel->bind_param("ii", $user_id, $hotel_index);
        $stmtDelHotel->execute();
        $stmtDelHotel->close();
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }
}

// Получение избранных отелей из БД
$favHotelsIndexes = [];
if ($user_id) {
    $stmtFavHotels = $mysqli->prepare("SELECT hotel_index FROM favorites_hotels WHERE id_user = ?");
    $stmtFavHotels->bind_param("i", $user_id);
    $stmtFavHotels->execute();
    $resultFavHotels = $stmtFavHotels->get_result();
    while ($row = $resultFavHotels->fetch_assoc()) {
        $favHotelsIndexes[] = $row['hotel_index'];
    }
    $stmtFavHotels->close();
}

// Загрузка данных всех отелей из JSON
$jsonFile = 'hotels.json';
$hotels = [];
if (file_exists($jsonFile)) {
    $json = file_get_contents($jsonFile);
    $hotels = json_decode($json, true) ?: [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Личный кабинет</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    /* Общие стили страницы */
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      color: #222;
    }

    body > main {
      max-width: 1100px;
      margin: 20px auto;
      padding: 0 10px;
      position: relative;
      min-height: 80vh;
    }

    h1 {
      position: relative;
      font-weight: 700;
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
    }

    h1 span.welcome-text {
      display: inline-block;
      line-height: 1;
      padding-top: 8px;
    }

    .logout-btn-header {
      background-color: #2f3433;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
      white-space: nowrap;
    }
    .logout-btn-header:hover {
      background-color: #000000;
    }

    .tabs {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .tab-btn {
  background-color: white;
  border: 2px solid #000000;
  color: #000000;
  padding: 10px 20px;
  font-weight: 600;
  border-radius: 30px;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.tab-btn.active,
.tab-btn:hover {
  background-color: #000000; /* изменён цвет активной вкладки */
  color: white;
}


    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    .favorites-section h2 {
      text-align: center;
      margin: 40px 0 20px 0;
      font-weight: 700;
    }

    /* Карточки ресторанов — вернул твои стили и разметку */
    .cards-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .restaurant-card {
      position: relative;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      border: 1px solid #ddd;
      overflow: hidden;
      width: calc(33.333% - 13.33px);
      cursor: pointer;
      display: flex;
      flex-direction: column;
      min-height: 400px;
      margin-bottom: 20px;
    }

    .restaurant-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .restaurant-info {
      padding: 10px;
      text-align: center;
      display: flex;
      flex-direction: column;
      gap: 10px;
      flex-grow: 1;
    }

    .restaurant-info .title {
      font-weight: bold;
      font-size: 20px;
      margin-bottom: 10px;
    }

    .restaurant-info .cuisine {
      font-size: 16px;
      margin-bottom: 10px;
    }

    .restaurant-info .description {
      font-size: 14px;
      color: #666;
      margin-bottom: auto;
    }

    /* Обновляем стиль кнопки "Подробнее" для ресторанов */
    .btn-green {
      background-color: white;
      color: black;
      padding: 10px 15px;
      text-decoration: none;
      text-align: center;
      border-radius: 5px;
      font-weight: bold;
      border: 2px solid transparent;
      transition: background-color 0.3s ease;
      cursor: pointer;
    }

    .btn-green:hover {
      background-color: #f0f0f0;
      color: black;
      border-color: black;
    }

    .restaurant-info a.btn-remove {
      background-color: #000000;
      margin-top: 10px;
      box-shadow: 0 2px 5px rgba(215, 76, 65, 0.4);
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px;
      border-radius: 5px;
      font-weight: 600;
      cursor: pointer;
      text-align: center;
      user-select: none;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .restaurant-info a.btn-remove:hover {
  background-color: #e30016 !important;
  box-shadow: 0 4px 10px rgba(176, 60, 55, 0.6) !important;
  border: none !important; /* <== добавь эту строку */
}

    .restaurant-info a.btn-green {
  background-color: white !important;
  color: black !important;
  border: 2px solid black !important;
  padding: 10px 15px;
  border-radius: 5px;
  font-weight: bold;
  text-decoration: none;
  display: inline-block;
  transition: background-color 0.3s ease;
  cursor: pointer;
}

.restaurant-info a.btn-green:hover {
  background-color: #f0f0f0 !important;
  color: black !important;
}



    /* Карточки маршрутов */
    .route-card {
      display: flex;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      margin-bottom: 40px;
      overflow: hidden;
      max-width: 1100px;
      margin-left: auto;
      margin-right: auto;
    }

    .route-map {
      flex: 1;
      min-width: 300px;
      height: 400px;
    }

    .route-map iframe {
      width: 100%;
      height: 100%;
      border: none;
      border-radius: 10px 0 0 10px;
    }

    .route-info {
      flex: 1;
      min-width: 300px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .route-title {
      font-size: 30px;
      margin-bottom: 15px;
      color: #333;
    }

    .route-description {
      color: #666;
      line-height: 1.6;
      margin-bottom: 20px;
      font-size: 20px;
    }

    .btn-group {
      display: flex;
      gap: 10px;
    }

    .btn-green {
      bottom: 10px;
  right: 10px;
  background-color: white; /* Белый фон */
  color: black; /* Черный цвет текста */
  padding: 10px 15px; /* Отступы сверху и снизу 10px, слева и справа 15px */
  text-decoration: none;
  text-align: center;
  border-radius: 5px; /* Закругленные углы */
  border: 2px solid black; /* Черная обводка */
  font-weight: bold; /* Жирный текст */
    }

    .btn-green:hover {
      background-color: #f0f0f0; /* Легкий серый фон при наведении */
  color: black; /* Текст остается черным */
  border-color: black; /* Обводка тоже остается черной */  
  }

    .btn-red {
      padding: 10px 20px;
      background-color: #000000;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: 600;
      box-shadow: 0 2px 5px rgba(215, 76, 65, 0.4);
      transition: background-color 0.3s;
    }

    .btn-red:hover {
      background-color: #e30016;
    }

    /* Стили карточек отелей — такие же как на странице отелей */
    .hotels-container {
      max-width: 1250px;
      margin: 40px auto;
      padding: 0 15px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .hotel-card {
      display: flex;
      align-items: stretch;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      overflow: hidden;
      min-height: 180px;
      cursor: pointer;
    }
    .hotel-card img {
      width: 300px;
      height: 100%;
      object-fit: cover;
    }
    .hotel-info {
      flex-grow: 1;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      border-right: 1px solid #eee;
    }
    .hotel-info .stars {
      font-size: 18px;
      color: gold;
      margin-bottom: 8px;
    }
    .hotel-info .title {
      font-weight: bold;
      font-size: 22px;
      margin-bottom: 5px;
    }
    .hotel-info .subtitle {
      font-size: 14px;
      color: #666;
    }
    .hotel-price {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      padding: 20px;
      min-width: 260px;
      max-width: 280px;
      box-sizing: border-box;
    }
    .hotel-price .price {
      font-size: 22px;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 5px;
      margin-bottom: 15px;
    }
    .hotel-price .price span.rub {
      font-size: 20px;
      position: relative;
      top: 1px;
    }
    .more-btn {
      width: 100%;
      bottom: 10px;
  right: 10px;
  background-color: white; /* Белый фон */
  color: black; /* Черный цвет текста */
  padding: 10px 15px; /* Отступы сверху и снизу 10px, слева и справа 15px */
  text-decoration: none;
  text-align: center;
  border-radius: 5px; /* Закругленные углы */
  border: 2px solid black; /* Черная обводка */
  font-weight: bold; /* Жирный текст */
    }
    .more-btn:hover {
      background-color: #f0f0f0; /* Легкий серый фон при наведении */
  color: black; /* Текст остается черным */
  border-color: black; /* Обводка тоже остается черной */ 
    }
    .btn-favorite {
      display: inline-block;
      padding: 12px 0;
      width: 100%;
      font-weight: 600;
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      user-select: none;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 16px;
      color: white;
      box-shadow: 0 2px 5px rgba(26, 62, 138, 0.4);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      text-decoration: none;
      margin-top: 10px;
    }
    .btn-favorite.add {
      background-color: #2f3433;
    }
    .btn-favorite.add:hover {
      background-color: #000000;
      box-shadow: 0 4px 10px rgba(18, 46, 109, 0.6);
    }
    .btn-favorite.remove {
      background-color: #000000;
    }
    .btn-favorite.remove:hover {
      background-color: #e30016;
      box-shadow: 0 4px 10px rgba(176, 60, 55, 0.6);
    }

    /* Адаптив */
    @media (max-width: 768px) {
      .restaurant-card, .hotel-card {
        width: 100%;
        margin-right: 0;
      }

      .route-card {
        flex-direction: column;
        max-width: 100%;
      }

      .route-map, .route-info {
        width: 100%;
        min-width: auto;
        height: auto;
      }

      .route-map iframe {
        border-radius: 10px 10px 0 0;
        height: 300px;
      }

      .hotel-card {
        flex-direction: column;
        height: auto;
      }
      .hotel-card img {
        width: 100%;
        height: 200px;
      }
      .hotel-info, .hotel-price {
        width: 100%;
        border: none;
        text-align: left;
        align-items: flex-start;
      }
      .hotel-price {
        align-items: flex-start;
      }
    }

    /* Модальное окно */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      width: 90%;
      max-width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.4);
      padding: 20px;
    }

    .modal-header {
      font-size: 24px;
      font-weight: bold;
      color: #333;
      text-align: center;
      margin-bottom: 10px;
    }

    .modal-body img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .modal-body p {
      font-size: 14px;
      line-height: 1.4;
      color: #555;
      margin: 5px 0;
    }

    .modal-body strong {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      margin-top: 15px;
    }

    .modal-footer button {
      background-color: #ff5733;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .modal-footer button:hover {
      background-color: #e04100;
    }

    .close {
      position: absolute;
      top: 10px;
      left: 15px;
      font-size: 28px;
      font-weight: bold;
      color: #aaa;
      cursor: pointer;
      background-color: transparent;
      border: none;
      padding: 0;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 5px;
      transition: color 0.3s ease, background-color 0.3s ease;
    }

    .close:hover,
    .close:focus {
      color: black;
      background-color: transparent;
      text-decoration: none;
      outline: none;
    }

    #testResults {
  margin-top: 20px;
  padding: 15px;
  background-color: #fff;
  border-radius: 10px;
  max-width: 1500px;
  margin-left: auto;
  margin-right: auto;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  font-family: Arial, sans-serif;
  color: #333;
}

#testResults h3 {
  font-size: 20px;
  margin-bottom: 10px;
  color: #000000 !important;
  font-weight: 700;
}

#testResults ul {
  list-style-type: disc;
  margin-left: 20px;
  margin-bottom: 20px;
}

#testResults ul li {
  margin-bottom: 6px;
  font-size: 16px;
}

.restaurant-info a, /* старый стиль */
#testResults .restaurant-card a.btn-green {
  background-color: white;
  color: black;
  border: 2px solid black;
  padding: 10px 15px;
  border-radius: 5px;
  font-weight: bold;
  text-decoration: none;
  display: inline-block;
  transition: background-color 0.3s ease;
}

#testResults .restaurant-card a.btn-green:hover {
  background-color: #f0f0f0;
  color: black;
}


.test-route-card {
  display: flex;
  background-color: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  border: 1px solid #ddd;
  overflow: hidden;
  width: calc(33.333% - 13.33px);
  flex-direction: column;
  cursor: pointer;
  margin-bottom: 20px;
  min-height: 350px;
  transition: box-shadow 0.3s ease;
}

.test-route-card:hover {
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
}

.test-route-card .route-map {
  width: 100%;
  height: 180px;
  border-radius: 15px 15px 0 0;
  overflow: hidden;
}

.test-route-card .route-map iframe {
  width: 100%;
  height: 250px; /* увеличить высоту */
  border: none;
}

.test-route-card .route-info {
  padding: 15px 10px 20px 10px;
  text-align: center;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.test-route-card .route-title {
  font-size: 18px;
  font-weight: 700;
  color: #000000;
  margin-bottom: 10px;
}

.test-route-card .route-description {
  font-size: 14px;
  color: #666;
  flex-grow: 1;
  margin-bottom: 15px;
}

.test-route-card .btn-green {
  padding: 10px 15px;
  background-color: #2f3433;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
  box-shadow: 0 2px 6px rgba(26,62,138,0.5);
  transition: background-color 0.3s ease;
  user-select: none;
}

.test-route-card .btn-green:hover {
  background-color: #000000;
}

/* Адаптив */
@media (max-width: 768px) {
  .test-route-card {
    width: 100%;
    flex-direction: column;
    min-height: auto;
  }

  .test-route-card .route-map {
    height: 220px;
    border-radius: 15px 15px 0 0;
  }
}

  #recommendationTest {
    padding: 20px 15px;
    max-width: 600px;
    margin: 20px auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
  }

  #recommendationTest div {
    margin-bottom: 10px; /* уменьшен отступ между вопросами */
  }

  #recommendationTest p {
    margin-bottom: 6px; /* уменьшен отступ между текстом вопроса и вариантами */
    font-weight: 600;
    color: #000000;
  }

  #recommendationTest label {
    display: block;
    margin-bottom: 6px; /* уменьшен отступ между вариантами */
    font-weight: 400;
    cursor: pointer;
  }

  #recommendationTest input[type="radio"],
  #recommendationTest input[type="checkbox"] {
    margin-right: 6px;
    vertical-align: middle;
  }

  #recommendationTest button[type="submit"] {
    margin-top: 15px;
    padding: 14px 0;
    width: 100%;
    font-size: 18px;
    background-color: #2f3433;
    color: white;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-weight: 700;
    transition: background-color 0.3s ease;
  }

  #recommendationTest button[type="submit"]:hover {
    background-color: #000000;
  }


  .required-star {
    color: red;
    font-weight: 700;
    margin-left: 5px;
  }

  #testResults .btn-green {
  background-color: white;
  color: black;
  padding: 10px 15px;
  text-decoration: none;
  text-align: center;
  border-radius: 5px;
  border: 2px solid black;
  font-weight: bold;
  display: inline-block;
  transition: background-color 0.3s ease;
}

#testResults .btn-green:hover {
  background-color: #f0f0f0;
  color: black;
  border-color: black;
}



  </style>
</head>
<body>

<header>
  <div class="logo">
    <a href="mainpage.php">
      <img src="images/mainpage/logo2.png" alt="Ярославль" />
    </a>
  </div>
  <div class="menu-toggle" id="menuToggle">☰</div>
  <div id="sideMenu" class="side-menu">
    <button id="closeMenu" class="close-btn">&times;</button>
    <ul>
      <li><a href="History.php">История города</a></li>
      <li><a href="Places.php">Достопримечательности</a></li>
      <li><a href="Restaurants.php">Рестораны</a></li>
      <li><a href="Hotels.php">Отели</a></li>
      <li><a href="Map.php">Карта города</a></li>
      <li><a href="Routes.php">Маршруты</a></li>
      <li><a href="Afisha.php">Афиша</a></li>
      <li style="margin-top: 20px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3);">
        <a href="logout.php">Выйти</a>
      </li>
    </ul>
  </div>
  <nav>
    <ul>
      <li><a href="History.php">История города</a></li>
      <li><a href="Places.php">Достопримечательности</a></li>
      <li><a href="Restaurants.php">Рестораны</a></li>
      <li><a href="Hotels.php">Отели</a></li>
      <li><a href="Map.php">Карта города</a></li>
      <li><a href="Routes.php">Маршруты</a></li>
      <li><a href="Afisha.php">Афиша</a></li>
    </ul>
  </nav>
  <div class="login-btn">
    <a href="user_dashboard.php">Личный кабинет</a>
  </div>
</header>

<main>
<h1>
  <span class="welcome-text">Добро пожаловать, <?= htmlspecialchars($name . ' ' . $surname) ?>!</span>
  <button class="logout-btn-header" onclick="location.href='logout.php'">Выйти из аккаунта</button>
</h1>

  <div class="tabs">
    <button class="tab-btn active" data-tab="tab-restaurants">Рестораны</button>
    <button class="tab-btn" data-tab="tab-routes">Маршруты</button>
    <button class="tab-btn" data-tab="tab-hotels">Отели</button>
    <button class="tab-btn" data-tab="tab-test">Тест</button>
  </div>

  <!-- Рестораны -->
  <section id="tab-restaurants" class="tab-content active favorites-section">
    <h2>Избранные рестораны</h2>
    <?php
    $query = "
      SELECT r.id_restaurant, r.name, r.image_restaurant, r.cuisine, r.description
      FROM favorites_restaurant f
      JOIN restaurants r ON f.id_restaurant = r.id_restaurant
      WHERE f.id_user = ?
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0):
      echo '<div class="cards-container">';
      while ($row = $result->fetch_assoc()):
    ?>
      <div class="restaurant-card">
        <img src="images/restaurants/<?= htmlspecialchars($row['image_restaurant']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
        <div class="restaurant-info">
          <p class="title"><?= htmlspecialchars($row['name']) ?></p>
          <p class="cuisine">Тип кухни: <?= htmlspecialchars($row['cuisine']) ?></p>
          <p class="description"><?= htmlspecialchars($row['description']) ?></p>
          <a href="javascript:void(0)" class="btn-green" onclick="openModal(<?= $row['id_restaurant'] ?>)">Подробнее</a>
          <a href="?remove_favorite=<?= $row['id_restaurant'] ?>" class="btn-remove" onclick="return confirm('Удалить ресторан из избранного?')">Удалить из избранного</a>
        </div>
      </div>
    <?php
      endwhile;
      echo '</div>';
    else:
      echo '<p>Вы ещё не добавили рестораны в избранное.</p>';
    endif;
    ?>
  </section>

  <!-- Маршруты -->
  <section id="tab-routes" class="tab-content favorites-section">
    <h2>Избранные маршруты</h2>
    <?php
    $queryRoutes = "
      SELECT r.id_route, r.name, r.url, r.description, r.extra_text
      FROM favorites_routes f
      JOIN routes r ON f.id_route = r.id_route
      WHERE f.id_user = ?
    ";
    $stmtRoutes = $mysqli->prepare($queryRoutes);
    $stmtRoutes->bind_param("i", $user_id);
    $stmtRoutes->execute();
    $resultRoutes = $stmtRoutes->get_result();

    if ($resultRoutes->num_rows > 0):
      while ($rowRoute = $resultRoutes->fetch_assoc()):
    ?>
      <div class="route-card">
        <div class="route-map">
          <iframe src="<?= htmlspecialchars($rowRoute['url']) ?>" allowfullscreen></iframe>
        </div>
        <div class="route-info">
          <h3 class="route-title"><?= htmlspecialchars($rowRoute['name']) ?></h3>
          <p class="route-description"><?= htmlspecialchars($rowRoute['description']) ?></p>
          <div class="btn-group">
            <a href="route.php?id=<?= urlencode($rowRoute['id_route']) ?>" class="btn-green">Подробнее</a>
            <a href="?remove_favorite_route=<?= $rowRoute['id_route'] ?>" class="btn-red" onclick="return confirm('Удалить маршрут из избранного?')">Удалить из избранного</a>
          </div>
        </div>
      </div>
    <?php
      endwhile;
    else:
      echo '<p class="placeholder-text">Здесь появятся ваши маршруты.</p>';
    endif;
    ?>
  </section>

  <!-- Отели -->
  <section id="tab-hotels" class="tab-content favorites-section">
    <h2>Избранные отели</h2>
    <?php
    if (!empty($favHotelsIndexes)):
      echo '<div class="hotels-container">';
      foreach ($favHotelsIndexes as $hotelIndex):
        if (!isset($hotels[$hotelIndex])) continue;
        $hotel = $hotels[$hotelIndex];
    ?>
      <div class="hotel-card">
        <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
        <div class="hotel-info">
          <p class="stars"><?= str_repeat("★", (int)($hotel['stars'] ?? 0)) ?> <strong>Отель</strong></p>
          <p class="title"><?= htmlspecialchars($hotel['name']) ?></p>
          <p class="subtitle"><?= htmlspecialchars($hotel['location']) ?></p>
          <p class="subtitle"><?= htmlspecialchars($hotel['distance']) ?></p>
        </div>
        <div class="hotel-price">
          <div class="price"><?= htmlspecialchars($hotel['price']) ?> <span class="rub">₽</span></div>
          <a class="more-btn" href="<?= htmlspecialchars($hotel['link']) ?>" target="_blank">Подробнее</a>
          <a href="?remove_favorite_hotel=<?= $hotelIndex ?>" class="btn-favorite remove" onclick="return confirm('Удалить отель из избранного?')">Удалить из избранного</a>
        </div>
      </div>
    <?php
      endforeach;
      echo '</div>';
    else:
      echo '<p>Вы ещё не добавили отели в избранное.</p>';
    endif;
    ?>
  </section>

  <!-- Тест -->
  <section id="tab-test" class="tab-content favorites-section">
  <form id="recommendationTest">
  <div>
    <p>1. Вы идёте с детьми?</p>
    <label><input type="radio" name="kids" value="семейный" required> Да</label><br>
    <label><input type="radio" name="kids" value="взрослый"> Нет</label>
  </div>
  <div>
    <p>2. Какой тип маршрута вы предпочитаете? <span class="required-star">*</span></p>
    <label><input type="checkbox" name="route_type" value="пешеходный"> Пешеходный</label><br>
    <label><input type="checkbox" name="route_type" value="исторический"> Исторический</label><br>
    <label><input type="checkbox" name="route_type" value="зелёный"> По зелёным зонам</label>
  </div>
  <div>
    <p>3. Какие кухни предпочитаете? <span class="required-star">*</span></p>
    <label><input type="checkbox" name="cuisine" value="европейская"> Европейская</label><br>
    <label><input type="checkbox" name="cuisine" value="русская"> Русская</label><br>
    <label><input type="checkbox" name="cuisine" value="грузинская"> Грузинская</label>
  </div>
  <button type="submit">Показать результаты</button>
  <div id="testError" style="color: red; margin-top: 10px;"></div>
</form>


<div id="testResults" style="margin-top:20px;"></div>

  </section>
</main>

<!-- Модальное окно с подробностями ресторана -->
<div id="restaurantModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-content">
    <button class="close" onclick="closeModal()" aria-label="Закрыть">&times;</button>
    <div class="modal-header" id="modalTitle">Название ресторана</div>
    <div class="modal-body">
      <img id="modalImage" src="" alt="Фото ресторана" />
      <p><strong>О ресторане:</strong> <span id="modalDescription"></span></p>
      <p><strong>Тип кухни:</strong> <span id="modalCuisine"></span></p>
      <p><strong>Виды блюд:</strong> <span id="modalFunctions"></span></p>
      <p><strong>Специализированное меню:</strong> <span id="modalSpecializedMenu"></span></p>
      <p><strong>Адрес:</strong> <span id="modalAddress"></span></p>
      <p><strong>Время работы:</strong> <span id="modalWorkingHours"></span></p>
      <p><strong>Телефон:</strong> <span id="modalPhone"></span></p>
    </div>
    <div class="modal-footer">
      <a id="menuLink" href="#" target="_blank"><button>Посмотреть меню</button></a>
    </div>
  </div>
</div>

<footer>
  <div class="footer-left">
    <img src="images/mainpage/gerb.png" alt="Герб" />
  </div>
  <div class="footer-center">
    <p>© Ордоян Абраам Мкртичевич</p>
    <p>Email: <a href="mailto:ordoyan.abraham@mail.ru">ordoyan.abraham@mail.ru</a></p>
  </div>
  <div class="footer-right">
    <a href="https://t.me/abraham4ik" target="_blank"><img src="images/mainpage/telegram.png" alt="Telegram" /></a>
    <a href="https://vk.com/abrahamo" target="_blank"><img src="images/mainpage/vk.png" alt="VK" /></a>
  </div>
</footer>

<script>
  const menuToggle = document.getElementById("menuToggle");
  const sideMenu = document.getElementById("sideMenu");
  const closeBtn = document.getElementById("closeMenu");

  menuToggle.addEventListener("click", () => {
    sideMenu.style.width = "250px";
  });

  closeBtn?.addEventListener("click", () => {
    sideMenu.style.width = "0";
  });

  // Логика табов
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');

  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.getAttribute('data-tab');
      tabButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      tabContents.forEach(tc => {
        if (tc.id === target) {
          tc.classList.add('active');
        } else {
          tc.classList.remove('active');
        }
      });
    });
  });

  // Модальное окно ресторана
  function openModal(id) {
    fetch(`getRestaurantDetails.php?id=${id}`)
      .then(response => response.json())
      .then(data => {
        document.getElementById('modalTitle').textContent = data.name;
        document.getElementById('modalDescription').textContent = data.description;
        document.getElementById('modalCuisine').textContent = data.cuisine;
        document.getElementById('modalFunctions').textContent = data.types_of_dishes;
        document.getElementById('modalSpecializedMenu').textContent = data.specialized_menu;
        document.getElementById('modalAddress').textContent = data.adress;
        document.getElementById('modalWorkingHours').textContent = data.working_hours;
        document.getElementById('modalPhone').textContent = data.telephone;
        document.getElementById('modalImage').src = `images/restaurants/${data.image_food || data.image_restaurant}`;
        document.getElementById('menuLink').href = data.url;
        document.getElementById('restaurantModal').style.display = 'block';
      })
      .catch(error => console.error('Ошибка загрузки данных:', error));
  }

  function closeModal() {
    document.getElementById('restaurantModal').style.display = 'none';
  }

  window.addEventListener('click', (e) => {
    const modal = document.getElementById('restaurantModal');
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });


  document.getElementById('recommendationTest').addEventListener('submit', function(e) {
  e.preventDefault();

  const form = e.target;
  const errorDiv = document.getElementById('testError');
  errorDiv.textContent = '';

  // Проверяем, выбран ли хотя бы один чекбокс для route_type
  const routeTypes = form.querySelectorAll('input[name="route_type"]:checked');
  if (routeTypes.length === 0) {
    errorDiv.textContent = 'Пожалуйста, выберите хотя бы один тип маршрута.';
    return;
  }

  // Проверяем, выбран ли хотя бы один чекбокс для cuisine
  const cuisines = form.querySelectorAll('input[name="cuisine"]:checked');
  if (cuisines.length === 0) {
    errorDiv.textContent = 'Пожалуйста, выберите хотя бы одну кухню.';
    return;
  }

  // Собираем все выбранные значения (радио и чекбоксы)
  const formData = new FormData(form);
  const selectedTags = [];
  for (const [name, value] of formData.entries()) {
    selectedTags.push(value);
  }

  // Отправляем запрос на сервер
  fetch('test_results.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({tags: selectedTags})
  })
  .then(res => res.json())
  .then(data => {
    const container = document.getElementById('testResults');
    container.innerHTML = '';
    errorDiv.textContent = '';

    if (data.error) {
      container.textContent = 'Ошибка при получении данных.';
      return;
    }

    if (data.routes.length === 0 && data.restaurants.length === 0) {
      container.textContent = 'По вашему запросу ничего не найдено.';
      return;
    }

    // Вывод маршрутов
    if (data.routes.length > 0) {
      const routesHeader = document.createElement('h3');
      routesHeader.textContent = 'Подходящие маршруты:';
      routesHeader.style.color = '#1a3e8a';
      container.appendChild(routesHeader);

      const routesContainer = document.createElement('div');
      routesContainer.style.display = 'flex';
      routesContainer.style.flexWrap = 'wrap';
      routesContainer.style.gap = '20px';

      data.routes.forEach(route => {
  const card = document.createElement('div');
  card.className = 'test-route-card';  // ИСПОЛЬЗУЕМ НОВЫЙ КЛАСС

  card.innerHTML = `
    <div class="route-map">
      <iframe src="${route.url}" allowfullscreen></iframe>
    </div>
    <div class="route-info">
      <h3 class="route-title">${route.name}</h3>
      <p class="route-description">${route.description}</p>
      <a href="route.php?id=${encodeURIComponent(route.id_route)}" class="btn-green">Подробнее</a>
    </div>
  `;

  routesContainer.appendChild(card);
});


      container.appendChild(routesContainer);
    }

    // Вывод ресторанов
    if (data.restaurants.length > 0) {
      const restHeader = document.createElement('h3');
      restHeader.textContent = 'Подходящие рестораны:';
      restHeader.style.color = '#1a3e8a';
      restHeader.style.marginTop = '30px';
      container.appendChild(restHeader);

      const restContainer = document.createElement('div');
      restContainer.style.display = 'flex';
      restContainer.style.flexWrap = 'wrap';
      restContainer.style.gap = '20px';

      data.restaurants.forEach(rest => {
        const card = document.createElement('div');
        card.className = 'restaurant-card';
        card.style.width = 'calc(33.333% - 13.33px)';
        card.style.background = '#fff';
        card.style.borderRadius = '15px';
        card.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.2)';
        card.style.border = '1px solid #ddd';
        card.style.overflow = 'hidden';
        card.style.cursor = 'pointer';
        card.style.display = 'flex';
        card.style.flexDirection = 'column';
        card.style.minHeight = '300px';

        card.innerHTML = `
  <img src="images/restaurants/${rest.image_restaurant}" alt="${rest.name}" style="width:100%; height:150px; object-fit: cover;" />
  <div class="restaurant-info">
    <p class="title">${rest.name}</p>
    <p class="cuisine">Тип кухни: ${rest.cuisine}</p>
    <p class="description">${rest.description}</p>
    <a href="javascript:void(0)" class="btn-green" onclick="openModal(${rest.id_restaurant})">Подробнее</a>
  </div>
`;

        restContainer.appendChild(card);
      });

      container.appendChild(restContainer);
    }
  })
  .catch(err => {
    const errorDiv = document.getElementById('testError');
    errorDiv.textContent = 'Ошибка соединения.';
    console.error(err);
  });
});



</script>

</body>
</html>
