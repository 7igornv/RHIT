<?php
$competenciesList = $db->getAllCompetencies();

// Обработка добавления
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_competency'])) {
    $title = $_POST['competency_title'] ?? '';
    if ($title) {
        $db->addCompetency($title);
        header('Location: ?tab=competencies&competency_added=1');
        exit();
    }
}

// Обработка редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_competency'])) {
    $id = $_POST['edit_competency_id'] ?? 0;
    $title = $_POST['competency_title'] ?? '';
    if ($id && $title) {
        $db->updateCompetency($id, $title, 1);
        header('Location: ?tab=competencies&competency_updated=1');
        exit();
    }
}

// Обработка удаления
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_competency'])) {
    $id = $_POST['competency_id'] ?? 0;
    if ($id) {
        $db->deleteCompetency($id);
        header('Location: ?tab=competencies&competency_deleted=1');
        exit();
    }
}

// Обработка порядка
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_competency_order'])) {
    $id = $_POST['competency_id'] ?? 0;
    $sort_order = $_POST['sort_order'] ?? 0;
    if ($id) {
        $db->updateCompetencyOrder($id, $sort_order);
        header('Location: ?tab=competencies&order_updated=1');
        exit();
    }
}

// Обработка статуса
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
?>

<!-- Уведомления -->
<?php if (isset($_GET['competency_added'])): ?>
    <div class="success">✅ Компетенция добавлена!</div>
<?php endif; ?>
<?php if (isset($_GET['competency_updated'])): ?>
    <div class="success">✅ Компетенция обновлена!</div>
<?php endif; ?>
<?php if (isset($_GET['competency_deleted'])): ?>
    <div class="success">✅ Компетенция удалена!</div>
<?php endif; ?>
<?php if (isset($_GET['order_updated'])): ?>
    <div class="success">✅ Порядок компетенций обновлен!</div>
<?php endif; ?>

<div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">🎯 Управление компетенциями</h2>
        <button onclick="document.getElementById('addCompetencyModal').style.display='flex'" 
                style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            + Добавить компетенцию
        </button>
    </div>
    
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Название</th>
                <th style="padding: 12px;">Порядок</th>
                <th style="padding: 12px;">Статус</th>
                <th style="padding: 12px;">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competenciesList as $comp): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 12px;">#<?= $comp['id'] ?></td>
                <td style="padding: 12px;"><?= htmlspecialchars($comp['title']) ?></td>
                <td style="padding: 12px;">
                    <form method="POST" style="display: flex; gap: 5px;">
                        <input type="hidden" name="competency_id" value="<?= $comp['id'] ?>">
                        <input type="number" name="sort_order" value="<?= $comp['sort_order'] ?>" style="width: 60px;">
                        <button type="submit" name="update_competency_order">Изменить</button>
                    </form>
                </td>
                <td style="padding: 12px;">
                    <form method="POST">
                        <input type="hidden" name="competency_id" value="<?= $comp['id'] ?>">
                        <button type="submit" name="toggle_competency_status" 
                                style="background: <?= $comp['is_active'] ? '#dc3545' : '#28a745' ?>; color:white;">
                            <?= $comp['is_active'] ? 'Деактивировать' : 'Активировать' ?>
                        </button>
                    </form>
                </td>
                <td style="padding: 12px;">
                    <button onclick="editCompetency(<?= $comp['id'] ?>, '<?= htmlspecialchars($comp['title']) ?>')">✏️</button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить?')">
                        <input type="hidden" name="competency_id" value="<?= $comp['id'] ?>">
                        <button type="submit" name="delete_competency" style="background:#dc3545; color:white;">🗑️</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="addCompetencyModal" class="modal">
    <div class="modal-content">
        <h3>➕ Добавить компетенцию</h3>
        <form method="POST">
            <textarea name="competency_title" rows="3" required style="width:100%"></textarea>
            <div style="display: flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" onclick="document.getElementById('addCompetencyModal').style.display='none'">Отмена</button>
                <button type="submit" name="add_competency">Добавить</button>
            </div>
        </form>
    </div>
</div>

<div id="editCompetencyModal" class="modal">
    <div class="modal-content">
        <h3>✏️ Редактировать компетенцию</h3>
        <form method="POST">
            <input type="hidden" name="edit_competency_id" id="edit_competency_id">
            <textarea name="competency_title" id="edit_competency_title" rows="3" required style="width:100%"></textarea>
            <div style="display: flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" onclick="document.getElementById('editCompetencyModal').style.display='none'">Отмена</button>
                <button type="submit" name="edit_competency">Сохранить</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCompetency(id, title) {
    document.getElementById('edit_competency_id').value = id;
    document.getElementById('edit_competency_title').value = title;
    document.getElementById('editCompetencyModal').style.display = 'flex';
}
</script>