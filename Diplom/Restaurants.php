<?php
session_start();


// Подключение к базе данных
$mysqli = new mysqli("localhost", "root", "", "diplom"); // Замените данные для подключения

// Проверка на ошибки соединения
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Получаем все рестораны из базы данных
$result = $mysqli->query("SELECT * FROM restaurants");

// Если есть ошибка при запросе
if (!$result) {
    die("Error: " . $mysqli->error);
}

// Чтение всех ресторанов
$restaurants = [];
while ($row = $result->fetch_assoc()) {
    $restaurants[] = $row;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Рестораны в Ярославле</title>
  <link rel="stylesheet" href="styles.css">
  <style>

    .restaurants-container h1, .restaurants-container p {
      margin-bottom: 30px; /* Отступ снизу */
    }

    .restaurants-container {
      max-width: 1100px;
      margin: 40px auto;
      padding: 0 15px;
    }
    .restaurant-card {
      position: relative;
      background: white;
      border-radius: 15px;
      box-shadow: 0 3px 15px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      width: calc(33.33% - 20px);
      margin-right: 20px;
      cursor: pointer;
      /* border: 1px solid #000000;  */
      display: flex;
      flex-direction: column; /* Вертикальное размещение элементов */
      height: 500px; /* Установим фиксированную высоту для карточек */
      margin-bottom: 20px; /* Добавим отступ снизу между карточками */
    }
    .restaurant-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .restaurant-info {
      padding: 10px;
      text-align: center;
      display: flex;
      flex-direction: column;
      gap: 10px; /* Отступ между элементами */
      flex-grow: 1;
    }

    .restaurant-info .title {
      font-weight: bold;
      font-size: 20px;
      margin-bottom: 10px;
    }

    .restaurant-info .cuisine {
      font-size: 16px;
      margin-bottom: 10px;
    }

    .restaurant-info .description {
      font-size: 14px;
      color: #666;
      margin-bottom: auto; /* Гарантирует, что текст не будет растягивать кнопку */
    }
    

    .restaurant-info a {
  bottom: 10px;
  right: 10px;
  background-color: white; /* Белый фон */
  color: black; /* Черный цвет текста */
  padding: 10px 15px; /* Отступы сверху и снизу 10px, слева и справа 15px */
  text-decoration: none;
  border-radius: 20px; /* Закругленные углы */
  border: 2px solid black; /* Черная обводка */
  font-weight: bold; /* Жирный текст */
    }

    .restaurant-info a:hover {
      background-color: #f0f0f0; /* Легкий серый фон при наведении */
  color: black; /* Текст остается черным */
  border-color: black; /* Обводка тоже остается черной */
    }


    .admin-button {
  background-color: #000000; /* темно-синий */
  color: white;
  padding: 8px 15px;
  border: none;
  border-radius: 15px;
  cursor: pointer;
  margin: 5px 0;
  text-decoration: none;
  display: inline-block;
  flex: 1; /* равная ширина */
  text-align: center;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.admin-button:hover {
  background-color: #e6b800; /* чуть темнее при наведении */
  color: black;

}

.delete-button {
  background-color: #000000; /* Исходный цвет, если хочешь сделать чёрным */
  color: white;
  padding: 8px 15px;
  border: none;
  border-radius: 15px;
  cursor: pointer;
  margin: 5px 0;
  text-decoration: none;
  display: inline-block;
  flex: 1;
  text-align: center;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.delete-button:hover {
  background-color: #e30016; /* Красный */
  color: white;
}


.restaurant-buttons {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 10px;
  cursor: default;
}


    footer {
      position: relative;
      bottom: 0;
      width: 100%;
      background-color: #1D1D1D;
      color: white;
      padding: 20px;
      text-align: center;
      box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
      font-size: 16px;
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
    @media (max-width: 768px) {
      .restaurant-card {
        width: 100%;
        margin-right: 0;
      }
    }

    /* Модальное окно */
    .modal {
      display: none;
      position: fixed;
      z-index: 999; /* Убедитесь, что модальное окно поверх всего */
      left: 50%; /* Центрируем окно по горизонтали */
      top: 50%;  /* Центрируем окно по вертикали */
      transform: translate(-50%, -50%); /* Сдвигаем окно на 50% от его размеров */
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7); /* Прозрачный черный фон для затемнения */
    }

    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 15px;
      border: 1px solid #888;
      width: 80%;
      max-width: 900px;
      display: flex;
      flex-direction: column;
      gap: 5px;
      border-radius: 10px;
    }


    .modal-header {
      font-size: 24px;
      font-weight: bold;
      color: #333;
      text-align: center;
      margin-bottom: 10px;
    }

    .modal-body {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .modal-body img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .modal-body p {
      font-size: 14px;
      line-height: 1.4; 
      color: #555;
      margin: 5px 0; 
    }

    .modal-body strong {
      font-weight: bold;
      margin-bottom: 5px; 
    }

    .modal-body .functions,
    .modal-body .specialized-menu {
      margin-top: 20px;
      font-size: 14px;
      color: #333;
      font-weight: normal;
    }

    .modal-body .address {
      font-size: 14px;
      color: #555;
      margin-top: 15px;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .close {
      color: #aaa;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
    }

    button {
      padding: 10px 20px;
      background-color: #ff5733;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #ff2e00;
    }

    #formModal .modal-content {
  max-width: 700px;
  padding: 20px;
  box-sizing: border-box;
  border-radius: 10px;
  background-color: #fff;
}

#formModal form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px 30px;
  align-items: center;
}

#formModal label {
  font-weight: 600;
  margin-bottom: 5px;
  display: block;
  text-align: right;
  padding-right: 10px;
}

#formModal input[type=text],
#formModal textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  resize: vertical;
  box-sizing: border-box;
}

