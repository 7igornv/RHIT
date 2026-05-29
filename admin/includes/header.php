<?php
$newCount = $db->getNewRequestsCount();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Админ-панель</h1>
            <a href="?logout=1" class="logout-btn">Выйти</a>
        </div>
        
        <div class="tabs">
            <a href="?tab=hero" class="tab <?= $activeTab === 'hero' ? 'active' : '' ?>">🎨 Баннер</a>
            <a href="?tab=services" class="tab <?= $activeTab === 'services' ? 'active' : '' ?>">🛠️ Услуги</a>
            <a href="?tab=competencies" class="tab <?= $activeTab === 'competencies' ? 'active' : '' ?>">🎯 Компетенции</a>
            <a href="?tab=clients" class="tab <?= $activeTab === 'clients' ? 'active' : '' ?>">🏢 Клиенты</a>
            <a href="?tab=requests" class="tab <?= $activeTab === 'requests' ? 'active' : '' ?>">
                📋 Заявки
                <?php if ($newCount > 0): ?>
                    <span class="badge"><?= $newCount ?></span>
                <?php endif; ?>
            </a>
        </div>