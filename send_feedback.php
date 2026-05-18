<?php
// Включаем отображение всех ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Подключаем базу данных
try {
    $dbPath = __DIR__ . '/config/database.sqlite';
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка подключения к БД: ' . $e->getMessage()]);
    exit;
}

// Получаем данные
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Нет данных']);
    exit;
}

// Проверяем обязательные поля
$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$email = trim($input['email'] ?? '');
$message = trim($input['message'] ?? '');

if (empty($name)) {
    echo json_encode(['success' => false, 'error' => 'Введите имя']);
    exit;
}

if (empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Введите телефон']);
    exit;
}

if (empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Введите email']);
    exit;
}

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Введите сообщение']);
    exit;
}

// Сохраняем в базу
try {
    $stmt = $pdo->prepare("INSERT INTO requests (name, phone, email, message, status, created_at) VALUES (?, ?, ?, ?, 'new', datetime('now'))");
    $result = $stmt->execute([$name, $phone, $email, $message]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Сообщение успешно отправлено!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка при сохранении']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Ошибка БД: ' . $e->getMessage()]);
}
?>