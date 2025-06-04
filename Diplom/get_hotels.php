<?php
// Указываем путь к файлу hotels.json
$json_file = __DIR__ . '/hotels.json';

// Проверяем, существует ли файл
if (file_exists($json_file)) {
    // Чтение содержимого файла
    $json_data = file_get_contents($json_file);

    // Устанавливаем правильный заголовок для отдачи JSON
    header('Content-Type: application/json');

    // Отправляем данные в формате JSON
    echo $json_data;
} else {
    // Если файл не найден, возвращаем ошибку
    echo json_encode(['error' => 'Файл с данными не найден']);
}
?>
