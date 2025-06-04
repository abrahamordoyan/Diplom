<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Афиша города Ярославль</title>
<link rel="stylesheet" href="styles.css" />
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 0;
    color: #222;
  }

  main {
    max-width: 1100px;
    margin: 20px auto;
    padding: 0 10px;
  }

  .places-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    /* не фиксируем высоту, пусть подстраивается */
  }

  .place-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .place-card img {
    width: 100%;
    height: 220px; /* увеличена высота для полного отображения */
    object-fit: cover;
    border-bottom: 1px solid #ddd;
  }

  .place-content {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .place-title {
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 8px;
  }

  .place-date {
    margin-top: 5px;
    font-size: 13px;
    color: #666;
    line-height: 1.3;
    margin-bottom: 12px;
  }

  .place-link {
    text-align: right;
    margin-top: auto; /* кнопка прижата к низу карточки */
  }

  .place-link a {
    display: inline-block;
    background-color: #2f3433;
    color: white;
    padding: 8px 14px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
    margin-top: 10px; /* Вот этот отступ сверху */
  }


  .place-link a:hover {
    background-color: #000000;
  }

  /* Адаптивность для мобильных */
  @media (max-width: 768px) {
    main {
      max-width: 95%;
      margin: 20px auto;
      padding: 0 10px;
    }
    .places-grid {
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 15px;
    }
    .place-card img {
      height: 180px;
    }
  }

  @media (max-width: 480px) {
    .places-grid {
      grid-template-columns: 1fr;
      gap: 15px;
    }
    .place-card img {
      height: 140px;
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
  <div class="places-grid" id="eventsContainer">
    <!-- Карточки будут создаваться динамически здесь -->
  </div>
</main>

<script>
  function formatDate(dateStr) {
    const days = ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];
    const months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

    const dateObj = new Date(dateStr);
    if (isNaN(dateObj)) return '';

    const day = dateObj.getDate();
    const month = months[dateObj.getMonth()];
    const weekday = days[dateObj.getDay()];
    const hours = dateObj.getHours().toString().padStart(2, '0');
    const minutes = dateObj.getMinutes().toString().padStart(2, '0');

    return `${day} ${month}, ${weekday}, ${hours}:${minutes}`;
  }

  async function loadEvents() {
    try {
      const response = await fetch('afisha.json');
      if (!response.ok) {
        throw new Error('Ошибка загрузки JSON');
      }
      const events = await response.json();

      const container = document.getElementById('eventsContainer');
      container.innerHTML = ''; 

      events.forEach(event => {
        const card = document.createElement('div');
        card.className = 'place-card';

        const genres = event.genres ? event.genres.join(', ') : '';

        const imagePathRaw = event.image || '';
        let imagePath = imagePathRaw;
        if (imagePathRaw && !imagePathRaw.includes('/')) {
          imagePath = 'images/afisha/' + imagePathRaw;
        }

        let imageHtml = '';

        if (imagePath) {
          imageHtml = `<img src="${imagePath}" alt="${event.title || 'Изображение'}" onerror="this.style.display='none'; this.parentNode.querySelector('.no-image').style.display='flex'">`;
        } else {
          imageHtml = `<div class="no-image" style="width:100%; height:220px; background:#ddd; display:flex; align-items:center; justify-content:center; color:#999;">Изображение отсутствует</div>`;
        }

        const formattedDate = event.date ? formatDate(event.date) : '';

        card.innerHTML = `
  ${imageHtml}
  <div class="no-image" style="display:none; width:100%; height:220px; background:#ddd; align-items:center; justify-content:center; color:#999; font-size:14px;">Изображение отсутствует</div>
  <div class="place-content">
    <div style="font-size: 13px; color: #666; margin-bottom: 5px;">
      ${genres} • Возраст ${event.age}
    </div>
    <div class="place-date">${formattedDate}</div>
    <div style="font-weight: 700; font-size: 18px; margin-bottom: 5px;">
      ${event.title}
    </div>
    <div style="font-weight: 700; font-size: 14px; margin-bottom: 10px;">
      ${event.subtitle || ''}
    </div>
    <div style="font-size: 14px; color: #555; margin-bottom: 8px;">
      ${event.venue}
    </div>
    <div style="font-size: 14px; color: #555; font-weight: 600; margin-bottom: 15px;">
      ${event.city}
    </div>
    <div class="place-link">
      <a href="${event.link || '#'}" target="_blank" rel="noopener noreferrer">Купить билет</a>
    </div>
  </div>
`;




        container.appendChild(card);
      });
    } catch (error) {
      console.error('Ошибка:', error);
      document.getElementById('eventsContainer').innerHTML = '<p>Не удалось загрузить мероприятия.</p>';
    }
  }

  loadEvents();
</script>

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
