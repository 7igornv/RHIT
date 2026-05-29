<?php
$clientsList = $db->getAllClients();
?>

<!-- Уведомления -->
<?php if (isset($_GET['client_added'])): ?>
    <div class="success">✅ Клиент добавлен!</div>
<?php endif; ?>
<?php if (isset($_GET['client_updated'])): ?>
    <div class="success">✅ Клиент обновлен!</div>
<?php endif; ?>
<?php if (isset($_GET['client_deleted'])): ?>
    <div class="success">✅ Клиент удален!</div>
<?php endif; ?>
<?php if (isset($_GET['order_updated'])): ?>
    <div class="success">✅ Порядок клиентов обновлен!</div>
<?php endif; ?>

<div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">🏢 Управление клиентами</h2>
        <button onclick="openAddClientModal()" 
                style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            + Добавить клиента
        </button>
    </div>
    
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Название</th>
                <th style="padding: 12px;">Логотип</th>
                <th style="padding: 12px;">Порядок</th>
                <th style="padding: 12px;">Статус</th>
                <th style="padding: 12px;">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientsList as $client): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 12px;">#<?= $client['id'] ?></td>
                <td style="padding: 12px;"><?= htmlspecialchars($client['name']) ?></td>
                <td style="padding: 12px;">
                    <img src="<?= htmlspecialchars($client['logo']) ?>" style="width: 50px; height: 50px; object-fit: contain; background: #f0f0f0; border-radius: 5px;">
                </td>
                <td style="padding: 12px;">
                    <form method="POST" style="display: flex; gap: 5px;">
                        <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
                        <input type="number" name="sort_order" value="<?= $client['sort_order'] ?>" style="width: 60px; padding: 4px;">
                        <button type="submit" name="update_client_order">Изменить</button>
                    </form>
                </td>
                <td style="padding: 12px;">
                    <form method="POST">
                        <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
                        <button type="submit" name="toggle_client_status" 
                                style="background: <?= $client['is_active'] ? '#dc3545' : '#28a745' ?>; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer;">
                            <?= $client['is_active'] ? 'Деактивировать' : 'Активировать' ?>
                        </button>
                    </form>
                </td>
                <td style="padding: 12px;">
                    <button onclick="openEditClientModal(<?= $client['id'] ?>, '<?= htmlspecialchars($client['name']) ?>', '<?= htmlspecialchars($client['logo']) ?>')" 
                            style="background: #007bff; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                        ✏️
                    </button>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить клиента?')">
                        <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
                        <button type="submit" name="delete_client" 
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

<!-- Модальное окно добавления клиента -->
<div id="addClientModal" class="modal">
    <div class="modal-content">
        <h3>➕ Добавить клиента</h3>
        <form method="POST" enctype="multipart/form-data">
            <div><label>Название компании:</label><input type="text" name="client_name" required></div>
            <div>
                <label>Логотип:</label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                    <div style="flex:1;"><input type="text" name="client_logo_url" placeholder="URL из интернета"></div>
                    <div style="text-align:center; padding:0 10px;">или</div>
                    <div style="flex:1;"><input type="file" name="client_logo_file" accept="image/*"></div>
                </div>
            </div>
            <div class="image-preview" id="addClientPreview"><img id="addClientPreviewImg" src=""></div>
            <div class="modal-buttons">
                <button type="button" onclick="closeAddClientModal()" class="btn-cancel">Отмена</button>
                <button type="submit" name="add_client" class="btn-submit">Добавить</button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно редактирования клиента -->
<div id="editClientModal" class="modal">
    <div class="modal-content">
        <h3>✏️ Редактировать клиента</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="edit_client_id" id="edit_client_id">
            <div><label>Название компании:</label><input type="text" name="client_name" id="edit_client_name" required></div>
            <div><label>Текущий логотип:</label><img id="edit_current_logo" class="current-image" src=""></div>
            <div>
                <label>Новый логотип (опционально):</label>
                <div style="display: flex; gap: 10px;">
                    <div style="flex:1;"><input type="text" name="client_logo_url" id="edit_client_url" placeholder="URL из интернета"></div>
                    <div style="text-align:center; padding:0 10px;">или</div>
                    <div style="flex:1;"><input type="file" name="client_logo_file" id="edit_client_file" accept="image/*"></div>
                </div>
            </div>
            <div class="image-preview" id="editClientPreview"><img id="editClientPreviewImg" src=""></div>
            <div class="modal-buttons">
                <button type="button" onclick="closeEditClientModal()" class="btn-cancel">Отмена</button>
                <button type="submit" name="edit_client" class="btn-edit">Сохранить</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddClientModal() {
    document.getElementById('addClientModal').classList.add('show');
}
function closeAddClientModal() {
    document.getElementById('addClientModal').classList.remove('show');
    document.getElementById('addClientPreview').style.display = 'none';
}
function openEditClientModal(id, name, logo) {
    document.getElementById('edit_client_id').value = id;
    document.getElementById('edit_client_name').value = name;
    document.getElementById('edit_current_logo').src = logo;
    document.getElementById('edit_client_url').value = '';
    document.getElementById('edit_client_file').value = '';
    document.getElementById('editClientPreview').style.display = 'none';
    document.getElementById('editClientModal').classList.add('show');
}
function closeEditClientModal() {
    document.getElementById('editClientModal').classList.remove('show');
}

// Предпросмотр логотипа при добавлении
const addClientFile = document.querySelector('#addClientModal input[name="client_logo_file"]');
const addClientUrl = document.querySelector('#addClientModal input[name="client_logo_url"]');
const addClientPreviewDiv = document.getElementById('addClientPreview');
const addClientPreviewImg = document.getElementById('addClientPreviewImg');

if (addClientFile) {
    addClientFile.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                addClientPreviewImg.src = event.target.result;
                addClientPreviewDiv.style.display = 'block';
            }
            reader.readAsDataURL(file);
            if (addClientUrl) addClientUrl.value = '';
        }
    });
}
if (addClientUrl) {
    addClientUrl.addEventListener('input', function(e) {
        if (e.target.value) {
            addClientPreviewImg.src = e.target.value;
            addClientPreviewDiv.style.display = 'block';
            if (addClientFile) addClientFile.value = '';
        }
    });
}

// Предпросмотр при редактировании
const editClientFile = document.querySelector('#editClientModal input[name="client_logo_file"]');
const editClientUrl = document.querySelector('#editClientModal input[name="client_logo_url"]');
const editClientPreviewDiv = document.getElementById('editClientPreview');
const editClientPreviewImg = document.getElementById('editClientPreviewImg');

if (editClientFile) {
    editClientFile.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                editClientPreviewImg.src = event.target.result;
                editClientPreviewDiv.style.display = 'block';
            }
            reader.readAsDataURL(file);
            if (editClientUrl) editClientUrl.value = '';
        }
    });
}
if (editClientUrl) {
    editClientUrl.addEventListener('input', function(e) {
        if (e.target.value) {
            editClientPreviewImg.src = e.target.value;
            editClientPreviewDiv.style.display = 'block';
            if (editClientFile) editClientFile.value = '';
        }
    });
}
</script>