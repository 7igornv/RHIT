<?php
require_once '../includes/Database.php';
require_once '../includes/Auth.php';

$db = new Database();

// Обработка выхода
if (isset($_GET['logout'])) {
    Auth::logout();
    header('Location: index.php');
    exit();
}

// Обработка входа
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (Auth::login($username, $password)) {
        header('Location: index.php');
        exit();
    } else {
        $loginError = 'Неверный логин или пароль';
    }
}

// Функция для загрузки фонового изображения Hero-блока
function uploadHeroImage($file) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'hero_bg_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $uploadPath = __DIR__ . '/../assets/img/uploads/' . $filename;
    
    if (!is_dir(__DIR__ . '/../assets/img/uploads')) {
        mkdir(__DIR__ . '/../assets/img/uploads', 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return '/assets/img/uploads/' . $filename;
    }
    
    return null;
}

// ========== ФУНКЦИЯ ДЛЯ ЗАГРУЗКИ ИЗОБРАЖЕНИЙ УСЛУГ ==========
function uploadServiceImage($file) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'service_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $uploadPath = __DIR__ . '/../assets/img/uploads/' . $filename;
    
    if (!is_dir(__DIR__ . '/../assets/img/uploads')) {
        mkdir(__DIR__ . '/../assets/img/uploads', 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return '/assets/img/uploads/' . $filename;
    }
    
    return null;
}

// ========== ФУНКЦИЯ ДЛЯ ЗАГРУЗКИ ЛОГОТИПОВ КЛИЕНТОВ ==========
function uploadClientImage($file) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'client_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $uploadPath = __DIR__ . '/../assets/img/uploads/' . $filename;
    
    if (!is_dir(__DIR__ . '/../assets/img/uploads')) {
        mkdir(__DIR__ . '/../assets/img/uploads', 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return '/assets/img/uploads/' . $filename;
    }
    
    return null;
}

// ========== ОБРАБОТЧИКИ УСЛУГ ==========
// Добавление услуги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $title = $_POST['service_title'] ?? '';
    $image = null;
    
    if (isset($_FILES['service_image_file']) && $_FILES['service_image_file']['error'] === UPLOAD_ERR_OK) {
        $image = uploadServiceImage($_FILES['service_image_file']);
    } elseif (!empty($_POST['service_image_url'])) {
        $image = $_POST['service_image_url'];
    } else {
        $image = '/assets/img/no-image.png';
    }
    
    if ($title) {
        $db->addService($title, $image);
        header('Location: ?tab=services&service_added=1');
        exit();
    }
}

// Редактирование услуги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service'])) {
    $id = $_POST['edit_service_id'] ?? 0;
    $title = $_POST['service_title'] ?? '';
    $image = null;
    
    if (isset($_FILES['service_image_file']) && $_FILES['service_image_file']['error'] === UPLOAD_ERR_OK) {
        $image = uploadServiceImage($_FILES['service_image_file']);
    } elseif (!empty($_POST['service_image_url'])) {
        $image = $_POST['service_image_url'];
    }
    
    if ($id && $title) {
        if ($image) {
            $db->updateService($id, $title, $image, 1);
        } else {
            $service = $db->getServiceById($id);
            $db->updateService($id, $title, $service['image'], 1);
        }
        header('Location: ?tab=services&service_updated=1');
        exit();
    }
}

// Удаление услуги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $id = $_POST['service_id'] ?? 0;
    if ($id) {
        $db->deleteService($id);
        header('Location: ?tab=services&service_deleted=1');
        exit();
    }
}

// Изменение порядка услуг
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $id = $_POST['service_id'] ?? 0;
    $sort_order = $_POST['sort_order'] ?? 0;
    if ($id) {
        $db->updateServiceOrder($id, $sort_order);
        header('Location: ?tab=services&order_updated=1');
        exit();
    }
}

// Переключение статуса услуги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $id = $_POST['service_id'] ?? 0;
    if ($id) {
        $service = $db->getServiceById($id);
        $newStatus = $service['is_active'] ? 0 : 1;
        $db->updateService($id, $service['title'], $service['image'], $newStatus);
        header('Location: ?tab=services&status_updated=1');
        exit();
    }
}

// ========== ОБРАБОТЧИКИ КОМПЕТЕНЦИЙ ==========
// Добавление компетенции
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_competency'])) {
    $title = $_POST['competency_title'] ?? '';
    if ($title) {
        $db->addCompetency($title);
        header('Location: ?tab=competencies&competency_added=1');
        exit();
    }
}

// Редактирование компетенции
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_competency'])) {
    $id = $_POST['edit_competency_id'] ?? 0;
    $title = $_POST['competency_title'] ?? '';
    if ($id && $title) {
        $db->updateCompetency($id, $title, 1);
        header('Location: ?tab=competencies&competency_updated=1');
        exit();
    }
}

// Удаление компетенции
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_competency'])) {
    $id = $_POST['competency_id'] ?? 0;
    if ($id) {
        $db->deleteCompetency($id);
        header('Location: ?tab=competencies&competency_deleted=1');
        exit();
    }
}

