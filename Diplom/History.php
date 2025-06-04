<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>История города Ярославля</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .history-page {
      padding: 40px 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .history-block {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      margin-bottom: 60px;
      flex-wrap: wrap;
    }
    
    .page-title {
      text-align: center;
      margin-bottom: 40px;
    }

    .page-title h1 {
      font-size: 32px;
    }

    .history-block .text {
      width: 60%;
      line-height: 1.6;
      text-align: justify;
    }

    .history-block img {
      width: 38%; /* чуть меньше, чтобы было красиво */
    }

    .fact {
      margin-top: 10px;
      font-style: italic;
      color: #444;
    }

    .reverse {
      flex-direction: row-reverse;
    }

    @media (max-width: 768px) {
      .history-block, .reverse {
        flex-direction: column !important;
      }

      .history-block img,
      .history-block .text {
        width: 100%;
      }

      .history-block {
        text-align: left;
      }
    }
  </style>
</head>
<body>

  <!-- Header с боковым меню как на главной -->
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

  <!-- Main -->
    <main class="history-page">
        <div class="page-title">
            <h1>История города Ярославля</h1>
        </div>

        <div class="history-block">
            <div class="text">
              <h2>История города</h2>
              <p>Ярославль — это один из старейших и величайших городов России, с богатым историческим наследием. Его история охватывает более 1000 лет, начиная с основания города в XI веке. Ярославль стал не только важным торговым и культурным центром, но и хранителем множества архитектурных памятников, которые до сих пор привлекают туристов со всего мира. Его уникальная атмосфера сочетает в себе древние традиции и современность, что делает его одним из самых значимых культурных и исторических центров страны.</p>
            </div>
            <img src="images/places/yaroslawl-old.jpg" alt="История Ярославля">
          </div>

        <div class="history-block reverse">
        <div class="text">
            <h2>Основание города Ярославля (XI век)</h2>
            <p>В 1010 году на месте селения Медвежий гром при слиянии рек Которосли и Волги ростовским князем Ярославом Мудрым был заложен город, основной задачей которого была охрана пути от Волги к Ростову Великому. В летописи Ярославль упоминается с 1071 года. Предание рассказывает, что на месте будущего города было языческое мерянское святилище, где князь зарубил священную медведицу. Эта легенда объясняет происхождение ярославского герба (медведь с секирой в лапах), известного по изображениям с XVII века.</p>
        </div>
        <img src="images/places/yaroslw_mudr.jpg" alt="Ярослав Мудрый">
        </div>

        <div class="history-block">
        <div class="text">
            <h2>Средневековье (XII–XV века)</h2>
            <p>В 1218 году Ярославль достался в удел второму сыну князя Константина – Всеволоду-Иоанну и стал столицей княжества. В 1463 году Ярославль присоединился к Московскому княжеству. XVI век стал для Ярославля временем нового экономического и культурного подъема. После пожара 1501 года в городе возобновляется каменное строительство. Отстраиваются заново рухнувший в огне городской Успенский собор и пришедший в негодность собор Спасо-Преображенского монастыря. После пожара 1536 года город получает новую систему укреплений: к деревянному Рубленому городу на Стрелке прибавляется Земляной город – ядро посада, обнесенное валом и обведенное рвом. К посаду, преимущественно вдоль Волги и Которосли, примыкают многочисленные слободы.</p>
        </div>
        <img src="images/places/yaroslawl-old2.jpg" alt="Карта Ярославля">
        </div>

        <div class="history-block reverse">
        <div class="text">
            <h2>Золотой век Ярославля (XVII–XIX века)</h2>
            <p>XVII век – время расцвета Ярославля. Этот период считается «золотым веком» в истории города. Ярославль сыграл важную роль в период Смутного времени. Именно здесь был создан «Совет всея земли» — правительство из представителей духовенства и боярской думы, дворян и посадских людей. На время Ярославль принял на себя роль столицы русского государства. Город быстро оправился от последствий Смуты и стал одним из важнейших торговых и ремесленных центров. В Ярославле проживала шестая часть наиболее влиятельного купечества - «гостей государевой сотни», в руках которых была сосредоточена торговля со странами Запада и Востока.</p>
        </div>
        <img src="images/places/yaroslawl-old3.jpg" alt="Церковь XVII века">
        </div>

        <div class="history-block">
        <div class="text">
            <h2>Советский период (XX век)</h2>
            <p>В начале XX века Ярославль был одним из наиболее крупных городов Центральной России (12-е место по числу жителей в пределах современной территории страны на 1897 год). Была значительно развита промышленность — работало более 50 предприятий с 15 тысячами рабочих, по числу которых город занимал 8-е место среди центров фабрично-заводской промышленности Европейской России. Преобладали текстильная, пище-вкусовая, химическая отрасли. Главные фабрики: две мануфактуры бумажной и льняной пряжи и тканей (производство на 2,16 млн руб. при 1 560 рабочих), табачная (открыта Дунаевым в 1850 году) (на 2,6 млн руб. при 940 раб.); заводы химические (на 0,6 млн руб., при 440 рабочих), спичечный (на 0,47 млн руб. при 350 рабочих), лесопильные, плотничные, столярные, бондарные, мыловаренные, водочные, колокольный, войлочные и валеночные, кожевенные, скорняжный и воскобойный, каждый с производством от 0,1 до 0,28 млн руб. В 1900 году в городе открылась первая электростанция.  </p>
        </div>
        <img src="images/places/yaroslawl-old4.jpg" alt="Советский период">
        </div>
    </main>

  <!-- Footer -->
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

  <!-- Script -->
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
