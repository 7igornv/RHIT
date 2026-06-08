<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Установка таблицы Hero-блока</h1>";

try {
    $dbPath = __DIR__ . '/config/database.sqlite';
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Создаём таблицу hero_settings
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hero_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL DEFAULT 'ООО \"РХИТ\"',
            subtitle TEXT NOT NULL DEFAULT 'Современная динамично развивающаяся IT-компания',
            button_text TEXT NOT NULL DEFAULT 'Оставить заявку',
            description TEXT NOT NULL DEFAULT 'Предоставляем сервисы в области информационных технологий. Управляем масштабной инфраструктурой и обеспечиваем ее бесперебойную работу',
            background_image TEXT DEFAULT '/assets/img/hero-bg.jpg',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Таблица hero_settings создана<br>";
    
    // Проверяем, есть ли данные
    $count = $pdo->query("SELECT COUNT(*) FROM hero_settings")->fetchColumn();
    
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO hero_settings (title, subtitle, button_text, description, background_image) 
            VALUES (
                'ООО \"РХИТ\"', 
                'Современная динамично развивающаяся IT-компания', 
                'Оставить заявку', 
                'Предоставляем сервисы в области информационных технологий. Управляем масштабной инфраструктурой и обеспечиваем ее бесперебойную работу', 
                '/assets/img/hero-bg.jpg'
            )
        ");
        echo "✅ Добавлены настройки Hero-блока по умолчанию<br>";
    } else {
        echo "📊 Настройки уже существуют<br>";
    }
    
    echo "<hr>";
    echo "<a href='blocks/Hero/hero.php'>Перейти к Hero-блоку</a> | ";
    echo "<a href='admin/index.php?tab=hero'>Перейти в админку</a>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Ошибка:</h2>";
    echo $e->getMessage();
}
?>