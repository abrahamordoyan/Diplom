<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ярославский Кремль</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .content-container {
      width: 80%;
      margin: 0 auto;
      text-align: center;
    }

    .place-image {
      width: 100%;
      height: auto;
      max-width: 800px;
      margin-bottom: 20px;
    }

    .place-description {
      font-size: 18px;
      line-height: 1.6;
      margin-bottom: 30px;
      text-align: left; /* Выравнивание по левому краю для описания */

    }

    .map-container {
      height: 600px;
      width: 1100px;
      margin-top: 20px;
      margin: 0 auto; /* Центрируем карту */
      margin-bottom: 40px; 
    }

    #map {
      width: 100%;
      height: 100%;
    }

    h1 {
      margin-top: 30px; /* Отступ сверху от заголовка */
      margin-bottom: 20px; /* Отступ снизу от заголовка */
    }

    h2, h3 {
      margin-bottom: 10px;
      text-align: left;
    }

    h4 {
      margin-bottom: 10px;
      font-size: 22px;
    }

    p {
      margin-bottom: 20px;
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

  <div class="content-container">
    <h1>Церковь Спаса на Городу</h1>
    <img src="images/places/kreml.jpg" alt="Ярославский Кремль" class="place-image">
    
    <h2>История:</h2>
    <p class="place-description">
    На месте современного храма до 1463 года существовала деревянная церковь Спаса Преображения. Она располагалась рядом с городскими торговыми рядами (как тогда говорили «на городском торгу»), отсюда и пошло ее название «Спаса на Городу».</br>
    Церковь простояла несколько столетий, но во времена Великого пожара 1658 года как и многие другие постройки сгорела.</br>
    Уже через несколько лет, в 1672 году, на месте сгоревшей деревянной церкви построен каменный храм. В 1693 году храм расписывали 22 мастера под руководством Лаврентия Севастьянова, отец которого оформлял Оружейную палату.</br>
    В 1831 году храм был претерпел значительную перестройку, а церковь была заново освящена.
    </p>

    <h3>Архитектура и особенности:</h3>
    <p class="place-description">
      Собор имеет пять куполов, а его фасад украшен величественными резными деталями. Внутри собора сохранились уникальные фрески, выполненные в XVI-XVII веках. Эти фрески считаются одним из лучших образцов русского искусства той эпохи.
    </p>

    <h3>Часы работы:</h3>
    <p class="place-description">
      Ежедневно с 09:00 до 18:00.
    </p>

    <h4>Местоположение:</h4>
    <div class="map-container">
      <!-- Встраиваем Яндекс карту с меткой для местоположения Собора Ильи Пророка -->
      <div id="map"></div>
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

  <!-- Подключаем API Яндекс.Карт -->
  <script type="text/javascript" src="https://api-maps.yandex.ru/2.1/?apikey=dfe7c77a-cb97-4a32-941e-6176cf3ebc3b&lang=ru_RU"></script>
  <script type="text/javascript">
    // Функция инициализации карты
    ymaps.ready(function () {
      var map = new ymaps.Map("map", {
        center: [57.622574, 39.895651], // Координаты Собора Ильи Пророка
        zoom: 16,
      });

      // Создание метки на карте
      var placemark = new ymaps.Placemark([57.622574, 39.895651], {
        balloonContent: "Церковь Спаса на городу", // Текст метки
      }, {
        preset: "islands#icon", // Стиль метки
        iconColor: "#FF0000", // Цвет метки
      });

      // Добавляем метку на карту
      map.geoObjects.add(placemark);
    });
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

</body>
</html>
