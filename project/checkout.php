<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.id as cart_id, c.quantity, p.* 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $order_id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['id'], $item['name'], $item['price'], $item['quantity']]);
        }
        
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        $pdo->commit();
        
        $_SESSION['order_success'] = "Заказ номер $order_id успешно оформлен!";
        header('Location: orders.php');
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Ошибка оформления заказа: " . $e->getMessage();
    }
}

$cart_count = getCartCount($pdo, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа — TechPeriph</title>
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
            <a href="logout.php">Выйти</a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h1 class="page-title">Оформление заказа</h1>
    
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Ваши товары</h5></div>
                <div class="card-body">
                    <table class="table"><thead><tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th></tr></thead>
                    <tbody><?php foreach ($cart_items as $item): ?>
                        <tr><td><?= htmlspecialchars($item['name']) ?></td><td><?= number_format($item['price'], 0, '.', ' ') ?> ₽</td><td><?= $item['quantity'] ?></td><td><?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?> ₽</td></tr>
                    <?php endforeach; ?></tbody>
                    </table>
                    <h5 class="text-end">Итого: <?= number_format($total, 0, '.', ' ') ?> ₽</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white"><h5 class="mb-0">Подтверждение заказа</h5></div>
                <div class="card-body">
                    <p>После оформления заказа с вами свяжется менеджер.</p>
                    <form method="post"><button type="submit" class="btn btn-success w-100">Подтвердить заказ</button><a href="cart.php" class="btn btn-secondary w-100 mt-2">Вернуться</a></form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4"><h5>Свяжитесь с нами</h5><p>г. Ижевск, ТЦ "Сигма"<br>support@techperiph.ru<br>+7 (495) 123-45-67<br>Ежедневно 10:00 – 21:00</p></div>
            <div class="col-md-4 mb-4"><h5>Информация</h5><p><a href="about.php">О нас</a><br><a href="delivery.php">Доставка</a><br><a href="guarantee.php">Гарантия</a></p></div>
            <div class="col-md-4 mb-4"><h5>Преимущества</h5><p><strong>Быстрая доставка</strong><br>По всей России за 1-3 дня.</p><p><strong>Гарантия 2 года</strong><br>На всю технику.</p></div>
        </div>
        <hr><div class="text-center"><p class="mb-0">© 2025 TechPeriph — Компьютеры и лучшая периферия</p></div>
    </div>
</footer>
</body>
</html>