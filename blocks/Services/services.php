<?php
require_once __DIR__ . '/../../includes/Database.php';
$db = new Database();
$services = $db->getActiveServices();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши услуги</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/services.css">
</head>
<body>
    <div id="services" class="services-container">
        <h2>Наши услуги</h2>
        
        <div class="carousel-stage" id="carouselStage">
            <div class="carousel-track" id="mainTrack">
                <?php foreach ($services as $service): ?>
                <div class="services-cell" data-id="<?= $service['id'] ?>">
                    <div class="services-cell-text" lang="ru"><?= htmlspecialchars($service['title']) ?></div>
                    <img class="services-cell-img" src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['title']) ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="thumb-stage" id="thumbStage">
            <div class="thumb-frame">
                <button class="frame-btn prev" id="thumbPrev">‹</button>
                <div class="thumb-viewport">
                    <div class="thumb-track" id="thumbTrack"></div>
                </div>
                <button class="frame-btn next" id="thumbNext">›</button>
            </div>
        </div>
    </div>
    
    <script src="blocks/Services/services.js"></script>
</body>
</html>