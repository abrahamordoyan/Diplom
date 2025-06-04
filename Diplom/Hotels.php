<?php
session_start();

$jsonFile = 'hotels.json';

// Функция для чтения данных из JSON
function getHotels($jsonFile) {
    if (!file_exists($jsonFile)) return [];
    $json = file_get_contents($jsonFile);
    return json_decode($json, true) ?: [];
}

// Функция для сохранения данных в JSON
function saveHotels($jsonFile, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($jsonFile, $json);
}

$hotels = getHotels($jsonFile);

// Подключение к БД
$mysqli = new mysqli("localhost", "root", "", "diplom");
$mysqli->set_charset("utf8");

$user_id = $_SESSION['user_id'] ?? null;

// Обработка добавления отеля в избранное (для пользователей)
if (isset($_GET['add_favorite_hotel']) && $user_id && $_SESSION['role_id'] == 2) {
    $hotel_index = (int)$_GET['add_favorite_hotel'];
    // Проверка, что такого избранного ещё нет
    $stmtCheck = $mysqli->prepare("SELECT id_favorite FROM favorites_hotels WHERE id_user = ? AND hotel_index = ?");
    $stmtCheck->bind_param("ii", $user_id, $hotel_index);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows == 0) {
        $stmtAdd = $mysqli->prepare("INSERT INTO favorites_hotels (id_user, hotel_index) VALUES (?, ?)");
        $stmtAdd->bind_param("ii", $user_id, $hotel_index);
        $stmtAdd->execute();
        $stmtAdd->close();
    }
    $stmtCheck->close();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// Обработка удаления отеля из избранного (для пользователей)
if (isset($_GET['remove_favorite_hotel']) && $user_id && $_SESSION['role_id'] == 2) {
    $hotel_index = (int)$_GET['remove_favorite_hotel'];
    $stmtDel = $mysqli->prepare("DELETE FROM favorites_hotels WHERE id_user = ? AND hotel_index = ?");
    $stmtDel->bind_param("ii", $user_id, $hotel_index);
    $stmtDel->execute();
    $stmtDel->close();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// Получаем список избранных отелей пользователя
$favorites_hotels = [];
if ($user_id && $_SESSION['role_id'] == 2) {
    $stmtFav = $mysqli->prepare("SELECT hotel_index FROM favorites_hotels WHERE id_user = ?");
    $stmtFav->bind_param("i", $user_id);
    $stmtFav->execute();
    $resultFav = $stmtFav->get_result();
    while ($fav = $resultFav->fetch_assoc()) {
        $favorites_hotels[] = $fav['hotel_index'];
    }
    $stmtFav->close();
}

// Обработка POST (добавление и редактирование)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_hotel'])) {
    $id = $_POST['id'] ?? '';
    $hotelData = [
        'name' => $_POST['name'],
        'location' => $_POST['location'],
        'distance' => $_POST['distance'],
        'price' => $_POST['price'],
        'stars' => intval($_POST['stars']),
        'image' => $_POST['image'],
        'link' => $_POST['link'],
    ];

    if ($id === '') {
        // Добавление нового отеля
        $hotels[] = $hotelData;
    } else {
        // Редактирование существующего
        $id = intval($id);
        if (isset($hotels[$id])) {
            $hotels[$id] = $hotelData;
        }
    }

    saveHotels($jsonFile, $hotels);
    header('Location: hotels.php');
    exit;
}

