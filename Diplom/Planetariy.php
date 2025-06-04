<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Планетарий имени В.В. Терешковой</title>
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
    <h1>Планетарий имени В.В. Терешковой</h1>
    <img src="images/places/planetariy.jpg" alt="Планетарий имени В.В. Терешковой" class="place-image">
    
    <h2>История:</h2>
    <p class="place-description">
    Планетарий им. Валентины Терешковой — современный научно-просветительский центр, посвящённый космосу и астрономии. Он назван в честь первой женщины-космонавта, уроженки Ярославской области. Это один из самых технологически продвинутых планетариев в России.    </p>

    <h3>Архитектура и особенности:</h3>
    <p class="place-description">
    Архитектура культурно-просветительского центра в Ярославле поражает: он представляет собой огромный стеклянный купол, высота которого 10 м и диаметр — 12 м, окруженный основным зданием, огибающим его полукругом. Стены учреждения украшены астрологическими символами планет и созвездий. Сверху возвышается башня обсерватории.</br>
В Ярославле этот планетарий не первый. С 1948 года в помещении храма Покрова Богородицы Казанского женского монастыря действовал прежний. Однако со временем здание отдали епархии. К тысячелетию города было принято решение возобновить деятельность планетария. Открытие прошло 07 апреля 2011 года.
   </p>

   

    <h3>Часы работы:</h3>
    <p class="place-description">
      Ежедневно с 09:00 до 21:00.
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
        center: [57.621089, 39.869884], // Координаты Собора Ильи Пророка
        zoom: 16,
      });

      // Создание метки на карте
      var placemark = new ymaps.Placemark([57.621089, 39.869884], {
        balloonContent: "Планетарий имени В.В. Терешковой", // Текст метки
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
