<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Установка таблицы услуг</h1>";

try {
    $dbPath = __DIR__ . '/config/database.sqlite';
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Создаём таблицу services
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            image TEXT NOT NULL,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Таблица services создана<br>";
    
    // Проверяем, есть ли данные
    $count = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    
    if ($count == 0) {
        // Добавляем тестовые услуги
        $services = [
            ['IT-консалтинг', '/assets/img/services-img-1.webp', 1],
            ['Разработка программного обеспечения', '/assets/img/services-img-2.webp', 2],
            ['Интеграция и поддержка', '/assets/img/services-img-3.webp', 3],
            ['Кибербезопасность', '/assets/img/services-img-4.webp', 4],
            ['Big Data и аналитика', '/assets/img/services-img-5.webp', 5],
            ['Облачные решения', '/assets/img/services-img-1.webp', 6],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO services (title, image, sort_order) VALUES (?, ?, ?)");
        foreach ($services as $service) {
            $stmt->execute($service);
        }
        
        echo "✅ Добавлено " . count($services) . " услуг<br>";
    } else {
        echo "📊 В таблице уже есть $count записей<br>";
    }
    
    // Показываем все услуги
    $services = $pdo->query("SELECT * FROM services ORDER BY sort_order")->fetchAll();
    echo "<h2>Текущие услуги:</h2>";
    echo "<ul>";
    foreach ($services as $service) {
        echo "<li>#" . $service['id'] . " - " . htmlspecialchars($service['title']) . " (сортировка: " . $service['sort_order'] . ")</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<a href='blocks/Services/services.php'>Перейти к блоку услуг</a> | ";
    echo "<a href='admin.php?tab=services'>Перейти в админку (скоро)</a>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Ошибка:</h2>";
    echo $e->getMessage();
}
?>