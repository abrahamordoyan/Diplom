<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Собор Ильи Пророка</title>
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
    <h1>Собор Ильи Пророка</h1>
    <img src="images/places/sobor-iliyi-proroka.jpg" alt="Собор Ильи Пророка" class="place-image">
    
    <h2>История:</h2>
    <p class="place-description">
    Каменную церковь возвели в середине XVII века на участке земли, принадлежавшем купцам Скрипиным. Рядом располагались их палаты и хозпостройки. Именно богатейшие предприниматели Ярославля и стали инициаторами и главными спонсорами возведения великолепного белокаменного храма. Братья Вонифатий и Иоанникий Скрипины торговали мехом, драгоценными камнями и серебряной утварью не только в стране, но и за границей. При этом купцы были уважаемы в городе — один из братьев занимал пост земского старосты. Скрипины взяли на себя все расходы по строительству церкви Ильи Пророка и не поскупились на него: они приглашали лучших зодчих и художников, оплачивали роскошное убранство храма и жертвовали иконы.
    </p>

    <h3>Архитектура и особенности:</h3>
    <p class="place-description">
    Пятиглавую церковь Ильи Пророка возвели по образцу византийских соборов. Для его возведения использовали красный крупный кирпич, а для отделки — белый камень, и лишь в XVIII веке строение побелили.</br>
    У старинного ярославского храма сложная асимметричная планировка. Центральное помещение стоит на подклете (нижнем полуподвальном этаже) и увенчано пятью главами на барабанах, украшенных кокошниками и узорчатыми поясами. Вокруг основного объёма расположены пристройки разных форм и размеров: три придела, трапезная, два крыльца, колокольня и галереи, которые опоясывают церковь с трёх сторон.</br>
    Помимо центральной части, самыми заметными в архитектурном ансамбле являются звонница и Ризоположенский придел. Четырёхъярусная шатровая колокольня с восьмигранным основанием украшена наличниками и декоративными лопатками, а завершается она луковкой и крестом. Один из приделов тоже увенчан ажурным восьмигранным шатром, а два нижних яруса украшены волнами кокошников, что придаёт строению визуальной лёгкости и изящности.</br>
    Впрочем, заметить интересные детали можно на всех фасадах церкви Ильи Пророка. Пышный и разнообразный декор есть почти повсюду. На внешних стенах увидите различные декоративные элементы (арки, наличники, квадратные ширинки, бусины и резные вставки), а на галереях и парапетах — керамическую плитку с двуглавым орлом, сказочными животными и птицами.
    </p>

    <h3>Часы работы:</h3>
    <p class="place-description">
    Посетить старинный храм можно только с начала мая и до середины октября. В это время церковь открыта с утра до 19:30.
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
        center: [57.626759, 39.894358], // Координаты Собора Ильи Пророка
        zoom: 16,
      });

      // Создание метки на карте
      var placemark = new ymaps.Placemark([57.626759, 39.894358], {
        balloonContent: "Собор Ильи Пророка", // Текст метки
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
