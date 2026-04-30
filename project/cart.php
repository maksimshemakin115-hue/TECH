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

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$cart_count = array_sum(array_column($cart_items, 'quantity'));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина — TechPeriph</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo">TECH<span>PERIPH</span></div>
            <div><?php if (isLoggedIn()): ?><span class="me-3">Привет, <?= htmlspecialchars($_SESSION['username']) ?></span><?php endif; ?></div>
        </div>
        <div class="mt-2"><p class="mb-0">Лучшие комплектующие и периферия</p><small>Игровые ПК, клавиатуры, мыши, мониторы — всё для комфортной работы и киберспорта</small></div>
    </div>
</div>

<div class="nav-menu">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center flex-wrap">
            <a href="index.php">Главная</a>
            <a href="catalog.php">Каталог</a>
            <a href="actions.php">Акции</a>
            <a href="contacts.php">Контакты</a>
            <a href="about.php">О нас</a>
            <a href="cart.php" class="btn-cart">Корзина (<?= $cart_count ?>)</a>
            <?php if (!isLoggedIn()): ?>
                <a href="login.php">Войти</a>
                <a href="register.php">Регистрация</a>
            <?php else: ?>
                <?php if (isAdmin()): ?>
                    <a href="admin/index.php">Админ-панель</a>
                    <a href="add_product.php">Добавить товар</a>
                <?php endif; ?>
                <a href="orders.php">Заказы</a>
                <a href="logout.php">Выйти</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h1 class="page-title">Моя корзина</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info text-center">
            <h4>Корзина пуста</h4>
            <a href="index.php" class="btn btn-primary">Перейти к покупкам</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr><th>Фото</th><th>Название</th><th>Цена</th><th>Количество</th><th>Сумма</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?php if ($item['image_path'] && file_exists($item['image_path'])): ?><img src="<?= $item['image_path'] ?>" class="cart-img" alt=""><?php else: ?><img src="https://via.placeholder.com/80" class="cart-img" alt=""><?php endif; ?></td>
                                    <td><a href="product.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a></td>
                                    <td><?= number_format($item['price'], 0, '.', ' ') ?> ₽</td>
                                    <td>
                                        <form action="update_cart.php" method="post" class="d-flex">
                                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="99" class="form-control quantity-input me-2" required>
                                            <button type="submit" class="btn btn-sm btn-secondary">Обновить</button>
                                        </form>
                                    </td>
                                    <td class="fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?> ₽</td>
                                    <td><a href="remove_from_cart.php?id=<?= $item['cart_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить товар?')">Удалить</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="index.php" class="btn btn-outline-secondary">Продолжить покупки</a>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white"><h5 class="mb-0">Итого</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3"><span>Товаров:</span><span><?= count($cart_items) ?> шт.</span></div>
                        <div class="d-flex justify-content-between mb-3"><span>Общая сумма:</span><span class="h5 text-primary"><?= number_format($total, 0, '.', ' ') ?> ₽</span></div>
                        <hr>
                        <form action="checkout.php" method="post"><button type="submit" class="btn btn-success w-100 btn-lg">Оформить заказ</button></form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4"><h5>Свяжитесь с нами</h5><p>г. Ижевск, ТЦ "Сигма"<br>support@techperiph.ru<br>+7 (495) 123-45-67<br>Ежедневно 10:00 – 21:00</p></div>
            <div class="col-md-4 mb-4"><h5>Информация</h5><p><a href="about.php">О нас</a><br><a href="delivery.php">Доставка и оплата</a><br><a href="guarantee.php">Гарантия</a><br><a href="contacts.php">Контакты</a></p></div>
            <div class="col-md-4 mb-4"><h5>Преимущества</h5><p><strong>Быстрая доставка</strong><br>По всей России за 1-3 дня.</p><p><strong>Гарантия 2 года</strong><br>На всю технику и периферию.</p><p><strong>Рассрочка</strong><br>Без переплат, одобрение за 5 минут.</p></div>
        </div>
        <hr><div class="text-center"><p class="mb-0">© 2025 TechPeriph — Компьютеры и лучшая периферия</p></div>
    </div>
</footer>
</body>
</html>