<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Карта города Ярославля</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    main {
      flex: 1;
      padding: 40px 20px;
      max-width: 1400px;
      margin: 0 auto;
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
    }

    .map-wrapper {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      flex-wrap: nowrap;
    }

    .map-container {
      flex-grow: 1;
      height: 600px;
      border-radius: 10px;
      overflow: hidden;
      min-width: 800px;  /* Оставляем минимальную ширину карты */
    }

    .legend {
      width: 250px;
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
      font-size: 16px;
      height: fit-content;
    }

    .legend .dot {
      height: 10px;
      width: 10px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }

    .legend .blue { background-color: blue; }
    .legend .red { background-color: red; }
    .legend .green { background-color: green; }
    .legend .orange { background-color: orange; }
    .legend .violet { background-color: violet; }


    footer {
      background-color: #1D1D1D;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      font-size: 16px;
      flex-wrap: wrap;
    }

    .footer-left img {
      width: 60px;
      height: 70px;
    }

    .footer-center {
      text-align: center;
      flex-grow: 1;
    }

    .footer-right {
      display: flex;
      gap: 15px;
    }

    .footer-right img {
      width: 50px;
      height: 50px;
    }

    /* Адаптивность */
    @media (max-width: 1200px) {
      .map-wrapper {
        flex-direction: column;
        align-items: center;
      }

      .map-container {
        width: 100%;
        height: 400px;
      }

      .legend {
        width: 100%;
        text-align: center;
      }
    }

    @media (max-width: 900px) {
      .map-container {
        height: 350px;
      }

      .legend {
        font-size: 14px;
        padding: 15px;
      }
    }

    @media (max-width: 500px) {
      .map-container {
        height: 300px;
      }

      .legend {
        font-size: 13px;
        padding: 15px;
      }

      h1 {
        font-size: 1.2em;
      }
    }
  </style>
  <script src="https://api-maps.yandex.ru/2.1/?apikey=dfe7c77a-cb97-4a32-941e-6176cf3ebc3b&lang=ru_RU"></script>
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
    <h1>Карта города Ярославля</h1>
    <div class="map-wrapper">
      <div class="map-container" id="map"></div>
      <div class="legend">
        <strong>Условные обозначения</strong><br><br>
        <span class="dot blue"></span> Музеи<br>
        <span class="dot red"></span> Театры<br>
        <span class="dot green"></span> Памятники<br>
        <span class="dot orange"></span> Храмы и церкви<br>
        <span class="dot violet"></span> Памятники архитектуры

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
    ymaps.ready(init);
    function init() {
      const map = new ymaps.Map("map", {
        center: [57.626074, 39.884470],
        zoom: 13
      });

      const museum1 = new ymaps.Placemark([57.621637, 39.888574], {
        balloonContent: `
          <strong>Музей Заповедник</strong><br>
          <img src="/Diplom/images/map/museums/Zapovednik.jpg" width="150" alt="Музей Заповедник"><br>
          Адрес: Богоявленская площадь, 25, <br>
          Ближайшая остановка: Богоявленская площадь
        `
      }, { preset: 'islands#blueDotIcon' });

      const museum2 = new ymaps.Placemark([57.628283, 39.897189], {
        balloonContent: `
          <strong>Ярославский художественный музей</strong><br>
          <img src="/Diplom/images/map/museums/museum2.jpg" width="150" alt="Ярославский художественный музей"><br>
          Адрес: Волжская наб., 23, <br>
          Ближайшая остановка: Красная площадь
        `
      }, { preset: 'islands#blueDotIcon' });

      const theatre1 = new ymaps.Placemark([57.627059, 39.898601], {
        balloonContent: `
          <strong>Музей истории города Ярославля</strong><br>
          <img src="/Diplom/images/map/museums/museum3.jpg" width="150" alt="Музей истории города Ярославля"><br>
          Адрес: Волжская наб., 17/1, <br>
          Ближайшая остановка: Богоявленская площадь
        `
      }, { preset: 'islands#blueDotIcon' });

      const theatre2 = new ymaps.Placemark([57.633218, 39.831700], {
        balloonContent: `
          <strong>Музей боевой славы</strong><br>
          <img src="/Diplom/images/map/museums/museum4.png" width="150" alt="Музей боевой славы"><br>
          Адрес: Угличская ул., 44А <br>
          Ближайшая остановка: Улица Жукова
        `
      }, { preset: 'islands#blueDotIcon' });

      map.geoObjects
        .add(museum1)
        .add(museum2)
        .add(theatre1)
        .add(theatre2);
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

</body>
</html>
