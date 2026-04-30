<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$order_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order && !isAdmin()) {
    die("Заказ не найден");
}

if (isAdmin() && !$order) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    if (!$order) die("Заказ не найден");
}

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$cart_count = getCartCount($pdo, $_SESSION['user_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ номер <?= $order_id ?> — TechPeriph</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo">TECH<span>PERIPH</span></div>
            <div><span class="me-3">Привет, <?= htmlspecialchars($_SESSION['username']) ?></span></div>
        </div>
    </div>
</div>

<div class="nav-menu">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center flex-wrap">
            <a href="index.php">Главная</a>
            <a href="catalog.php">Каталог</a>
            <a href="cart.php" class="btn-cart">Корзина (<?= $cart_count ?>)</a>
            <a href="orders.php">Заказы</a>
            <?php if (isAdmin()): ?><a href="admin/index.php">Админ-панель</a><?php endif; ?>
            <a href="logout.php">Выйти</a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h1 class="page-title">Заказ номер <?= $order_id ?></h1>
    <a href="orders.php" class="btn btn-secondary mb-3">← Мои заказы</a>
    
    <div class="card">
        <div class="card-body">
            <p><strong>Дата:</strong> <?= $order['created_at'] ?></p>
            <p><strong>Статус:</strong> <?= $order['status'] ?></p>
            <p><strong>Общая сумма:</strong> <?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽</p>
            
            <h4 class="mt-4">Товары в заказе</h4>
            <table class="table table-bordered">
                <thead><tr><th>Товар</th><th>Цена</th><th>Количество</th><th>Сумма</th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr><td><?= htmlspecialchars($item['product_name']) ?></td><td><?= number_format($item['product_price'], 0, '.', ' ') ?> ₽</td><td><?= $item['quantity'] ?></td><td><?= number_format($item['product_price'] * $item['quantity'], 0, '.', ' ') ?> ₽</td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4"><h5>Свяжитесь с нами</h5><p>г. Ижевск, ТЦ "Сигма"<br>support@techperiph.ru<br>+7 (495) 123-45-67</p></div>
            <div class="col-md-4 mb-4"><h5>Информация</h5><p><a href="about.php">О нас</a><br><a href="delivery.php">Доставка</a><br><a href="guarantee.php">Гарантия</a></p></div>
            <div class="col-md-4 mb-4"><h5>Преимущества</h5><p><strong>Быстрая доставка</strong><br>По всей России за 1-3 дня.</p></div>
        </div>
        <hr><div class="text-center"><p class="mb-0">© 2025 TechPeriph</p></div>
    </div>
</footer>
</body>
</html>