<?php
session_start();

// Подключение к базе данных
$mysqli = new mysqli("localhost", "root", "", "diplom");

// Проверка на ошибки соединения
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Обработка добавления маршрута в избранное (для пользователей)
if (isset($_GET['add_favorite']) && isset($_SESSION['user_id']) && $_SESSION['role_id'] == 2) {
    $route_id = (int)$_GET['add_favorite'];
    $user_id = $_SESSION['user_id'];

    // Проверка, что такого избранного еще нет
    $stmtCheck = $mysqli->prepare("SELECT id_favorite FROM favorites_routes WHERE id_user = ? AND id_route = ?");
    $stmtCheck->bind_param("ii", $user_id, $route_id);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows == 0) {
        $stmtAdd = $mysqli->prepare("INSERT INTO favorites_routes (id_user, id_route) VALUES (?, ?)");
        $stmtAdd->bind_param("ii", $user_id, $route_id);
        $stmtAdd->execute();
        $stmtAdd->close();
    }
    $stmtCheck->close();

    // Перенаправление чтобы избежать повторного добавления при обновлении страницы
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// Обработка удаления маршрута из избранного (для пользователей)
if (isset($_GET['remove_favorite']) && isset($_SESSION['user_id']) && $_SESSION['role_id'] == 2) {
    $route_id = (int)$_GET['remove_favorite'];
    $user_id = $_SESSION['user_id'];

    $stmtDel = $mysqli->prepare("DELETE FROM favorites_routes WHERE id_user = ? AND id_route = ?");
    $stmtDel->bind_param("ii", $user_id, $route_id);
    $stmtDel->execute();
    $stmtDel->close();

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// Получение всех маршрутов
$result = $mysqli->query("SELECT * FROM routes");
if (!$result) {
    die("Error: " . $mysqli->error);
}

$routes = [];
while ($row = $result->fetch_assoc()) {
    $routes[] = $row;
}

// Получение избранных маршрутов пользователя (для отметки кнопки)
$favorites_routes_ids = [];
if (isset($_SESSION['user_id']) && $_SESSION['role_id'] == 2) {
    $user_id = $_SESSION['user_id'];
    $stmtFav = $mysqli->prepare("SELECT id_route FROM favorites_routes WHERE id_user = ?");
    $stmtFav->bind_param("i", $user_id);
    $stmtFav->execute();
    $resultFav = $stmtFav->get_result();
    while ($fav = $resultFav->fetch_assoc()) {
        $favorites_routes_ids[] = $fav['id_route'];
    }
    $stmtFav->close();
}

// Обработка добавления/редактирования маршрутов (админ)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_route'])) {
  $id = intval($_POST['id_route']);
  $name = $mysqli->real_escape_string($_POST['name']);
  $desc = $mysqli->real_escape_string($_POST['description']);
  $url = $mysqli->real_escape_string($_POST['url']);
  $extra = $mysqli->real_escape_string($_POST['extra_text']);
  $tags = $mysqli->real_escape_string($_POST['tags']);

  if ($id > 0) {
      $mysqli->query("UPDATE routes SET name='$name', description='$desc', url='$url', extra_text='$extra', tags='$tags' WHERE id_route=$id");
  } else {
      $mysqli->query("INSERT INTO routes (name, description, url, extra_text, tags) VALUES ('$name', '$desc', '$url', '$extra', '$tags')");
  }
  header("Location: Routes.php");
  exit;
}

