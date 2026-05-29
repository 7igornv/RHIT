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
        
        // Подключаемся к БД
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
        
        // Таблица requests (для заявок) - обновлённая структура
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
        
        // Таблица services
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS services (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                image TEXT NOT NULL,
                sort_order INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Таблица competencies
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS competencies (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                sort_order INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Проверяем и добавляем начальные блоки контента
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM content_blocks");
        $count = $stmt->fetchColumn();
        
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

    // Сортировка: новые первые
    public function getAllRequestsSorted($sort_by = 'new_first') {
        switch ($sort_by) {
            case 'id_asc':
                $sql = "SELECT * FROM requests ORDER BY id ASC";
                break;
            case 'id_desc':
                $sql = "SELECT * FROM requests ORDER BY id DESC";
                break;
            case 'date_asc':
                $sql = "SELECT * FROM requests ORDER BY created_at ASC";
                break;
            case 'date_desc':
                $sql = "SELECT * FROM requests ORDER BY created_at DESC";
                break;
            case 'old_first':
                $sql = "SELECT * FROM requests ORDER BY 
                            CASE WHEN status = 'new' THEN 0 ELSE 1 END,
                            created_at ASC";
                break;
            case 'new_first':
            default:
                $sql = "SELECT * FROM requests ORDER BY 
                            CASE WHEN status = 'new' THEN 0 ELSE 1 END,
                            created_at DESC";
                break;
        }
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Отметить как прочитанное
    public function markAsRead($id) {
        $stmt = $this->pdo->prepare("UPDATE requests SET status = 'read', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Отметить все как прочитанные
    public function markAllAsRead() {
        $stmt = $this->pdo->prepare("UPDATE requests SET status = 'read', updated_at = CURRENT_TIMESTAMP WHERE status = 'new'");
        return $stmt->execute();
    }

    // Получить количество новых заявок
    public function getNewRequestsCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'new'");
        return $stmt->fetchColumn();
    }

    // Статистика по статусам
    public function getRequestStatusCounts() {
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new,
                SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read
            FROM requests
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Удалить заявку
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
    
    // ========== МЕТОДЫ ДЛЯ КОМПЕТЕНЦИЙ ==========
    public function getAllCompetencies() {
        $stmt = $this->pdo->query("SELECT * FROM competencies ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveCompetencies() {
        $stmt = $this->pdo->query("SELECT * FROM competencies WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompetencyById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM competencies WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCompetency($title) {
        $stmt = $this->pdo->prepare("INSERT INTO competencies (title, sort_order) VALUES (?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM competencies))");
        return $stmt->execute([$title]);
    }

    public function updateCompetency($id, $title, $is_active = 1) {
        $stmt = $this->pdo->prepare("UPDATE competencies SET title = ?, is_active = ? WHERE id = ?");
        return $stmt->execute([$title, $is_active, $id]);
    }

    public function deleteCompetency($id) {
        $stmt = $this->pdo->prepare("DELETE FROM competencies WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateCompetencyOrder($id, $sort_order) {
        $stmt = $this->pdo->prepare("UPDATE competencies SET sort_order = ? WHERE id = ?");
        return $stmt->execute([$sort_order, $id]);
    }
    
    public function getAllCompetenciesCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM competencies");
        return $stmt->fetchColumn();
    }

    // ========== МЕТОДЫ ДЛЯ КЛИЕНТОВ ==========
    public function getAllClients() {
        $stmt = $this->pdo->query("SELECT * FROM clients ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveClients() {
        $stmt = $this->pdo->query("SELECT * FROM clients WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClientById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addClient($name, $logo) {
        $stmt = $this->pdo->prepare("INSERT INTO clients (name, logo, sort_order) VALUES (?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM clients))");
        return $stmt->execute([$name, $logo]);
    }

    public function updateClient($id, $name, $logo, $is_active = 1) {
        $stmt = $this->pdo->prepare("UPDATE clients SET name = ?, logo = ?, is_active = ? WHERE id = ?");
        return $stmt->execute([$name, $logo, $is_active, $id]);
    }

    public function deleteClient($id) {
        $stmt = $this->pdo->prepare("DELETE FROM clients WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateClientOrder($id, $sort_order) {
        $stmt = $this->pdo->prepare("UPDATE clients SET sort_order = ? WHERE id = ?");
        return $stmt->execute([$sort_order, $id]);
    }

    public function getAllClientsCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM clients");
        return $stmt->fetchColumn();
    }

    // ========== МЕТОДЫ ДЛЯ HERO-БЛОКА (БАННЕРА) ==========
    public function getHeroSettings() {
        $stmt = $this->pdo->query("SELECT * FROM hero_settings LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            // Создаём запись по умолчанию
            $this->pdo->exec("
                INSERT INTO hero_settings (title, subtitle, button_text, description, background_image) 
                VALUES ('ООО \"РХИТ\"', 'Современная динамично развивающаяся IT-компания', 'Оставить заявку', 'Предоставляем сервисы в области информационных технологий. Управляем масштабной инфраструктурой и обеспечиваем ее бесперебойную работу', '/assets/img/hero-bg.jpg')
            ");
            $stmt = $this->pdo->query("SELECT * FROM hero_settings LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $result;
    }

    public function updateHeroSettings($title, $subtitle, $button_text, $description, $background_image = null) {
        if ($background_image) {
            $stmt = $this->pdo->prepare("UPDATE hero_settings SET title = ?, subtitle = ?, button_text = ?, description = ?, background_image = ? WHERE id = 1");
            return $stmt->execute([$title, $subtitle, $button_text, $description, $background_image]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE hero_settings SET title = ?, subtitle = ?, button_text = ?, description = ? WHERE id = 1");
            return $stmt->execute([$title, $subtitle, $button_text, $description]);
        }
    }

    public function updateHeroBackground($background_image) {
        $stmt = $this->pdo->prepare("UPDATE hero_settings SET background_image = ? WHERE id = 1");
        return $stmt->execute([$background_image]);
    }
}
?>