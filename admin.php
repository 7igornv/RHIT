<?php
require_once 'includes/Database.php';
require_once 'includes/Auth.php';

$db = new Database();

// Обработка выхода
if (isset($_GET['logout'])) {
    Auth::logout();
    header('Location: /admin.php');
    exit();
}

// Обработка входа
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (Auth::login($username, $password)) {
        header('Location: /admin.php');
        exit();
    } else {
        $loginError = 'Неверный логин или пароль';
    }
}

// Функция для загрузки файла
function uploadServiceImage($file) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'service_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $uploadPath = __DIR__ . '/assets/img/uploads/' . $filename;
    
    // Создаём папку если нет
    if (!is_dir(__DIR__ . '/assets/img/uploads')) {
        mkdir(__DIR__ . '/assets/img/uploads', 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return '/assets/img/uploads/' . $filename;
    }
    
    return null;
}

// Обработка добавления услуги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $title = $_POST['service_title'] ?? '';
    $image = null;
    
    if (isset($_FILES['service_image_file']) && $_FILES['service_image_file']['error'] === UPLOAD_ERR_OK) {
        $image = uploadServiceImage($_FILES['service_image_file']);
    } elseif (!empty($_POST['service_image_url'])) {
        $image = $_POST['service_image_url'];
    }
    
    if ($title && $image) {
        $db->addService($title, $image);
        header('Location: /admin.php?tab=services&service_added=1');
        exit();
    }
}

// Обработка редактирования услуги
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
        header('Location: /admin.php?tab=services&service_updated=1');
        exit();
    }
}

// Обработка удаления услуги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $id = $_POST['service_id'] ?? 0;
    if ($id) {
        $db->deleteService($id);
        header('Location: /admin.php?tab=services&service_deleted=1');
        exit();
    }
}

// Обработка изменения порядка
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $id = $_POST['service_id'] ?? 0;
    $sort_order = $_POST['sort_order'] ?? 0;
    if ($id) {
        $db->updateServiceOrder($id, $sort_order);
        header('Location: /admin.php?tab=services&order_updated=1');
        exit();
    }
}

// Обработка переключения статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $id = $_POST['service_id'] ?? 0;
    if ($id) {
        $service = $db->getServiceById($id);
        $newStatus = $service['is_active'] ? 0 : 1;
        $db->updateService($id, $service['title'], $service['image'], $newStatus);
        header('Location: /admin.php?tab=services&status_updated=1');
        exit();
    }
}

// Обработка сохранения контента
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_block'])) {
    $blockKey = $_POST['block_key'];
    $content = $_POST['content'];
    $db->updateContentBlock($blockKey, $content);
    header('Location: /admin.php?tab=content&updated=1');
    exit();
}

// Обработка изменения статуса заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $requestId = $_POST['request_id'];
    $status = $_POST['status'];
    $db->updateRequestStatus($requestId, $status);
    header('Location: /admin.php?tab=requests&status_updated=1');
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
        <link rel="stylesheet" href="assets/css/admin.css">
    </head>
    <body class="login-container">
        <div class="login-form">
            <h2>Вход в админ-панель</h2>
            <?php if ($loginError): ?>
                <div class="error"><?= htmlspecialchars($loginError) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['login']) && $_GET['login'] === 'required'): ?>
                <div class="error">Требуется авторизация</div>
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

// Определяем активную вкладку
$activeTab = $_GET['tab'] ?? 'content';