// Удаление маршрута (админ)
if (isset($_GET['delete_id']) && isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
  $id = intval($_GET['delete_id']);
  $mysqli->query("DELETE FROM routes WHERE id_route=$id");
  header("Location: Routes.php");
  exit;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Маршруты</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .admin-button {
      background-color: #000000;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 15px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s;
    }
    .admin-button:hover { background-color: #e6b800; color: black;}
    .delete-button { background-color: #000000; }
    .delete-button:hover { background-color: #e30016; color: white}


    .btn-save {
  background-color: #1a2a56;
  color: white;
  font-weight: 600;
  border-radius: 15px;
  padding: 8px 18px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  min-width: 90px;
  transition: background-color 0.3s ease;
}

.btn-save:hover {
  background-color: #11204a;
}

.btn-cancel {
  background-color: #777;
  color: white;
  font-weight: 600;
  border-radius: 15px;
  padding: 8px 18px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  min-width: 90px;
  transition: background-color 0.3s ease;
}

.btn-cancel:hover {
  background-color: #555;
}

    .modal {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      z-index: 1000;
      transform: translate(-50%, -50%);
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.4);
      width: 90%;
      max-width: 600px;
    }
    .modal h2 { margin-top: 0; }
    .modal input, .modal textarea {
      width: 100%;
      margin-bottom: 15px;
      padding: 10px;
      font-size: 16px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      width: 90%;
      max-width: 600px;
      position: relative;
    }
    .modal-content .close {
      position: absolute;
      top: 10px; right: 15px;
      font-size: 20px;
      cursor: pointer;
      color: red;
    }
    .modal-content label {
      display: block;
      margin-top: 10px;
    }
    .modal-content input, .modal-content textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
    }
    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: 999;
    }
    footer {
      position: relative;
      bottom: 0;
      width: 100%;
      background-color: #1D1D1D;
      color: white;
      padding: 20px;
      text-align: center;
      box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
      font-size: 16px;
    }
    h1 {
      font-size: 36px;
      color: #000;
      text-align: center;
      margin: 0px;
    }
    main {
      max-width: 1200px;
      margin: 20px auto 30px;
      padding: 20px;
    }
    .route-container {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      margin-bottom: 40px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .map-container {
      flex: 1;
      min-width: 300px;
      height: 400px;
    }
    .info-container {
      flex: 1;
      min-width: 300px;
      padding: 20px;
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
    .route-button {
  bottom: 10px;
  right: 10px;
  background-color: white; /* Белый фон */
  color: black; /* Черный цвет текста */
  padding: 10px 15px; /* Отступы сверху и снизу 10px, слева и справа 15px */
  text-decoration: none;
  text-align: center;
  border-radius: 15px; /* Закругленные углы */
  border: 2px solid black; /* Черная обводка */
  font-weight: bold; /* Жирный текст */
    }

    .route-button:hover {
      background-color: #f0f0f0; /* Легкий серый фон при наведении */
  color: black; /* Текст остается черным */
  border-color: black; /* Обводка тоже остается черной */
    }

    .btn-add-favorite {
      background-color: #000000;
      color: white;
      font-weight: 600;
      border-radius: 20px; /* Закругленные углы */
      padding: 10px 20px;
      text-align: center;
      cursor: pointer;
      user-select: none;
      box-shadow: 0 2px 5px rgba(26, 62, 138, 0.4);
      display: inline-block;
      margin-top: 0;
      text-decoration: none;
      font-family: Arial, sans-serif;
      font-size: 16px;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-add-favorite:hover {
      background-color: #e6b800;
      color: black;
      box-shadow: 0 4px 10px rgba(18, 46, 109, 0.6);
    }
    .btn-remove-favorite {
      background-color: #000000;
      color: white;
      font-weight: 600;
      border-radius: 20px; /* Закругленные углы */
      padding: 10px 20px;
      text-align: center;
      cursor: pointer;
      user-select: none;
      box-shadow: 0 2px 5px rgba(215, 76, 65, 0.4);
      display: inline-block;
      margin-top: 0;
      text-decoration: none;
      font-family: Arial, sans-serif;
      font-size: 16px;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      margin-left: 10px;
    }
    .btn-remove-favorite:hover {
      background-color: #e30016;
      box-shadow: 0 4px 10px rgba(176, 60, 55, 0.6);
    }
    .buttons-row {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
    @media (max-width: 768px) {
      .route-container {
        flex-direction: column;
      }
      .map-container, .info-container {
        width: 100%;
      }
    }
  </style>
</head>

<body>

<header>
  <div class="logo">
    <a href="mainpage.php">
      <img src="images/mainpage/logo2.png" alt="Ярославль">
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

<main>
<h1 style="text-align: center; margin-bottom: 40px;">Маршруты</h1>
<?php if (count($routes) > 0): ?>

<?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
<div style="text-align:center; margin-bottom: 30px;">
  <button class="admin-button" onclick="openRouteForm()">Добавить маршрут</button>
</div>
<?php endif; ?>

<?php foreach ($routes as $route): ?>
  <div class="route-container">
    <div class="map-container">
      <iframe 
        src="<?php echo htmlspecialchars($route['url']); ?>" 
        width="100%" 
        height="100%" 
        frameborder="0"
        allowfullscreen="true">
      </iframe>
    </div>
    <div class="info-container">
      <h2 class="route-title"><?php echo htmlspecialchars($route['name']); ?></h2>
      <p class="route-description"><?php echo htmlspecialchars($route['description']); ?></p>
      <div class="buttons-row">
        <a href="route.php?id=<?php echo urlencode($route['id_route']); ?>" class="route-button">Подробнее</a>

        <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
          <?php if (in_array($route['id_route'], $favorites_routes_ids)): ?>
            <a href="?remove_favorite=<?= $route['id_route'] ?>" class="btn-remove-favorite" onclick="return confirm('Удалить маршрут из избранного?')">Удалить из избранного</a>
          <?php else: ?>
            <a href="?add_favorite=<?= $route['id_route'] ?>" class="btn-add-favorite" onclick="return confirm('Добавить маршрут в избранное?')">Добавить в избранное</a>
          <?php endif; ?>
        <?php endif; ?>

      </div>

      <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
        <div style="margin-top: 10px; display: flex; gap: 10px;">
          <button class="admin-button" onclick="openRouteForm(<?= $route['id_route'] ?>)">Редактировать</button>
          <button class="admin-button delete-button" onclick="confirmDeleteRoute(<?= $route['id_route'] ?>)">Удалить</button>
        </div>
      <?php endif; ?>

    </div>
  </div>
<?php endforeach; ?>
<?php else: ?>
  <p>Нет доступных маршрутов</p>
<?php endif; ?>
</main>

<div id="routeFormModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeRouteForm()">&times;</span>
    <h2 id="routeFormTitle">Добавить маршрут</h2>
    <form method="POST" id="routeForm">
      <input type="hidden" name="id_route" id="routeId">
      <label for="name">Название:</label>
      <input type="text" name="name" id="name" required>
      <label for="description">Описание:</label>
      <textarea name="description" id="description" rows="4" required></textarea>
      <label for="extra_text">Дополнительная информация:</label>
      <textarea name="extra_text" id="extra_text" rows="3" required></textarea>
      <label for="url">Ссылка на карту:</label>
      <input type="text" name="url" id="url" required>
      <label for="tags">Теги (через запятую):</label>
      <input type="text" name="tags" id="tags" placeholder="например, история, дети, прогулка">
      <div class="modal-footer">
  <button type="submit" name="save_route" class="btn-save">Сохранить</button>
  <button type="button" class="btn-cancel" onclick="closeRouteForm()">Отмена</button>
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

<script>
function openRouteForm(id = null) {
  document.getElementById("routeForm").reset();
  document.getElementById("routeFormTitle").textContent = "Добавить маршрут";
  document.getElementById("routeId").value = '';
  document.getElementById("tags").value = '';

  if (id) {
    fetch("getRouteDetails.php?id=" + id)
      .then(res => res.json())
      .then(data => {
        if (data && !data.error) {
          document.getElementById("routeId").value = data.id_route;
          document.getElementById("name").value = data.name;
          document.getElementById("description").value = data.description;
          document.getElementById("extra_text").value = data.extra_text;
          document.getElementById("url").value = data.url;
          document.getElementById("tags").value = data.tags || '';
          document.getElementById("routeFormTitle").textContent = "Редактировать маршрут";
        }
      });
  }

  document.getElementById("routeFormModal").style.display = "block";
}

function closeRouteForm() {
  document.getElementById("routeFormModal").style.display = "none";
}

function confirmDeleteRoute(id) {
  if (confirm("Удалить маршрут?")) {
    window.location.href = "Routes.php?delete_id=" + id;
  }
}
</script>

</body>
</html>
