<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "diplom");
if ($mysqli->connect_error) {
    die("Ошибка соединения: " . $mysqli->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = $mysqli->query("SELECT * FROM routes WHERE id_route = $id");

if (!$result || $result->num_rows === 0) {
    die("Маршрут не найден");
}

$route = $result->fetch_assoc();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($route['name']); ?></title>
  <link rel="stylesheet" href="styles.css">
  <style>
    main {
      max-width: 960px;
      margin: 120px auto 80px;
      padding: 0 20px;
    }

    .route-title {
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 15px;
      border-left: 6px solid #d33;
      padding-left: 15px;
    }

    .route-description {
      font-size: 18px;
      color: #444;
      margin-bottom: 30px;
    }

    iframe {
      width: 100%;
      height: 450px;
      border: none;
      border-radius: 6px;
      margin-bottom: 40px;
    }

    .extra-text {
      font-size: 17px;
      background: none;
      color: #2a2a2a;
      padding-left: 10px;
      border-left: 4px solid #4CAF50;
    }

    .route-buttons {
      display: flex;
      gap: 20px;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .route-button {
      display: inline-block;
      padding: 12px 24px;
      background-color: #d33;
      color: white;
      text-decoration: none;
      font-weight: bold;
      border-radius: 6px;
      transition: background-color 0.3s ease;
    }

    .route-button:hover {
      background-color: #b72b2b;
    }

    .route-button.back {
      background-color: #555;
    }

    .route-button.back:hover {
      background-color: #333;
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
  <h1 class="route-title"><?php echo htmlspecialchars($route['name']); ?></h1>
  <p class="route-description"><?php echo nl2br(htmlspecialchars($route['description'])); ?></p>

  <iframe src="<?php echo htmlspecialchars($route['url']); ?>" allowfullscreen></iframe>

  <?php if (!empty($route['extra_text'])): ?>
    <div class="extra-text">
      <?php echo nl2br(htmlspecialchars($route['extra_text'])); ?>
    </div>
  <?php endif; ?>

  <div class="route-buttons">
    <a href="<?php echo htmlspecialchars($route['url']); ?>" target="_blank" class="route-button">Пройти маршрут</a>
    <a href="Routes.php" class="route-button back">Вернуться к маршрутам</a>
  </div>
</main>

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

</body>
</html>
