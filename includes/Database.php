<?php
class Database {
    private $pdo;
    
    public function __construct() {
        $dbPath = __DIR__ . '/../config/database.sqlite';
        
        // Создаём папку config, если её нет
        $configDir = __DIR__ . '/../config';
        if (!is_dir($configDir)) {
            mkdir($configDir, 0777, true);
        }
        
        // Подключаемся к БД (файл создастся автоматически)
        $this->pdo = new PDO("sqlite:" . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Создаём таблицы, если их нет
        $this->createTables();
    }
    
    private function createTables() {
        // Таблица content_blocks (для контента)
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS content_blocks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                block_key TEXT UNIQUE NOT NULL,
                block_name TEXT NOT NULL,
                content TEXT,
                type TEXT DEFAULT 'text',
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Таблица requests (для заявок)
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                phone TEXT NOT NULL,
                email TEXT,
                message TEXT,
                status TEXT DEFAULT 'new',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Проверяем, есть ли данные в content_blocks
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM content_blocks");
        $count = $stmt->fetchColumn();
        
        // Если таблица пустая, добавляем начальные блоки
        if ($count == 0) {
            $this->insertInitialBlocks();
        }
    }
    
    private function insertInitialBlocks() {
        $blocks = [
            ['hero_title', 'Заголовок Hero блока', 'Добро пожаловать в нашу компанию', 'text'],
            ['hero_subtitle', 'Подзаголовок Hero блока', 'Мы предоставляем лучшие услуги', 'text'],
            ['hero_button', 'Текст кнопки Hero', 'Оставить заявку', 'text'],
            ['services_title', 'Заголовок блока Услуги', 'Наши услуги', 'text'],
            ['about_text', 'Текст о компании', '<h2>О нас</h2><p>Мы профессионалы своего дела</p>', 'html'],
            ['footer_copyright', 'Копирайт в подвале', '© 2024 Все права защищены', 'text'],
        ];
        
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO content_blocks (block_key, block_name, content, type) VALUES (?, ?, ?, ?)");
        foreach ($blocks as $block) {
            $stmt->execute($block);
        }
    }
    
    // ========== МЕТОДЫ ДЛЯ КОНТЕНТА ==========
    
    public function getContent($blockKey) {
        $stmt = $this->pdo->prepare("SELECT content FROM content_blocks WHERE block_key = ?");
        $stmt->execute([$blockKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['content'] : '';
    }
    
    public function getAllContentBlocks() {
        $stmt = $this->pdo->query("SELECT * FROM content_blocks ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateContentBlock($blockKey, $content) {
        $stmt = $this->pdo->prepare("UPDATE content_blocks SET content = ?, updated_at = CURRENT_TIMESTAMP WHERE block_key = ?");
        return $stmt->execute([$content, $blockKey]);
    }
    
    // ========== МЕТОДЫ ДЛЯ ЗАЯВОК ==========
    
    public function saveRequest($name, $phone, $email, $message) {
        $stmt = $this->pdo->prepare("INSERT INTO requests (name, phone, email, message, status, created_at, updated_at) VALUES (?, ?, ?, ?, 'new', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        return $stmt->execute([$name, $phone, $email, $message]);
    }
    
    public function getAllRequests() {
        $stmt = $this->pdo->query("SELECT * FROM requests ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRequestStatusCounts() {
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM requests
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateRequestStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE requests SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
    
    public function getRequestById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM requests WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function deleteRequest($id) {
        $stmt = $this->pdo->prepare("DELETE FROM requests WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ========== МЕТОДЫ ДЛЯ УСЛУГ ==========
    public function getAllServices() {
        $stmt = $this->pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveServices() {
        $stmt = $this->pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addService($title, $image) {
        $stmt = $this->pdo->prepare("INSERT INTO services (title, image, sort_order) VALUES (?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM services))");
        return $stmt->execute([$title, $image]);
    }

    public function updateService($id, $title, $image, $is_active = 1) {
        $stmt = $this->pdo->prepare("UPDATE services SET title = ?, image = ?, is_active = ? WHERE id = ?");
        return $stmt->execute([$title, $image, $is_active, $id]);
    }

    public function deleteService($id) {
        $stmt = $this->pdo->prepare("DELETE FROM services WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateServiceOrder($id, $sort_order) {
        $stmt = $this->pdo->prepare("UPDATE services SET sort_order = ? WHERE id = ?");
        return $stmt->execute([$sort_order, $id]);
    }

    public function getAllServicesCount() {
    $stmt = $this->pdo->query("SELECT COUNT(*) FROM services");
    return $stmt->fetchColumn();
}
}
?>