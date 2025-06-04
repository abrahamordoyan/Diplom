<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$mysqli = new mysqli("localhost", "root", "", "diplom");
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_type = $_POST['form_type'] ?? '';

    if ($form_type === 'login') {
        // Обработка входа
        $email = $mysqli->real_escape_string($_POST["email"]);
        $password = $_POST["password"];

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // ПРОСТАЯ проверка пароля (без хеширования)
            if ($password === $user['password']) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["surname"] = $user["surname"];
                $_SESSION["role_id"] = $user["role_id"];

                $role_id = $user["role_id"];

                if ($role_id == 1) {
                    $_SESSION['role'] = 'Администратор';
                    header("Location: admin_dashboard.php");
                    exit();
                } elseif ($role_id == 2) {
                    $_SESSION['role'] = 'Пользователь';
                    header("Location: user_dashboard.php");
                    exit();
                } else {
                    $error = "Неизвестная роль пользователя.";
                }
            } else {
                $error = "Неверный email или пароль.";
            }
        } else {
            $error = "Неверный email или пароль.";
        }

        $stmt->close();

    } elseif ($form_type === 'register') {
        // Обработка регистрации
        $name = trim($_POST["name"] ?? "");
        $surname = trim($_POST["surname"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $password_confirm = $_POST["password_confirm"] ?? "";

        if ($name === "" || $surname === "" || $email === "" || $password === "") {
            $error = "Пожалуйста, заполните все поля регистрации.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Неверный формат email.";
        } elseif ($password !== $password_confirm) {
            $error = "Пароли не совпадают.";
        } else {
            $stmt = $mysqli->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Пользователь с таким email уже существует.";
            } else {
                $role_id = 2; // Роль по умолчанию — пользователь

                $stmtInsert = $mysqli->prepare("INSERT INTO users (name, surname, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
                $stmtInsert->bind_param("ssssi", $name, $surname, $email, $password, $role_id);

                if ($stmtInsert->execute()) {
                    $success = "Регистрация прошла успешно! Теперь войдите в систему.";
                } else {
                    $error = "Ошибка при регистрации. Попробуйте позже.";
                }
                $stmtInsert->close();
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Вход и регистрация</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
    }
    .wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px 10px;
    }
    .login-box {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }
    .login-box h2 {
      margin-bottom: 20px;
      text-align: center;
    }
    .login-box input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      margin-bottom: 15px;
      box-sizing: border-box;
    }
    .login-box button {
      background-color: #000000;
      color: white;
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: 600;
    }
    .login-box button:hover {
      background-color: #e6b800;
      color: black;
    }
    .toggle-link {
      text-align: center;
      margin-top: 15px;
      cursor: pointer;
      color: #1a3e8a;
      user-select: none;
      font-weight: 600;
      text-decoration: underline;
    }
    .message {
      text-align: center;
      margin-bottom: 15px;
      color: red;
      font-weight: 600;
    }
    .success-message {
      color: green;
    }
    @media (max-width: 768px) {
      nav ul {
        flex-direction: column;
      }
      .login-box {
        margin-top: 30px;
      }
    }
  </style>
</head>
<body>

<div class="wrapper">

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
      <li><a href="Login.php" class="side-login">Вход</a></li>
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
  <div class="login-btn"><a href="Login.php">Вход</a></div>
</header>

<main>

<div class="login-box" id="loginBox" style="display: <?= $success ? 'none' : 'block' ?>">
  <h2>Вход в систему</h2>
  <?php if ($error && !$success): ?>
    <p class="message" style="margin-bottom: 15px; text-align: center;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="post" id="loginForm">
    <input type="hidden" name="form_type" value="login">

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Пароль:</label>
    <input type="password" name="password" required>

    <button type="submit">Войти</button>
  </form>
  <p class="toggle-link" id="showRegisterForm">Я ещё не зарегистрирован</p>
</div>


  <div class="login-box" id="registerBox" style="display: <?= $success ? 'block' : 'none' ?>">
    <h2>Регистрация</h2>
    <form method="post" id="registerForm">
      <input type="hidden" name="form_type" value="register">

      <label>Имя:</label>
      <input type="text" name="name" required>

      <label>Фамилия:</label>
      <input type="text" name="surname" required>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Пароль:</label>
      <input type="password" name="password" required>

      <label>Подтверждение пароля:</label>
      <input type="password" name="password_confirm" required>

      <button type="submit">Зарегистрироваться</button>
    </form>
    <p class="toggle-link" id="showLoginForm">Уже есть аккаунт? Войти</p>

    <?php if ($success): ?>
      <p class="message success-message" style="margin-top: 15px; text-align: center;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

  </div>

</main>


</div>

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

  document.getElementById('showRegisterForm').addEventListener('click', function() {
    document.getElementById('loginBox').style.display = 'none';
    document.getElementById('registerBox').style.display = 'block';
  });

  document.getElementById('showLoginForm').addEventListener('click', function() {
    document.getElementById('registerBox').style.display = 'none';
    document.getElementById('loginBox').style.display = 'block';
  });
</script>

</body>
</html>
