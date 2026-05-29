<?php
// Получаем параметр сортировки
$sort_by = $_GET['sort_by'] ?? 'new_first';
$requests = $db->getAllRequestsSorted($sort_by);
$statusCounts = $db->getRequestStatusCounts();
$newCount = $db->getNewRequestsCount();
?>

<!-- Уведомления -->
<?php if (isset($_GET['marked_read'])): ?>
    <div class="success">✅ Заявка отмечена как прочитанная!</div>
<?php endif; ?>
<?php if (isset($_GET['all_marked_read'])): ?>
    <div class="success">✅ Все заявки отмечены как прочитанные!</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
    <div class="success">✅ Заявка удалена!</div>
<?php endif; ?>

<div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0;">📋 Управление заявками</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <!-- Выпадающий список сортировки -->
            <form method="GET" style="display: inline;">
                <input type="hidden" name="tab" value="requests">
                <select name="sort_by" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="new_first" <?= $sort_by === 'new_first' ? 'selected' : '' ?>>🆕 Сначала новые</option>
                    <option value="old_first" <?= $sort_by === 'old_first' ? 'selected' : '' ?>>📅 Сначала старые</option>
                    <option value="id_asc" <?= $sort_by === 'id_asc' ? 'selected' : '' ?>>🔢 ID по возрастанию</option>
                    <option value="id_desc" <?= $sort_by === 'id_desc' ? 'selected' : '' ?>>🔢 ID по убыванию</option>
                    <option value="date_asc" <?= $sort_by === 'date_asc' ? 'selected' : '' ?>>📅 Дата по возрастанию</option>
                    <option value="date_desc" <?= $sort_by === 'date_desc' ? 'selected' : '' ?>>📅 Дата по убыванию</option>
                </select>
            </form>
            
            <?php if ($newCount > 0): ?>
                <form method="POST" onsubmit="return confirm('Отметить ВСЕ заявки как прочитанные?')">
                    <button type="submit" name="mark_all_read" 
                            style="background: #28a745; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                        ✅ Прочитать все (<?= $newCount ?>)
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="stats">
        <div class="stat-card">
            <div class="stat-number"><?= (int)($statusCounts['total'] ?? 0) ?></div>
            <div class="stat-label">Всего заявок</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #ffc107;"><?= (int)($statusCounts['new'] ?? 0) ?></div>
            <div class="stat-label">🟡 Новые</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #17a2b8;"><?= (int)($statusCounts['read'] ?? 0) ?></div>
            <div class="stat-label">🔵 Прочитанные</div>
        </div>
    </div>
    
    <div class="requests-table" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Дата</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Имя</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Телефон</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Email</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Сообщение</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Статус</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="8" style="padding: 12px; text-align: center;">Нет заявок</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                    <tr style="border-bottom: 1px solid #ddd; <?= $request['status'] === 'new' ? 'background: #fff3cd;' : '' ?>">
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
                            <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; <?= $request['status'] === 'new' ? 'background: #ffc107; color: #856404;' : 'background: #d1ecf1; color: #0c5460;' ?>">
                                <?= $request['status'] === 'new' ? '🟡 Новая' : '🔵 Прочитана' ?>
                            </span>
                        </td>
                        <td style="padding: 12px;">
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <?php if ($request['status'] === 'new'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">
                                        <button type="submit" name="mark_read" 
                                                style="background: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                            📖 Прочитать
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить заявку #<?= (int)$request['id'] ?>?')">
                                    <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">
                                    <button type="submit" name="delete_request" 
                                            style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        🗑️ Удалить
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>