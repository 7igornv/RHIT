<?php
require_once __DIR__ . '/../../includes/Database.php';
$db = new Database();
$clients = $db->getActiveClients();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши клиенты</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/clients.css">
</head>
<body>
    <div id="clients" class="clients-container">
        <h2>Наши клиенты</h2>
        <div class="clients">
            <?php foreach ($clients as $client): ?>
                <img src="<?= htmlspecialchars($client['logo']) ?>" alt="<?= htmlspecialchars($client['name']) ?>" title="<?= htmlspecialchars($client['name']) ?>">
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>