#formModal textarea {
  grid-column: 2 / 3; /* чтобы текстовое поле описания занимало всю правую колонку */
  height: 100px;
}

#formModal .modal-footer {
  grid-column: 1 / 3;
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 20px;
}

#formModal button {
  padding: 10px 25px;
  font-weight: 600;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  background-color: #1a2a56;
  color: white;
  transition: background-color 0.3s ease;
}

#formModal button:hover {
  background-color: #11204a;
}

#formModal .cancel-btn {
  background-color: #777;
}

#formModal .cancel-btn:hover {
  background-color: #555;
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

<main class="restaurants-container">
  <h1 style="text-align:center;">Рестораны в Ярославле</h1>
  <p style="text-align:center;">Попробуйте блюда русской, европейской и других кухонь в лучших ресторанах Ярославля</p>

  <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
    <div style="text-align:center; margin-bottom: 20px;">
    <button class="admin-button" onclick="openFormModal()">Добавить ресторан</button>
    </div>
  <?php endif; ?>

  <?php if (!empty($restaurants)): ?>
    <div style="display: flex; flex-wrap: wrap;">
      <?php foreach ($restaurants as $restaurant): ?>
        <div class="restaurant-card" data-id="<?= $restaurant['id_restaurant'] ?>" onclick="openModal(<?= $restaurant['id_restaurant'] ?>)">
          <img src="images/restaurants/<?= htmlspecialchars($restaurant['image_restaurant']) ?>" alt="<?= htmlspecialchars($restaurant['name']) ?>">
          <div class="restaurant-info">
            <p class="title"><?= htmlspecialchars($restaurant['name']) ?></p>
            <p class="cuisine">Тип кухни: <?= htmlspecialchars($restaurant['cuisine']) ?></p>
            <p class="description"><?= htmlspecialchars($restaurant['description']) ?></p>
            <a href="javascript:void(0)">Подробнее</a>

            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
              <form method="POST" action="add_favorite_restaurant.php" onClick="event.stopPropagation();" style="margin-top: 10px;">
                <input type="hidden" name="id_restaurant" value="<?= $restaurant['id_restaurant'] ?>">
                <button type="submit" class="admin-button">Добавить в избранное</button>
              </form>
            <?php endif; ?>


            <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>
              <div class="restaurant-buttons" onclick="event.stopPropagation();">
              <button class="admin-button" onclick="event.stopPropagation(); openFormModal(<?= $restaurant['id_restaurant'] ?>)">Редактировать</button>

                
              <button class="admin-button delete-button" onclick="if(confirm('Удалить ресторан?')) window.location.href='add_edit_restaurant.php?delete_id=<?= $restaurant['id_restaurant'] ?>'; event.stopPropagation();">Удалить</button>

              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center;">Нет доступных ресторанов.</p>
  <?php endif; ?>
</main>

<!-- Модальное окно -->
<div id="restaurantModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <div class="modal-header" id="modalTitle">Название ресторана</div>
    <div class="modal-body">
      <div>
        <img id="modalImage" src="" alt="Фото ресторана">
      </div>
      <div>
        <p class="functions"><strong>О ресторане:</strong> <span id="modalDescription"></span></p>
        <p class="functions"><strong>Тип кухни:</strong> <span id="modalCuisine"></span></p>
        <p class="functions"><strong>Виды блюд:</strong> <span id="modalFunctions"></span></p>
        <p class="specialized-menu"><strong>Специализированное меню:</strong> <span id="modalSpecializedMenu"></span></p>
        <p class="functions"><strong>Адрес:</strong> <span id="modalAddress"></span></p>
        <p class="functions"><strong>Время работы:</strong> <span id="modalWorkingHours"></span></p>
        <p class="functions"><strong>Телефон:</strong> <span id="modalPhone"></span></p>
      </div>
    </div>
    <div class="modal-footer">
      <a id="menuLink" href="#" target="_blank">
          <button>Посмотреть меню</button>
      </a>
    </div>
  </div>