// Обработка удаления (для админа)
if (isset($_GET['delete_id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
    $id = intval($_GET['delete_id']);
    if (isset($hotels[$id])) {
        array_splice($hotels, $id, 1);
        saveHotels($jsonFile, $hotels);
    }
    header('Location: hotels.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Отели в Ярославле</title>
  <link rel="stylesheet" href="styles.css">
  <style>
  /* Твои стили отелей */
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
  min-width: 280px;
  max-width: 300px;
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
  border-radius: 20px; /* Закругленные углы */
  border: 2px solid black; /* Черная обводка */
  font-weight: bold; /* Жирный текст */
}
.more-btn:hover {
  background-color: #f0f0f0; /* Легкий серый фон при наведении */
  color: black; /* Текст остается черным */
  border-color: black; /* Обводка тоже остается черной */
}

button.add-hotel-button {
  display: block;
  margin: 0 auto 5px auto;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-weight: bold;
  padding: 10px 20px;
  border-radius: 20px; /* Закругленные углы */
  background-color: #000000;
  color: white;
  border: none;
  cursor: pointer;
  white-space: nowrap;
  width: 200px;
  transition: background-color 0.3s ease;
}

button.add-hotel-button:hover {
  background-color: #e6b800;
  color: black;
} 

.hotel-buttons {
  display: flex;
  gap: 10px;
  width: 100%;
  justify-content: flex-end;
  margin-top: 0;
}
.hotel-buttons button {
  padding: 10px 15px;
  border-radius: 20px;
  font-weight: bold;
  border: none;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  transition: background-color 0.3s ease;
  color: white;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 16px;
  white-space: nowrap;
  min-width: 100px;
}
.hotel-buttons button.edit-button {
  background-color: #000000;
  min-width: 140px;
  padding-left: 15px;
  padding-right: 15px;
  border-radius: 20px; /* Закругленные углы */

}
.hotel-buttons button.edit-button:hover {
  background-color: #e6b800;
  color: black;
}
.hotel-buttons button.delete-button {
  background-color: #000000;
}
.hotel-buttons button.delete-button:hover {
  background-color: #e30016;
}

/* Новые стили для кнопок "Добавить/Удалить в избранное" */
.btn-favorite {
  display: inline-block;
  padding: 12px 0;
  width: 100%; /* чтобы совпадала с шириной "Подробнее" */
  font-weight: 600;
  border-radius: 20px; /* Закругленные углы */
  text-align: center;
  cursor: pointer;
  user-select: none;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 16px;
  color: white;
  box-shadow: 0 2px 5px rgba(26, 62, 138, 0.4);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  text-decoration: none;
  margin-top: 10px; /* отступ сверху */
}
.btn-favorite.add {
  background-color: #000000;
}
.btn-favorite.add:hover {
  background-color: #e6b800;
  color: black;
  box-shadow: 0 4px 10px rgba(18, 46, 109, 0.6);
}
.btn-favorite.remove {
  background-color: #000000;
}
.btn-favorite.remove:hover {
  background-color: #e30016;
  box-shadow: 0 4px 10px rgba(176, 60, 55, 0.6);
}

/* Фон затемнения */
#overlay {
  display: none;
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 999;
}

/* Модальное окно */
.modal {
  display: none;
  position: fixed;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  width: 420px;
  background: white;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  z-index: 1000;
  padding: 20px 30px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Кнопка закрытия */
.close-btn {
  font-size: 28px;
  font-weight: bold;
  color: #666;
  cursor: pointer;
  float: right;
  user-select: none;
}

.close-btn:hover {
  color: #000;
}

/* Заголовок */
.modal h2 {
  margin-top: 0;
  margin-bottom: 20px;
  text-align: center;
}

/* Формы и подписи */
.modal label {
  display: block;
  margin-top: 15px;
  margin-bottom: 5px;
  font-weight: 600;
}

.modal input[type="text"],
.modal input[type="number"],
.modal input[type="url"],
.modal input[type="email"],
.modal textarea {
  width: 100%;
  padding: 8px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  box-sizing: border-box;
  font-size: 14px;
  font-family: inherit;
}

/* Если понадобится многострочное описание, textarea: */
/* .modal textarea {
  height: 80px;
  resize: vertical;
} */

/* Кнопки */
.modal-buttons {
  margin-top: 25px;
  text-align: right;
}

.modal-buttons button {
  font-weight: 600;
  border-radius: 15px;
  padding: 8px 18px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  min-width: 90px;
  margin-left: 10px;
  transition: background-color 0.3s ease;
}

.btn-save {
  background-color: #1a2a56;
  color: white;
}

.btn-save:hover {
  background-color: #11204a;
}

.btn-cancel {
  background-color: #777;
  color: white;
}

.btn-cancel:hover {
  background-color: #555;
}


  @media (max-width: 768px) {
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
  </style>
</head>
<body>

<header>
  <div class="logo">
    <a href="mainpage.php"><img src="images/mainpage/logo2.png" alt="Ярославль"></a>
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
      <?php if (isset($_SESSION['user_id'])): ?>
          <?php if ($_SESSION['role_id'] == 1): ?>
            <li><a href="admin_dashboard.php" class="side-login">Личный кабинет</a></li>
          <?php else: ?>
            <li><a href="user_dashboard.php" class="side-login">Личный кабинет</a></li>
          <?php endif; ?>
          <li><a href="logout.php">Выйти</a></li>
      <?php else: ?>
          <li><a href="Login.php" class="side-login">Вход</a></li>
      <?php endif; ?>
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
    <?php if (isset($_SESSION['user_id'])): ?>
      <?php if ($_SESSION['role_id'] == 1): ?>
        <a href="admin_dashboard.php">Личный кабинет</a>
      <?php else: ?>
        <a href="user_dashboard.php">Личный кабинет</a>
      <?php endif; ?>
    <?php else: ?>
      <a href="Login.php">Вход</a>
    <?php endif; ?>
  </div>
</header>

<main class="hotels-container">
  <h1 style="text-align:center;">Отели в Ярославле</h1>

  <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
  <button onclick="openHotelForm()" class="add-hotel-button">Добавить отель</button>
<?php endif; ?>


  <?php if (!empty($hotels)): ?>
    <?php foreach ($hotels as $id => $hotel): ?>
      <div class="hotel-card">
        <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
        <div class="hotel-info">
          <p class="stars"><?= str_repeat("★", (int)($hotel['stars'] ?? 0)) ?> <strong>Отель</strong></p>
          <p class="title"><?= htmlspecialchars($hotel['name'] ?? 'Без названия') ?></p>
          <p class="subtitle"><?= htmlspecialchars($hotel['location'] ?? 'Не указано') ?></p>
          <p class="subtitle"><?= htmlspecialchars($hotel['distance'] ?? 'Не указано') ?></p>
        </div>
        <div class="hotel-price">
  <div class="price"><?= htmlspecialchars($hotel['price']) ?> <span class="rub">₽</span></div>
  <a class="more-btn" href="<?= htmlspecialchars($hotel['link']) ?>" target="_blank">Подробнее</a>

  <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
    <div class="hotel-buttons">
      <button class="edit-button" onclick="openHotelForm(<?= $id ?>)">Редактировать</button>
      <button class="delete-button" onclick="if(confirm('Удалить отель?')) window.location.href='hotels.php?delete_id=<?= $id ?>'">Удалить</button>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
    <?php if (in_array($id, $favorites_hotels)): ?>
      <a href="?remove_favorite_hotel=<?= $id ?>" class="btn-favorite remove" onclick="return confirm('Удалить отель из избранного?')">Удалить из избранного</a>
    <?php else: ?>
      <a href="?add_favorite_hotel=<?= $id ?>" class="btn-favorite add" onclick="return confirm('Добавить отель в избранное?')">Добавить в избранное</a>
    <?php endif; ?>
  <?php endif; ?>

</div>


      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align:center;">Нет доступных отелей.</p>
  <?php endif; ?>
</main>

<div id="overlay"></div>

<!-- Модальное окно отеля -->
<div id="hotelFormModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeHotelForm()">&times;</span>
    <h2 id="modalTitle">Добавить отель</h2>
    <form method="POST" action="hotels.php" id="hotelForm">
      <input type="hidden" name="id" id="hotelId">

      <label for="hotelName"><b>Название:</b></label>
      <input type="text" name="name" id="hotelName" required>

      <label for="hotelLocation"><b>Адрес:</b></label>
      <input type="text" name="location" id="hotelLocation" required>

      <label for="hotelDistance"><b>Расстояние от центра:</b></label>
      <input type="text" name="distance" id="hotelDistance" required>

      <label for="hotelPrice"><b>Цена:</b></label>
      <input type="text" name="price" id="hotelPrice" required>

      <label for="hotelStars"><b>Звезды:</b></label>
      <input type="number" name="stars" id="hotelStars" min="1" max="5" required>

      <label for="hotelImage"><b>Изображение (путь):</b></label>
      <input type="text" name="image" id="hotelImage" required>

      <label for="hotelLink"><b>Ссылка:</b></label>
      <input type="url" name="link" id="hotelLink" required>

      <div class="modal-buttons">
        <button type="submit" name="save_hotel" class="btn-save">Сохранить</button>
        <button type="button" class="btn-cancel" onclick="closeHotelForm()">Отмена</button>
      </div>
    </form>
  </div>
</div>


<footer>
  <div class="footer-left">
    <img src="images/mainpage/gerb.png" alt="Герб">
  </div>
  <div class="footer-center">
    <p>© Ордоян Абраам Мкртичевич</p>
    <p>Email: <a href="mailto:ordoyan.abraham@mail.ru">ordoyan.abraham@mail.ru</a></p>
  </div>
  <div class="footer-right">
    <a href="https://t.me/abraham4ik" target="_blank"><img src="images/mainpage/telegram.png" alt="Telegram"></a>
    <a href="https://vk.com/abrahamo" target="_blank"><img src="images/mainpage/vk.png" alt="VK"></a>
  </div>
</footer>

<script>
const modal = document.getElementById('hotelFormModal');
const overlay = document.getElementById('overlay');

function openHotelForm(id = null) {
  overlay.style.display = 'block';
  modal.style.display = 'block';
  document.getElementById('hotelForm').reset();
  document.getElementById('modalTitle').textContent = id === null ? 'Добавить отель' : 'Редактировать отель';
  if (id !== null) {
    const hotelsData = <?= json_encode($hotels) ?>;
    const hotel = hotelsData[id];
    document.getElementById('hotelId').value = id;
    document.getElementById('hotelName').value = hotel.name;
    document.getElementById('hotelLocation').value = hotel.location;
    document.getElementById('hotelDistance').value = hotel.distance;
    document.getElementById('hotelPrice').value = hotel.price;
    document.getElementById('hotelStars').value = hotel.stars;
    document.getElementById('hotelImage').value = hotel.image;
    document.getElementById('hotelLink').value = hotel.link;
  } else {
    document.getElementById('hotelId').value = '';
  }
}

function closeHotelForm() {
  modal.style.display = 'none';
  overlay.style.display = 'none';
}
</script>

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
</script>

<style>
#hotelFormModal {
  display: none;
  position: fixed;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  z-index: 1000;
  width: 400px;
}
#overlay {
  display: none;
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 999;
}
</style>

</body>
</html>
