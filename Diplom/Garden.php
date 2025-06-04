<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Губернаторский сад</title>
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
    <h1>Губернаторский сад</h1>
    <img src="images/places/gubernatorskiy-sad.png" alt="Губернаторский сад" class="place-image">
    
    <h2>История:</h2>
    <p class="place-description">
    С 1821 по 1917 годы в Губернаторском доме жили и правили 17 губернаторов. Здесь принимали императоров, членов семьи Романовых, а также иностранных путешественников и известных деятелей Российской империи.</br>
    В 1917 году в этом здании была провозглашена Советская власть. На протяжении многих десятилетий здесь располагались: уездный исполком, Высшая партийная школа, затем – Технологический институт. </br>
    В 1969 году здание передали Ярославскому художественному музею. Здесь открылись постоянные экспозиции.</br>
    Ярославскому художественному музею передан Губернаторский сад. Реконструкция парка, являющегося продолжением усадьбы, длилась 5 лет. С 1994 года открыта сезонная экспозиция «Скульптура в пленэре».  </br>
    Зданию возвращен статус Губернаторского дома. Здесь проходят официальные приемы, торжественные церемонии с участием Губернатора и Правительства Ярославской области. 
    </p>

    <h3>Архитектура и особенности:</h3>
    <p class="place-description">
    Территория сада оформлена в стиле регулярного парка XIX века. Здесь находятся кованые скамейки, старинные фонари и живописные дорожки. Летом в саду работают открытые выставки, проходят концерты камерной музыки и творческие вечера.    </p>


    <h3>Часы работы:</h3>
    <p class="place-description">
    Музей: вт-вс 10:00-18:00.</br>
    Губернаторский дом и Музей зарубежного искусства: вт-вс 10:00-18:00.</br>
    Губернаторский сад: пн-вс 10:00-20:00.</br>
    Дом-музей Опекушина: пт-вс 12:00-18:00, другие дни по записи.</br>
    Понедельник и последний вторник месяца - выходной.    
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
        center: [57.627865, 39.896158], // Координаты Собора Ильи Пророка
        zoom: 16,
      });

      // Создание метки на карте
      var placemark = new ymaps.Placemark([57.627865, 39.896158], {
        balloonContent: "Губернаторский сад", // Текст метки
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
