<?php
require_once __DIR__ . '/../../includes/Database.php';
$db = new Database();
$hero = $db->getHeroSettings();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Современная IT-компания</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/hero.css">
</head>
<body>
    <div id="hero" class="hero-container" style="background-image: url('<?= htmlspecialchars($hero['background_image']) ?>');">
        <h1><?= htmlspecialchars($hero['title']) ?></h1>
        <h3><?= htmlspecialchars($hero['subtitle']) ?></h3>
        <button onclick="document.getElementById('feedbackForm')?.scrollIntoView({behavior: 'smooth'})"><?= htmlspecialchars($hero['button_text']) ?></button>
        <p><?= nl2br(htmlspecialchars($hero['description'])) ?></p>
        <div class="scrol-container">
            <div class="chevron"></div>
            <div class="chevron"></div>
            <div class="chevron"></div>
            <span class="text">Scroll down</span>
        </div>
    </div>  
</body>
</html>