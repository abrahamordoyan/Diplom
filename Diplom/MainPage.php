<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "diplom");
if ($mysqli->connect_error) {
    die("Ошибка соединения: " . $mysqli->connect_error);
}
$routes_result = $mysqli->query("SELECT * FROM routes LIMIT 3");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ярославль - Туристический сайт</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .routes-blocks {
      display: flex;
      gap: 40px;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 40px;
    }

    .route {
      background-color: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
      padding: 25px;
      width: 520px;
      min-height: 700px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.3s ease;
    }

    .route:hover {
      transform: translateY(-5px);
    }

    .route iframe {
      width: 100%;
      height: 400px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .route h3 {
      font-size: 22px;
      font-weight: 600;
      color: #111;
      margin-bottom: 12px;
    }

    .route p {
      font-size: 16px;
      color: #444;
      flex-grow: 1;
      line-height: 1.5;
      margin-bottom: 20px;
    }

    .route .btn-container {
      margin-top: auto;
    }

    .route button {
      background-color: #ffd700;
      color: black;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .route button:hover {
      background-color: #E6B800 ;
    }

    .more-routes {
    margin-top: 40px;
    padding: 12px 24px;
    background-color: #000000;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Вариант 1: Золотой акцент (рекомендуется) */
.more-routes:hover {
    background-color: #FFD700; /* Золотой из герба */
    color: #000000;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(255, 215, 0, 0.2);
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
  <section class="intro">
    <div class="image-container" id="slideshow">
      <img src="images/mainpage/main-yaroslawl3.jpg" alt="Ярославль" class="main-image" id="mainImage">
      <div class="text-overlay">
        <h2>Ярославль: больше, чем просто город</h2>
        <p>Маршруты, достопримечательности, кафе, парки и вечерние прогулки — выбери, что интересно именно тебе.</p>
      </div>
    </div>
    <div class="blue-blocks">
      <div class="block"><img src="images/mainpage/hotel2.png" alt="Отели"><p>Отели</p></div>
      <div class="block"><img src="images/mainpage/restauraunt2.png" alt="Рестораны"><p>Рестораны</p></div>
      <div class="block"><img src="images/mainpage/map2.png" alt="Карта города"><p>Карта города</p></div>
      <div class="block"><img src="images/mainpage/route2.png" alt="Маршруты"><p>Маршруты</p></div>
      <div class="block"><img src="images/mainpage/poster2.png" alt="Афиша"><p>Афиша</p></div>
    </div>
  </section>

  <section class="routes">
    <h2>Популярные маршруты</h2>
    <div class="routes-blocks">
      <?php while ($route = $routes_result->fetch_assoc()): ?>
        <div class="route">
          <iframe src="<?= htmlspecialchars($route['url']) ?>" frameborder="0" allowfullscreen></iframe>
          <h3><?= htmlspecialchars($route['name']) ?></h3>
          <p><?= htmlspecialchars($route['description']) ?></p>
          <div class="btn-container">
            <a href="route.php?id=<?= $route['id_route'] ?>">
              <button>Пройти маршрут</button>
            </a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <a href="Routes.php"><button class="more-routes">Больше маршрутов</button></a>
  </section>
</main>

<footer>
  <div class="footer-left">
    <img src="images/mainpage/gerb.png" alt="Герб">
  </div>
  <div class="footer-center">
    <p>© Ордоян Абраам Мкртичевич</p>
    <p>Email: <a href="mailto:ordoyan.abraham@mail.ru">ordoyan.abraham@mail.ru</a></p>
  </div>
  <div class="footer-right socials">
    <a href="https://t.me/abraham4ik" target="_blank"><img src="images/mainpage/telegram.png" alt="Telegram"></a>
    <a href="https://vk.com/abrahamo" target="_blank"><img src="images/mainpage/vk.png" alt="VK"></a>
  </div>
</footer>

<script>
  const images = [
    "images/mainpage/main-yaroslawl3.jpg",
    "images/mainpage/main-yaroslawl2.jpg",
    "images/mainpage/main-yaroslawl1.jpg",
    "images/mainpage/main-yaroslawl4.jpg"
  ];
  let current = 0;
  const imgElement = document.getElementById("mainImage");
  imgElement.src = images[current];
  setInterval(() => {
    current = (current + 1) % images.length;
    imgElement.style.opacity = 0;
    setTimeout(() => {
      imgElement.src = images[current];
      imgElement.style.opacity = 1;
    }, 700);
  }, 6000);

  const sideMenu = document.getElementById('sideMenu');
  const closeBtn = document.getElementById('closeMenu');
  const menuToggle = document.getElementById('menuToggle');
  menuToggle.addEventListener('click', () => sideMenu.style.width = '250px');
  closeBtn.addEventListener('click', () => sideMenu.style.width = '0');
</script>

</body>
</html>
