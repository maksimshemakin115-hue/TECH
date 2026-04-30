<?php
require_once '../config.php';
requireAdmin();

// Статистика
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$products_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orders_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Получаем данные
$users = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id")->fetchAll();
$products = $pdo->query("SELECT p.*, u.username FROM products p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC")->fetchAll();
$orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC")->fetchAll();

// Удаление пользователя
if (isset($_GET['delete_user'])) {
    $userId = (int)$_GET['delete_user'];
    if ($userId != $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
    }
    header('Location: index.php');
    exit;
}

// Удаление товара
if (isset($_GET['delete_product'])) {
    $productId = (int)$_GET['delete_product'];
    $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if ($product && $product['image_path'] && file_exists('../' . $product['image_path'])) {
        unlink('../' . $product['image_path']);
    }
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$productId]);
    header('Location: index.php');
    exit;
}

// Обновление статуса заказа
if (isset($_POST['update_order'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $order_id]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; font-family: Arial, sans-serif; }
        .navbar { background: #333; color: white; padding: 10px; }
        .navbar a { color: white; text-decoration: none; margin-right: 20px; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: white; padding: 20px; border-radius: 8px; text-align: center; flex: 1; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-box h3 { margin: 0; font-size: 32px; color: #e94560; }
        .card { background: white; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-header { background: #e94560; color: white; padding: 12px 20px; font-size: 18px; font-weight: bold; border-radius: 8px 8px 0 0; }
        .card-body { padding: 20px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
        .btn { display: inline-block; padding: 5px 12px; background: #e94560; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 12px; }
        .btn-sm { padding: 3px 8px; font-size: 11px; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-danger { background: #dc3545; }
        .btn-success { background: #28a745; }
        .btn-info { background: #17a2b8; }
        .btn-secondary { background: #6c757d; }
        select { padding: 4px 8px; border-radius: 4px; border: 1px solid #ddd; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 11px; }
        .badge-admin { background: #e94560; color: white; }
        .badge-user { background: #6c757d; color: white; }
        img.thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="container" style="display: flex; justify-content: space-between;">
        <div><strong>ADMIN PANEL</strong> | TechPeriph</div>
        <div>
            <a href="../index.php">На сайт</a>
            <a href="../logout.php">Выйти</a>
        </div>
    </div>
</div>

<div class="container">
    <h1 style="margin-bottom: 20px;">Панель управления</h1>
    
    <!-- Статистика -->
    <div class="stats">
        <div class="stat-box"><h3><?= $users_count ?></h3><p>Пользователей</p></div>
        <div class="stat-box"><h3><?= $products_count ?></h3><p>Товаров</p></div>
        <div class="stat-box"><h3><?= $orders_count ?></h3><p>Заказов</p></div>
    </div>
    
    <!-- Пользователи -->
    <div class="card">
        <div class="card-header">Пользователи</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr><th>ID</th><th>Логин</th><th>Email</th><th>Роль</th><th>Дата</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge <?= $user['role'] == 'admin' ? 'badge-admin' : 'badge-user' ?>"><?= $user['role'] ?></span></td>
                        <td><?= $user['created_at'] ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="?delete_user=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Удалить</a>
                            <?php else: ?>
                                <span style="color:#999;">Вы</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Товары -->
    <div class="card">
        <div class="card-header">
            Товары
            <a href="../add_product.php" class="btn btn-sm" style="float: right; background:#28a745;">+ Добавить</a>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr><th>ID</th><th>Фото</th><th>Название</th><th>Автор</th><th>Цена</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod): ?>
                    <tr>
                        <td><?= $prod['id'] ?></td>
                        <td>
                            <?php if ($prod['image_path'] && file_exists('../' . $prod['image_path'])): ?>
                                <img src="../<?= $prod['image_path'] ?>" class="thumb">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/40" class="thumb">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($prod['name']) ?></td>
                        <td><?= htmlspecialchars($prod['username']) ?></td>
                        <td><?= number_format($prod['price'], 0, '.', ' ') ?> ₽</td>
                        <td>
                            <a href="../product.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-info" target="_blank">Смотреть</a>
                            <a href="../edit_product.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-warning">Ред.</a>
                            <a href="?delete_product=<?= $prod['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">Уд.</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Заказы -->
    <div class="card">
        <div class="card-header">Заказы</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr><th>ID</th><th>Пользователь</th><th>Сумма</th><th>Статус</th><th>Дата</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['username']) ?></td>
                        <td><?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽</td>
                        <td>
                            <form method="post" style="display: flex; gap: 5px;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status">
                                    <option value="new" <?= $order['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Обрабатывается</option>
                                    <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Отправлен</option>
                                    <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Доставлен</option>
                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Отменён</option>
                                </select>
                                <button type="submit" name="update_order" class="btn btn-sm">OK</button>
                            </form>
                        </td>
                        <td><?= $order['created_at'] ?></td>
                        <td><a href="../order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info" target="_blank">Детали</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>