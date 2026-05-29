<?php
$hero = $db->getHeroSettings();

// Обработка сохранения настроек
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_hero'])) {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $button_text = $_POST['button_text'] ?? '';
    $description = $_POST['description'] ?? '';
    $background_image = null;
    
    if (isset($_FILES['background_image_file']) && $_FILES['background_image_file']['error'] === UPLOAD_ERR_OK) {
        $background_image = uploadHeroImage($_FILES['background_image_file']);
    } elseif (!empty($_POST['background_image_url'])) {
        $background_image = $_POST['background_image_url'];
    }
    
    if ($background_image) {
        $db->updateHeroSettings($title, $subtitle, $button_text, $description, $background_image);
    } else {
        $db->updateHeroSettings($title, $subtitle, $button_text, $description);
    }
    
    header('Location: ?tab=hero&saved=1');
    exit();
}
?>

<?php if (isset($_GET['saved'])): ?>
    <div class="success">✅ Настройки Hero-блока сохранены!</div>
<?php endif; ?>

<div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <h2 style="margin: 0 0 20px 0;">🎨 Настройка Hero-блока (баннера)</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Заголовок (h1):</label>
            <input type="text" name="title" value="<?= htmlspecialchars($hero['title']) ?>" 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Подзаголовок (h3):</label>
            <input type="text" name="subtitle" value="<?= htmlspecialchars($hero['subtitle']) ?>" 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Текст кнопки:</label>
            <input type="text" name="button_text" value="<?= htmlspecialchars($hero['button_text']) ?>" 
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Описание:</label>
            <textarea name="description" rows="4" 
                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?= htmlspecialchars($hero['description']) ?></textarea>
            <small style="color: #666;">Поддерживается перенос строк</small>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Фоновое изображение:</label>
            <div style="margin-bottom: 10px;">
                <img src="<?= htmlspecialchars($hero['background_image']) ?>" 
                     style="max-width: 100%; max-height: 200px; border-radius: 5px; border: 1px solid #ddd;">
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <div style="flex: 1;">
                    <input type="text" name="background_image_url" placeholder="URL из интернета" 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="text-align: center; padding: 0 10px;">или</div>
                <div style="flex: 1;">
                    <input type="file" name="background_image_file" accept="image/*" 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>
            <small style="color: #666;">Рекомендуемый размер: 1920x1080px (JPG, PNG, WEBP до 5MB)</small>
        </div>
        
        <div class="image-preview" id="heroPreview" style="display: none; margin-bottom: 20px;">
            <img id="heroPreviewImg" src="" style="max-width: 100%; max-height: 150px; border-radius: 5px;">
        </div>
        
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="submit" name="save_hero" 
                    style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                💾 Сохранить настройки
            </button>
        </div>
    </form>
</div>

<script>
// Предпросмотр фонового изображения
const heroFile = document.querySelector('#hero input[name="background_image_file"]');
const heroUrl = document.querySelector('#hero input[name="background_image_url"]');
const heroPreviewDiv = document.getElementById('heroPreview');
const heroPreviewImg = document.getElementById('heroPreviewImg');

if (heroFile) {
    heroFile.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                heroPreviewImg.src = event.target.result;
                heroPreviewDiv.style.display = 'block';
            }
            reader.readAsDataURL(file);
            if (heroUrl) heroUrl.value = '';
        }
    });
}

if (heroUrl) {
    heroUrl.addEventListener('input', function(e) {
        if (e.target.value) {
            heroPreviewImg.src = e.target.value;
            heroPreviewDiv.style.display = 'block';
            if (heroFile) heroFile.value = '';
        } else if (heroPreviewDiv) {
            heroPreviewDiv.style.display = 'none';
        }
    });
}
</script>