// Изменение порядка компетенций
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_competency_order'])) {
    $id = $_POST['competency_id'] ?? 0;
    $sort_order = $_POST['sort_order'] ?? 0;
    if ($id) {
        $db->updateCompetencyOrder($id, $sort_order);
        header('Location: ?tab=competencies&order_updated=1');
        exit();
    }
}

// Переключение статуса компетенции
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_competency_status'])) {
    $id = $_POST['competency_id'] ?? 0;
    if ($id) {
        $comp = $db->getCompetencyById($id);
        $newStatus = $comp['is_active'] ? 0 : 1;
        $db->updateCompetency($id, $comp['title'], $newStatus);
        header('Location: ?tab=competencies&status_updated=1');
        exit();
    }
}

// ========== ОБРАБОТЧИКИ КЛИЕНТОВ ==========
// Добавление клиента
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_client'])) {
    $name = $_POST['client_name'] ?? '';
    $logo = null;
    
    if (isset($_FILES['client_logo_file']) && $_FILES['client_logo_file']['error'] === UPLOAD_ERR_OK) {
        $logo = uploadClientImage($_FILES['client_logo_file']);
    } elseif (!empty($_POST['client_logo_url'])) {
        $logo = $_POST['client_logo_url'];
    } else {
        $logo = '/assets/img/no-logo.png';
    }
    
    if ($name) {
        $db->addClient($name, $logo);
        header('Location: ?tab=clients&client_added=1');
        exit();
    }
}

// Редактирование клиента
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_client'])) {
    $id = $_POST['edit_client_id'] ?? 0;
    $name = $_POST['client_name'] ?? '';
    $logo = null;
    
    if (isset($_FILES['client_logo_file']) && $_FILES['client_logo_file']['error'] === UPLOAD_ERR_OK) {
        $logo = uploadClientImage($_FILES['client_logo_file']);
    } elseif (!empty($_POST['client_logo_url'])) {
        $logo = $_POST['client_logo_url'];
    }
    
    if ($id && $name) {
        if ($logo) {
            $db->updateClient($id, $name, $logo, 1);
        } else {
            $client = $db->getClientById($id);
            $db->updateClient($id, $name, $client['logo'], 1);
        }
        header('Location: ?tab=clients&client_updated=1');
        exit();
    }
}

// Удаление клиента
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client'])) {
    $id = $_POST['client_id'] ?? 0;
    if ($id) {
        $db->deleteClient($id);
        header('Location: ?tab=clients&client_deleted=1');
        exit();
    }
}

// Изменение порядка клиентов
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_client_order'])) {
    $id = $_POST['client_id'] ?? 0;
    $sort_order = $_POST['sort_order'] ?? 0;
    if ($id) {
        $db->updateClientOrder($id, $sort_order);
        header('Location: ?tab=clients&order_updated=1');
        exit();
    }
}

// Переключение статуса клиента
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_client_status'])) {
    $id = $_POST['client_id'] ?? 0;
    if ($id) {
        $client = $db->getClientById($id);
        $newStatus = $client['is_active'] ? 0 : 1;
        $db->updateClient($id, $client['name'], $client['logo'], $newStatus);
        header('Location: ?tab=clients&status_updated=1');
        exit();
    }
}

// ========== ОБРАБОТЧИКИ ЗАЯВОК ==========
// Отметка заявки как прочитанной
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $requestId = $_POST['request_id'] ?? 0;
    if ($requestId) {
        $db->markAsRead($requestId);
        header('Location: ?tab=requests&marked_read=1');
        exit();
    }
}

// Отметка всех заявок как прочитанных
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read'])) {
    $db->markAllAsRead();
    header('Location: ?tab=requests&all_marked_read=1');
    exit();
}

// Если пользователь не авторизован - показываем форму входа
if (!Auth::isLoggedIn()) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Вход в админ-панель</title>
        <link rel="stylesheet" href="/assets/css/admin.css">
    </head>
    <body class="login-container">
        <div class="login-form">
            <h2>Вход в админ-панель</h2>
            <?php if ($loginError): ?>
                <div class="error"><?= htmlspecialchars($loginError) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Логин" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit" name="login">Войти</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}
// Удаление заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request'])) {
    $requestId = $_POST['request_id'] ?? 0;
    if ($requestId) {
        $db->deleteRequest($requestId);
        header('Location: ?tab=requests&deleted=1');
        exit();
    }
}

// Определяем активную вкладку
$activeTab = $_GET['tab'] ?? 'hero';

// Подключаем общий header
include 'includes/header.php';

// Подключаем нужный модуль
switch ($activeTab) {
    case 'hero':
        include 'hero.php';
        break;
    case 'services':
        include 'services.php';
        break;
    case 'competencies':
        include 'competencies.php';
        break;
    case 'clients':
        include 'clients.php';
        break;
    case 'requests':
        include 'requests.php';
        break;
    default:
        include 'content.php';
        break;
}

// Подключаем общий footer
include 'includes/footer.php';
?>