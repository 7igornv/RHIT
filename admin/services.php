<?php
$servicesList = $db->getAllServices();
?>

<!-- Уведомления -->
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

<div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">🛠️ Управление услугами</h2>
        <button onclick="openAddServiceModal()" 
                style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            + Добавить услугу
        </button>
    </div>
    
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Название</th>
                <th style="padding: 12px;">Изображение</th>
                <th style="padding: 12px;">Порядок</th>
                <th style="padding: 12px;">Статус</th>
                <th style="padding: 12px;">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicesList as $service): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 12px;">#<?= $service['id'] ?></td>
                <td style="padding: 12px;"><?= htmlspecialchars($service['title']) ?></td>
                <td style="padding: 12px;">
                    <img src="<?= htmlspecialchars($service['image']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                </td>
                <td style="padding: 12px;">
                    <form method="POST" style="display: flex; gap: 5px;">
                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                        <input type="number" name="sort_order" value="<?= $service['sort_order'] ?>" style="width: 60px; padding: 4px;">
                        <button type="submit" name="update_order">Изменить</button>
                    </form>
                </td>
                <td style="padding: 12px;">
                    <form method="POST">
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
            <div><label>Название:</label><input type="text" name="service_title" required></div>
            <div>
                <label>Изображение:</label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <div style="flex:1;"><input type="text" name="service_image_url" placeholder="URL из интернета"></div>
                    <div style="text-align:center; padding:0 10px;">или</div>
                    <div style="flex:1;"><input type="file" name="service_image_file" accept="image/*"></div>
                </div>
            </div>
            <div class="image-preview" id="addImagePreview"><img id="addPreviewImg" src=""></div>
            <div class="modal-buttons">
                <button type="button" onclick="closeAddServiceModal()" class="btn-cancel">Отмена</button>
                <button type="submit" name="add_service" class="btn-submit">Добавить</button>
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
            <div><label>Название:</label><input type="text" name="service_title" id="edit_service_title" required></div>
            <div><label>Текущее изображение:</label><img id="edit_current_image" class="current-image" src=""></div>
            <div>
                <label>Новое изображение:</label>
                <div style="display: flex; gap: 10px;">
                    <div style="flex:1;"><input type="text" name="service_image_url" id="edit_service_url" placeholder="URL из интернета"></div>
                    <div style="text-align:center; padding:0 10px;">или</div>
                    <div style="flex:1;"><input type="file" name="service_image_file" id="edit_service_file" accept="image/*"></div>
                </div>
            </div>
            <div class="image-preview" id="editImagePreview"><img id="editPreviewImg" src=""></div>
            <div class="modal-buttons">
                <button type="button" onclick="closeEditServiceModal()" class="btn-cancel">Отмена</button>
                <button type="submit" name="edit_service" class="btn-edit">Сохранить</button>
            </div>
        </form>
    </div>
</div>