// Получаем данные
$blocks = $db->getAllContentBlocks();
$requests = $db->getAllRequests();
$statusCounts = $db->getRequestStatusCounts();
$servicesList = $db->getAllServices();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: white; padding: 30px; border-radius: 10px; width: 500px; max-width: 90%; }
        .modal-content input, .modal-content textarea { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 4px; }
        .image-preview { margin: 10px 0; display: none; }
        .image-preview img { max-width: 100%; max-height: 150px; border-radius: 5px; }
        .current-image { max-width: 100%; max-height: 100px; border-radius: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Админ-панель</h1>
            <a href="?logout=1" class="logout-btn">Выйти</a>
        </div>
        
        <div class="tabs">
            <a href="?tab=content" class="tab <?= $activeTab === 'content' ? 'active' : '' ?>">📝 Контент</a>
            <a href="?tab=services" class="tab <?= $activeTab === 'services' ? 'active' : '' ?>">
                🛠️ Услуги
                <?php if (count($servicesList) > 0): ?>
                    <span class="badge"><?= count($servicesList) ?></span>
                <?php endif; ?>
            </a>
            <a href="?tab=requests" class="tab <?= $activeTab === 'requests' ? 'active' : '' ?>">
                📋 Заявки
                <?php if (($statusCounts['new'] ?? 0) > 0): ?>
                    <span class="badge"><?= (int)($statusCounts['new'] ?? 0) ?></span>
                <?php endif; ?>
            </a>
        </div>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="success">✅ Контент успешно обновлен!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['status_updated'])): ?>
            <div class="success">✅ Статус заявки обновлен!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['service_added'])): ?>
            <div class="success">✅ Услуга добавлена!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['service_updated'])): ?>
            <div class="success">✅ Услуга обновлена!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['service_deleted'])): ?>
            <div class="success">✅ Услуга удалена!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['order_updated'])): ?>
            <div class="success">✅ Порядок услуг обновлен!</div>
        <?php endif; ?>
        
        <!-- Вкладка Контент -->
        <?php if ($activeTab === 'content'): ?>
            <div class="grid">
                <?php foreach ($blocks as $block): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($block['block_name']) ?></h3>
                        <div class="key">Ключ: <?= htmlspecialchars($block['block_key']) ?></div>
                        <form method="POST">
                            <input type="hidden" name="block_key" value="<?= htmlspecialchars($block['block_key']) ?>">
                            <textarea name="content" rows="<?= $block['type'] === 'html' ? 6 : 4 ?>"><?= htmlspecialchars($block['content']) ?></textarea>
                            <button type="submit" name="save_block">Сохранить</button>
                        </form>
                        <div class="preview">
                            <div class="preview-title">📱 Предпросмотр:</div>
                            <?= $block['content'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Вкладка Услуги -->
        <?php if ($activeTab === 'services'): ?>
            <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">🛠️ Управление услугами</h2>
                    <button onclick="document.getElementById('addServiceModal').style.display='flex'" 
                            style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                        + Добавить услугу
                    </button>
                </div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">ID</th>
                            <th style="padding: 12px; text-align: left;">Название</th>
                            <th style="padding: 12px; text-align: left;">Изображение</th>
                            <th style="padding: 12px; text-align: left;">Порядок</th>
                            <th style="padding: 12px; text-align: left;">Статус</th>
                            <th style="padding: 12px; text-align: left;">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicesList as $service): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px;">#<?= $service['id'] ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($service['title']) ?></td>
                            <td style="padding: 12px;">
                                <img src="<?= htmlspecialchars($service['image']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <span style="font-size: 11px; display: block;"><?= basename($service['image']) ?></span>
                            </td>
                            <td style="padding: 12px;">
                                <form method="POST" style="display: flex; gap: 5px; align-items: center;">
                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                    <input type="number" name="sort_order" value="<?= $service['sort_order'] ?>" style="width: 60px; padding: 4px;">
                                    <button type="submit" name="update_order" style="padding: 4px 8px;">Изменить</button>
                                </form>
                            </td>
                            <td style="padding: 12px;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                    <button type="submit" name="toggle_status" 
                                            style="background: <?= $service['is_active'] ? '#dc3545' : '#28a745' ?>; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer;">
                                        <?= $service['is_active'] ? 'Деактивировать' : 'Активировать' ?>
                                    </button>
                                </form>
                            </td>
                            <td style="padding: 12px;">
                                <button onclick="editService(<?= $service['id'] ?>, '<?= htmlspecialchars($service['title']) ?>', '<?= htmlspecialchars($service['image']) ?>')" 
                                        style="background: #007bff; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                                    ✏️
                                </button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить услугу?')">
                                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                    <button type="submit" name="delete_service" 
                                            style="background: #dc3545; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer;">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Модальное окно добавления услуги -->
            <div id="addServiceModal" class="modal">
                <div class="modal-content">
                    <h3>➕ Добавить услугу</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <div>
                            <label>Название услуги:</label>
                            <input type="text" name="service_title" required>
                        </div>
                        <div>
                            <label>Изображение:</label>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <input type="text" name="service_image_url" placeholder="URL из интернета" style="width: 100%;">
                                    <small>Пример: https://example.com/image.jpg</small>
                                </div>
                                <div style="text-align: center; padding: 0 10px;">или</div>
                                <div style="flex: 1;">
                                    <input type="file" name="service_image_file" accept="image/*" style="width: 100%;">
                                    <small>Выберите файл (JPG, PNG, WEBP, до 2MB)</small>
                                </div>
                            </div>
                        </div>
                        <div class="image-preview" id="addImagePreview">
                            <img id="addPreviewImg" src="">
                        </div>
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button type="button" onclick="document.getElementById('addServiceModal').style.display='none'" 
                                    style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                                Отмена
                            </button>
                            <button type="submit" name="add_service" 
                                    style="background: #28a745; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                                Добавить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Модальное окно редактирования услуги -->
            <div id="editServiceModal" class="modal">
                <div class="modal-content">
                    <h3>✏️ Редактировать услугу</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="edit_service_id" id="edit_service_id">
                        <div>
                            <label>Название услуги:</label>
                            <input type="text" name="service_title" id="edit_service_title" required>
                        </div>
                        <div>
                            <label>Текущее изображение:</label>
                            <img id="edit_current_image" class="current-image" src="">
                        </div>
                        <div>
                            <label>Новое изображение (опционально):</label>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <div style="flex: 1;">
                                    <input type="text" name="service_image_url" id="edit_service_url" placeholder="URL из интернета" style="width: 100%;">
                                </div>
                                <div style="text-align: center; padding: 0 10px;">или</div>
                                <div style="flex: 1;">
                                    <input type="file" name="service_image_file" id="edit_service_file" accept="image/*" style="width: 100%;">
                                </div>
                            </div>
                        </div>
                        <div class="image-preview" id="editImagePreview">
                            <img id="editPreviewImg" src="">
                        </div>
                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button type="button" onclick="document.getElementById('editServiceModal').style.display='none'" 
                                    style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                                Отмена
                            </button>
                            <button type="submit" name="edit_service" 
                                    style="background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                                Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Вкладка Заявки -->
        <?php if ($activeTab === 'requests'): ?>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?= (int)($statusCounts['total'] ?? 0) ?></div>
                    <div class="stat-label">Всего заявок</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= (int)($statusCounts['new'] ?? 0) ?></div>
                    <div class="stat-label">Новые</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= (int)($statusCounts['in_progress'] ?? 0) ?></div>
                    <div class="stat-label">В работе</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= (int)($statusCounts['completed'] ?? 0) ?></div>
                    <div class="stat-label">Завершены</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= (int)($statusCounts['rejected'] ?? 0) ?></div>
                    <div class="stat-label">Отклонены</div>
                </div>
            </div>
            
            <div class="requests-table">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left;">ID</th>
                            <th style="padding: 12px; text-align: left;">Дата</th>
                            <th style="padding: 12px; text-align: left;">Имя</th>
                            <th style="padding: 12px; text-align: left;">Телефон</th>
                            <th style="padding: 12px; text-align: left;">Email</th>
                            <th style="padding: 12px; text-align: left;">Сообщение</th>
                            <th style="padding: 12px; text-align: left;">Статус</th>
                            <th style="padding: 12px; text-align: left;">Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="8" style="padding: 12px; text-align: center;">Нет заявок</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $request): ?>
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 12px;">#<?= (int)$request['id'] ?></td>
                                    <td style="padding: 12px;"><?= date('d.m.Y H:i', strtotime($request['created_at'])) ?></td>
                                    <td style="padding: 12px;"><?= htmlspecialchars($request['name'] ?? '') ?></td>
                                    <td style="padding: 12px;"><?= htmlspecialchars($request['phone'] ?? '') ?></td>
                                    <td style="padding: 12px;"><?= htmlspecialchars($request['email'] ?? '') ?></td>
                                    <td style="padding: 12px; max-width: 250px; word-wrap: break-word;">
                                        <?php 
                                        $msg = $request['message'] ?? '';
                                        if (!is_string($msg)) $msg = '';
                                        $msg = strip_tags($msg);
                                        $msg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
                                        if (strlen($msg) > 100) $msg = substr($msg, 0, 97) . '...';
                                        echo $msg;
                                        ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <span class="status status-<?= $request['status'] ?? 'new' ?>">
                                            <?php
                                                $statusNames = [
                                                    'new' => 'Новая',
                                                    'in_progress' => 'В работе',
                                                    'completed' => 'Завершена',
                                                    'rejected' => 'Отклонена'
                                                ];
                                                echo $statusNames[$request['status'] ?? 'new'] ?? $request['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <form method="POST" style="margin: 0;">
                                            <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">
                                            <select name="status" onchange="this.form.submit()" style="padding: 4px;">
                                                <option value="new" <?= ($request['status'] ?? '') === 'new' ? 'selected' : '' ?>>Новая</option>
                                                <option value="in_progress" <?= ($request['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>В работе</option>
                                                <option value="completed" <?= ($request['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Завершена</option>
                                                <option value="rejected" <?= ($request['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Отклонена</option>
                                            </select>
                                            <button type="submit" name="update_status" style="margin-top: 5px; padding: 4px 8px;">Изменить</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Предпросмотр для добавления
        const addFileInput = document.querySelector('#addServiceModal input[name="service_image_file"]');
        const addUrlInput = document.querySelector('#addServiceModal input[name="service_image_url"]');
        const addPreviewDiv = document.getElementById('addImagePreview');
        const addPreviewImg = document.getElementById('addPreviewImg');
        
        if (addFileInput) {
            addFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        addPreviewImg.src = event.target.result;
                        addPreviewDiv.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                    if (addUrlInput) addUrlInput.value = '';
                }
            });
        }
        
        if (addUrlInput) {
            addUrlInput.addEventListener('input', function(e) {
                if (e.target.value) {
                    addPreviewImg.src = e.target.value;
                    addPreviewDiv.style.display = 'block';
                    if (addFileInput) addFileInput.value = '';
                } else if (addPreviewDiv) {
                    addPreviewDiv.style.display = 'none';
                }
            });
        }
        
        // Предпросмотр для редактирования
        const editFileInput = document.querySelector('#editServiceModal input[name="service_image_file"]');
        const editUrlInput = document.querySelector('#editServiceModal input[name="service_image_url"]');
        const editPreviewDiv = document.getElementById('editImagePreview');
        const editPreviewImg = document.getElementById('editPreviewImg');
        
        if (editFileInput) {
            editFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        editPreviewImg.src = event.target.result;
                        editPreviewDiv.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                    if (editUrlInput) editUrlInput.value = '';
                }
            });
        }
        
        if (editUrlInput) {
            editUrlInput.addEventListener('input', function(e) {
                if (e.target.value) {
                    editPreviewImg.src = e.target.value;
                    editPreviewDiv.style.display = 'block';
                    if (editFileInput) editFileInput.value = '';
                }
            });
        }
        
        function editService(id, title, image) {
            document.getElementById('edit_service_id').value = id;
            document.getElementById('edit_service_title').value = title;
            document.getElementById('edit_current_image').src = image;
            document.getElementById('edit_service_url').value = '';
            if (editFileInput) editFileInput.value = '';
            editPreviewDiv.style.display = 'none';
            document.getElementById('editServiceModal').style.display = 'flex';
        }
        
        // Закрытие модальных окон
        window.onclick = function(event) {
            const addModal = document.getElementById('addServiceModal');
            const editModal = document.getElementById('editServiceModal');
            if (event.target === addModal) addModal.style.display = 'none';
            if (event.target === editModal) editModal.style.display = 'none';
        }
    </script>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>