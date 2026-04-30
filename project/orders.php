<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$order_success = $_SESSION['order_success'] ?? null;
unset($_SESSION['order_success']);

$cart_count = getCartCount($pdo, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои заказы — TechPeriph</title>
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
    <h1 class="page-title">Мои заказы</h1>
    
    <?php if ($order_success): ?><div class="alert alert-success"><?= $order_success ?></div><?php endif; ?>
    
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">У вас пока нет заказов. <a href="index.php">Перейти к покупкам</a></div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($orders as $order): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">Заказ номер <?= $order['id'] ?> от <?= $order['created_at'] ?></div>
                        <div class="card-body">
                            <p><strong>Статус:</strong> <?php $statuses = ['new'=>'Новый','processing'=>'Обрабатывается','shipped'=>'Отправлен','delivered'=>'Доставлен','cancelled'=>'Отменён']; echo $statuses[$order['status']] ?? $order['status']; ?></p>
                            <p><strong>Сумма:</strong> <?= number_format($order['total_amount'], 0, '.', ' ') ?> ₽</p>
                            <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Подробнее</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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