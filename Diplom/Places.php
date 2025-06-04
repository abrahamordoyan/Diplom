<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Достопримечательности города Ярославль</title>
  <link rel="stylesheet" href="styles.css" />
  <style>

.places-container {
  text-align: center;
  padding: 20px;
}

.places-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* Два изображения в ряду */
  /* gap: 20px;  */
  margin-top: 30px;
  margin-bottom: 30px;
  max-width: 1000px; 
  margin-left: auto;
  margin-right: auto;
}

.place-card {
  position: relative;
  background-color: #fff;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%; /* Ширина будет 100% от родителя */
  max-width: 500px; /* Ограничиваем максимальную ширину изображения */
  height: 300px; /* Фиксированная высота */
}

.place-card img {
  width: 100%; /* Картинка займет всю ширину блока */
  height: 100%; /* Картинка займет всю высоту блока */
  object-fit: cover; /* Картинка будет заполнять пространство, не искажаясь */
}

.details-btn {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background-color: white; /* Белый фон */
  color: black; /* Черный цвет текста */
  padding: 10px 15px; /* Отступы сверху и снизу 10px, слева и справа 15px */
  text-decoration: none;
  border-radius: 25px; /* Закругленные углы */
  border: 2px solid black; /* Черная обводка */
  font-size: 14px;
  font-weight: bold; /* Жирный текст */
}

.details-btn:hover {
  background-color: #f0f0f0; /* Легкий серый фон при наведении */
  color: black; /* Текст остается черным */
  border-color: black; /* Обводка тоже остается черной */
}
.places-container h1 {
  margin-top: 20px; /* Отступ сверху для заголовка */
  font-size: 26px; /* Размер шрифта для заголовка */
  font-weight: bold; /* Жирный шрифт */
}

.places-container p {
  margin-top: 10px; /* Отступ сверху для описания */
  font-size: 18px; /* Размер шрифта для описания */
  color: #555; /* Цвет текста */
}

/* Для первой картинки — скругляем левый верхний угол */
.places-grid .place-card:nth-child(1) {
  border-radius: 20px 0 0 0; /* Скругляем только левый верхний угол */
}

/* Для второй картинки — скругляем правый верхний угол */
.places-grid .place-card:nth-child(2) {
  border-radius: 0 20px 0 0; /* Скругляем только правый верхний угол */
}

/* Для первой картинки в последнем ряду — скругляем правый нижний угол */
.places-grid .place-card:nth-child(10) {
  border-radius: 0 0 20px 0; /* Скругляем только правый нижний угол */
}

/* Для последней картинки в ряду — скругляем левый нижний угол */
.places-grid .place-card:nth-child(9) {
  border-radius: 0 0 0 20px; /* Скругляем только левый нижний угол */
}


footer {
  position: relative;
  bottom: 0;
  width: 100%;
  background-color: #1d1d1d;
  color: white;
  padding: 20px;
  text-align: center;
  font-size: 16px;
}


@media (max-width: 768px) {
  .places-grid {
    grid-template-columns: repeat(2, 1fr); /* Два изображения в ряду на экранах до 768px */
  }
}

@media (max-width: 480px) {
  .places-grid {
    grid-template-columns: 1fr; /* Одно изображение в ряду на экранах до 480px */
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

  <main class="places-container">
    <h1>Достопримечательности города Ярославль</h1>
    <p>Посетите лучшие достопримечательности нашего города</p>
  
    <div class="places-grid">
      <div class="place-card">
        <img src="images/places/sobor-iliyi-proroka.jpg" alt="Достопримечательность 1">
        <a href="Sobor.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/church-ioan.png" alt="Достопримечательность 2">
        <a href="Church.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/kreml.jpg" alt="Достопримечательность 3">
        <a href="Kreml.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/hram-hrista.png" alt="Достопримечательность 4">
        <a href="Hram_Hrista.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/zapovednik.jpg" alt="Достопримечательность 4">
        <a href="Zapovednik.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/park-1000.jpg" alt="Достопримечательность 1">
        <a href="Park1000.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/strelka.jpg" alt="Достопримечательность 4">
        <a href="Strelka.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/planetariy.jpg" alt="Достопримечательность 4">
        <a href="Planetariy.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/theatr-volkova.jpg" alt="Достопримечательность 4">
        <a href="Theatre.php" class="details-btn">Подробнее</a>
      </div>

      <div class="place-card">
        <img src="images/places/gubernatorskiy-sad.png" alt="Достопримечательность 4">
        <a href="Garden.php" class="details-btn">Подробнее</a>
      </div>
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
