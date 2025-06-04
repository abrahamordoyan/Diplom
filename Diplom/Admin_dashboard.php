<?php
session_start();

// Проверяем, что пользователь авторизован и является администратором (role_id == 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: Login.php");
    exit();
}

// Подключение к БД
$mysqli = new mysqli("localhost", "root", "", "diplom");
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");

// Удаление пользователя по id
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Защита от удаления самого себя
    if ($delete_id !== $_SESSION['user_id']) {
        $mysqli->query("DELETE FROM users WHERE user_id = $delete_id");
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Вы не можете удалить самого себя!');</script>";
    }
}

// Добавление нового пользователя
if (isset($_POST['add_user'])) {
  $name = $_POST['name'];
  $surname = $_POST['surname'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $role_id = $_POST['role_id'];

  $sql = "INSERT INTO users (name, surname, email, password, role_id) VALUES (?, ?, ?, ?, ?)";
  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param("ssssi", $name, $surname, $email, $password, $role_id);

  if ($stmt->execute()) {
      header("Location: admin_dashboard.php");
      exit();
  } else {
      echo "Ошибка при добавлении пользователя: " . $mysqli->error;
  }
}

// Редактирование пользователя
if (isset($_POST['edit_user'])) {
    $user_id = (int)$_POST['user_id'];
    $name = $mysqli->real_escape_string($_POST['name']);
    $surname = $mysqli->real_escape_string($_POST['surname']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $role_id = (int)$_POST['role_id'];

    // Обновляем данные (если пароль пустой — не обновляем)
    if (empty($password)) {
        $mysqli->query("UPDATE users SET name='$name', surname='$surname', email='$email', role_id=$role_id WHERE user_id=$user_id");
    } else {
        $mysqli->query("UPDATE users SET name='$name', surname='$surname', email='$email', password='$password', role_id=$role_id WHERE user_id=$user_id");
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Получаем всех пользователей для отображения
$result = $mysqli->query("SELECT user_id, name, surname, email, role_id FROM users ORDER BY user_id ASC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Панель администратора</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    /* Стили, аналогичные предыдущим, плюс стили для модального окна редактирования */

   html, body {
     height: 100%;
     margin: 0;
     padding: 0;
     display: flex;
     flex-direction: column;
     min-height: 100vh;
     font-family: Arial, sans-serif;
     background: #f5f5f5;
     color: #222;
   }

   body > main {
     flex: 1;
     max-width: 1100px;
     margin: 20px auto;
     padding: 0 10px;
     position: relative;
   }

   footer {
     background-color: #222;
     color: white;
     padding: 20px;
     flex-shrink: 0;
     display: flex;
     justify-content: space-between;
     align-items: center;
   }

   h1 {
     text-align: center;
     margin-bottom: 30px;
     font-weight: 700;
     position: relative;
   }

   /* Кнопка выйти справа от заголовка */
   .logout-btn-header {
     position: absolute;
     right: 0;
     top: 50%;
     transform: translateY(-50%);
     background-color: #2f3433;
     color: white;
     border: none;
     padding: 10px 20px;
     border-radius: 25px;
     font-weight: bold;
     cursor: pointer;
     transition: background-color 0.3s ease;
   }
   .logout-btn-header:hover {
     background-color: #000000;
   }

   table {
     width: 100%;
     border-collapse: collapse;
     background: white;
     border-radius: 10px;
     box-shadow: 0 2px 8px rgba(0,0,0,0.1);
     margin-bottom: 20px;
   }

   th, td {
     padding: 12px 15px;
     border-bottom: 1px solid #ddd;
     text-align: left;
   }

   th {
    background-color: #f2d462; /* мягкий жёлтый */
    color: #3a2e00; /* очень тёмно-коричневый */
   }

   tr:hover {
     background-color: #f9f9f9;
   }

   /* Кнопка Добавить пользователя под таблицей */
   #showAddUserForm {
      background-color: #000000;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: bold;
      cursor: pointer;
      margin: 0 auto 30px auto;
      display: block;
      text-decoration: none;
      text-align: center;
      width: max-content;
    }

    #showAddUserForm:hover {
      background-color: #e6b800;
      color: black;
    }

   form {
     background: white;
     padding: 20px;
     border-radius: 10px;
     box-shadow: 0 2px 8px rgba(0,0,0,0.1);
     max-width: 600px;
     margin: 0 auto 40px;
   }

   form label {
     display: block;
     margin-bottom: 10px;
   }

   form input[type="text"],
   form input[type="email"],
   form input[type="password"],
   form select {
     width: 100%;
     padding: 8px;
     margin-top: 5px;
     margin-bottom: 15px;
     border: 1px solid #ccc;
     border-radius: 5px;
     box-sizing: border-box;
   }

   form button {
     background-color: #2f3433;
     color: white;
     border: none;
     padding: 10px 18px;
     border-radius: 6px;
     cursor: pointer;
     font-weight: bold;
   }

   form button:hover {
     background-color: #000000;
   }

   .actions a {
     color: #d74c41;
     text-decoration: none;
     margin-right: 10px;
   }

   .actions a:hover {
     text-decoration: underline;
   }

   /* Модальные окна */
   #modalAddUser,
   #modalEditUser {
     display: none; 
     position: fixed;
     z-index: 1000;
     left: 0; top: 0;
     width: 100%; height: 100%;
     background: rgba(0, 0, 0, 0.5);
     justify-content: center;
     align-items: center;
   }

   #modalAddUser.active,
   #modalEditUser.active {
     display: flex;
   }

   .modal-content {
     background: white;
     padding: 20px;
     border-radius: 10px;
     width: 100%;
     max-width: 500px;
     box-shadow: 0 2px 10px rgba(0,0,0,0.25);
     position: relative;
   }

   .close-btn {
     position: absolute;
     top: 10px;
     right: 15px;
     font-size: 20px;
     font-weight: bold;
     color: #999;
     cursor: pointer;
   }

   .close-btn:hover {
     color: #d74c41;
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
      <li><a href="admin_dashboard.php" class="side-login">Админ-панель</a></li>
      <li><a href="logout.php">Выйти</a></li>
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
    <a href="admin_dashboard.php">Личный кабинет</a>
  </div>
</header>

<main>
  <h1>Панель администратора
    <button class="logout-btn-header" onclick="location.href='logout.php'">Выйти из аккаунта</button>
  </h1>
  
  <table>
    <thead>
      <tr>
        <th>ID пользователя</th>
        <th>Имя</th>
        <th>Фамилия</th>
        <th>Email</th>
        <th>Роль</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php while($user = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($user['user_id']) ?></td>
          <td><?= htmlspecialchars($user['name']) ?></td>
          <td><?= htmlspecialchars($user['surname']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= $user['role_id'] == 1 ? 'Администратор' : 'Пользователь' ?></td>
          <td class="actions">
            <a href="#" class="edit-link" data-id="<?= $user['user_id'] ?>" data-name="<?= htmlspecialchars($user['name']) ?>" data-surname="<?= htmlspecialchars($user['surname']) ?>" data-email="<?= htmlspecialchars($user['email']) ?>" data-role="<?= $user['role_id'] ?>">Редактировать</a>
            <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
              | <a href="?delete_id=<?= $user['user_id'] ?>" onclick="return confirm('Удалить пользователя?')">Удалить</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <button id="showAddUserForm">Добавить пользователя</button>

  <!-- Модальное окно добавления пользователя -->
  <div id="modalAddUser">
    <div class="modal-content">
      <span class="close-btn" id="closeAddModal">&times;</span>
      <h2>Добавить нового пользователя</h2>
      <form method="post">
        <label>Имя:
          <input type="text" name="name" required>
        </label>
        <label>Фамилия:
          <input type="text" name="surname" required>
        </label>
        <label>Email:
          <input type="email" name="email" required>
        </label>
        <label>Пароль:
          <input type="password" name="password" required>
        </label>
        <label>Роль:
          <select name="role_id">
            <option value="1">Администратор</option>
            <option value="2" selected>Пользователь</option>
          </select>
        </label>
        <button type="submit" name="add_user">Добавить пользователя</button>
      </form>
    </div>
  </div>

  <!-- Модальное окно редактирования пользователя -->
  <div id="modalEditUser">
    <div class="modal-content">
      <span class="close-btn" id="closeEditModal">&times;</span>
      <h2>Редактировать пользователя</h2>
      <form method="post">
        <input type="hidden" name="user_id" id="edit_user_id">
        <label>Имя:
          <input type="text" name="name" id="edit_name" required>
        </label>
        <label>Фамилия:
          <input type="text" name="surname" id="edit_surname" required>
        </label>
        <label>Email:
          <input type="email" name="email" id="edit_email" required>
        </label>
        <label>Пароль:
          <input type="password" name="password" placeholder="Оставьте пустым, чтобы не менять">
        </label>
        <label>Роль:
          <select name="role_id" id="edit_role">
            <option value="1">Администратор</option>
            <option value="2">Пользователь</option>
          </select>
        </label>
        <button type="submit" name="edit_user">Сохранить изменения</button>
      </form>
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
  <div class="footer-right socials">
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

  // Открытие и закрытие модального окна добавления пользователя
  const openAddUserBtn = document.getElementById('showAddUserForm');
  const modalAddUser = document.getElementById('modalAddUser');
  const closeAddModalBtn = document.getElementById('closeAddModal');

  openAddUserBtn.addEventListener('click', () => {
    modalAddUser.classList.add('active');
  });

  closeAddModalBtn.addEventListener('click', () => {
    modalAddUser.classList.remove('active');
  });

  // Открытие и закрытие модального окна редактирования пользователя
  const modalEditUser = document.getElementById('modalEditUser');
  const closeEditModalBtn = document.getElementById('closeEditModal');

  document.querySelectorAll('.edit-link').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      // Заполняем форму данными пользователя из data-атрибутов
      document.getElementById('edit_user_id').value = link.getAttribute('data-id');
      document.getElementById('edit_name').value = link.getAttribute('data-name');
      document.getElementById('edit_surname').value = link.getAttribute('data-surname');
      document.getElementById('edit_email').value = link.getAttribute('data-email');
      document.getElementById('edit_role').value = link.getAttribute('data-role');
      modalEditUser.classList.add('active');
    });
  });

  closeEditModalBtn.addEventListener('click', () => {
    modalEditUser.classList.remove('active');
  });

  // Закрытие модальных окон при клике вне области модального контента
  window.addEventListener('click', (event) => {
    if (event.target === modalAddUser) {
      modalAddUser.classList.remove('active');
    }
    if (event.target === modalEditUser) {
      modalEditUser.classList.remove('active');
    }
  });
</script>

</body>
</html>
