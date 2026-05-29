<?php
require_once __DIR__ . '/../../includes/Database.php';
$db = new Database();
$competencies = $db->getActiveCompetencies();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши компетенции</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/competencies.css">
</head>
<body>
    <div id="competencies" class="competencies-container">
        <h2>Наши компетенции</h2>
        
        <div class="competencies">
            <?php foreach ($competencies as $comp): ?>
            <div class="competencies-cell" data-id="<?= $comp['id'] ?>">
                — <?= htmlspecialchars($comp['title']) ?> —
            </div>
            <?php endforeach; ?>
        </div>

        <button onclick="document.getElementById('feedbackForm')?.scrollIntoView({behavior: 'smooth'})">Оставить заявку</button>
    </div>
</body>
</html>