</div>

<div id="formModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeFormModal()">&times;</span>
    <div class="modal-header" id="formModalTitle">Добавить ресторан</div>
    <form id="restaurantForm" method="POST" action="add_edit_restaurant.php">
      <input type="hidden" name="id_restaurant" id="restaurantId" value="">
      <label for="name">Название:</label>
      <input type="text" id="name" name="name" required>
      <label for="cuisine">Тип кухни:</label>
      <input type="text" id="cuisine" name="cuisine" required>
      <label for="description">Описание:</label>
      <textarea id="description" name="description" rows="4" required></textarea>
      <label for="image_restaurant">Имя изображения (файл в папке images/restaurants):</label>
      <input type="text" id="image_restaurant" name="image_restaurant" required>
      <label for="types_of_dishes">Виды блюд:</label>
      <input type="text" id="types_of_dishes" name="types_of_dishes">
      <label for="specialized_menu">Специализированное меню:</label>
      <input type="text" id="specialized_menu" name="specialized_menu">
      <label for="image_food">Имя изображения блюда (image_food):</label>
      <input type="text" id="image_food" name="image_food">
      <label for="adress">Адрес:</label>
      <input type="text" id="adress" name="adress">
      <label for="working_hours">Время работы:</label>
      <input type="text" id="working_hours" name="working_hours">
      <label for="telephone">Телефон:</label>
      <input type="text" id="telephone" name="telephone">
      <label for="url">Ссылка на меню:</label>
      <input type="text" id="url" name="url">
      <label for="tags">Теги (через запятую):</label>
      <input type="text" id="tags" name="tags">


      <div class="modal-footer">
        <button type="submit" class="admin-button">Сохранить</button>
        <button type="button" class="admin-button cancel-btn" onclick="closeFormModal()">Отмена</button>
      </div>
    </form>
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

  function openModal(id) {
  // Запрос к серверу для получения данных о ресторане по ID
  fetch(`getRestaurantDetails.php?id=${id}`)
      .then(response => response.json())
      .then(data => {
          // Заполняем модальное окно данными о ресторане
          document.getElementById('modalTitle').textContent = data.name;
          document.getElementById('modalDescription').textContent = data.description;
          document.getElementById('modalCuisine').textContent = data.cuisine;
          document.getElementById('modalFunctions').textContent = data.types_of_dishes;
          document.getElementById('modalSpecializedMenu').textContent = data.specialized_menu;
          document.getElementById('modalAddress').textContent = data.adress;
          document.getElementById('modalWorkingHours').textContent = data.working_hours;
          document.getElementById('modalPhone').textContent = data.telephone;
          document.getElementById('modalImage').src = `images/restaurants/${data.image_food}`;
          document.getElementById('tags').value = data.tags || '';


          // Передаем ссылку на меню ресторана
          document.getElementById('menuLink').href = data.url;

          // Открываем модальное окно
          document.getElementById('restaurantModal').style.display = 'block';
      })
      .catch(error => console.error('Error fetching restaurant details:', error));
}
  function closeModal() {
    document.getElementById('restaurantModal').style.display = 'none';
  }


  // Открыть модальное окно добавления/редактирования
function openFormModal(id) {
  if (!id) {
    // Добавление нового ресторана
    document.getElementById('formModalTitle').textContent = 'Добавить ресторан';
    document.getElementById('restaurantForm').reset();
    document.getElementById('restaurantId').value = '';
  } else {
    // Редактирование — загрузка данных ресторана
    fetch(`getRestaurantDetails.php?id=${id}`)
      .then(response => response.json())
      .then(data => {
        document.getElementById('formModalTitle').textContent = 'Редактировать ресторан';
        document.getElementById('restaurantId').value = data.id_restaurant;
        document.getElementById('name').value = data.name;
        document.getElementById('cuisine').value = data.cuisine;
        document.getElementById('description').value = data.description;
        document.getElementById('image_restaurant').value = data.image_restaurant || data.image_food;
        document.getElementById('types_of_dishes').value = data.types_of_dishes;
        document.getElementById('specialized_menu').value = data.specialized_menu;
        document.getElementById('image_food').value = data.image_food || '';
        document.getElementById('adress').value = data.adress;
        document.getElementById('working_hours').value = data.working_hours;
        document.getElementById('telephone').value = data.telephone;
        document.getElementById('url').value = data.url;
        document.getElementById('tags').value = data.tags;
      })
      .catch(err => {
        alert('Ошибка загрузки данных ресторана');
        console.error(err);
      });
  }
  document.getElementById('formModal').style.display = 'block';
}

// Закрыть модальное окно формы
function closeFormModal() {
  document.getElementById('formModal').style.display = 'none';
}

</script>

</body>
</html>