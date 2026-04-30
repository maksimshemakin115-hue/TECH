<?php
require_once 'config.php';

$stmt = $pdo->query("SELECT p.*, u.username FROM products p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 8");
$products = $stmt->fetchAll();

$cart_count = getCartCount($pdo, $_SESSION['user_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechPeriph — Компьютеры и периферия</title>
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

<div class="hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 0; text-align: center;">
    <div class="container"><h1 style="font-size: 48px;">TECHPERIPH</h1><p style="font-size: 20px;">Лучшие комплектующие и периферия</p><p>Игровые ПК, клавиатуры, мыши, мониторы — всё для комфортной работы и киберспорта</p></div>
</div>

<div class="container">
    <div class="row mt-5">
        <div class="col-md-6"><div class="action-card"><div class="badge-sale">-20%</div><h3>Скидка -20% на игровые мыши</h3><p>Только до конца месяца. Logitech, Razer, HyperX.</p><div class="price">от 1 990 ₽ <span class="old-price">2 490 ₽</span></div><a href="catalog.php" class="btn-action">Купить</a></div></div>
        <div class="col-md-6"><div class="action-card"><div class="badge-sale">Хит</div><h3>Механические клавиатуры</h3><p>Красные/синие свечи, RGB подсветка, подарок — коврик.</p><div class="price">3 490 ₽</div><a href="catalog.php" class="btn-action">Подробнее</a></div></div>
        <div class="col-md-6"><div class="action-card"><div class="badge-sale">Рассрочка</div><h3>Сборка ПК за 5 дней</h3><p>Любая конфигурация, рассрочка 0% на 6 месяцев.</p><div class="price">от 45 000 ₽</div><a href="catalog.php" class="btn-action">Узнать</a></div></div>
        <div class="col-md-6"><div class="action-card"><div class="badge-sale">Подарок</div><h3>Игровые гарнитуры</h3><p>7.1 звук, шумоподавление. Подарок — подписка на game pass.</p><div class="price">2 990 ₽</div><a href="catalog.php" class="btn-action">Выбрать</a></div></div>
    </div>

    <div style="background-color: #f0f2f5; padding: 60px 0; margin: 40px 0;">
        <div class="container"><h2 class="page-title" style="margin-top: 0;">Что говорят клиенты</h2><div class="row"><div class="col-md-4"><div class="review-card"><p>"Купил монитор 240Hz — доставили за день!"</p><div class="author">— Артем</div></div></div><div class="col-md-4"><div class="review-card"><p>"Отличный сервис, помогли с выбором периферии."</p><div class="author">— Елена</div></div></div><div class="col-md-4"><div class="review-card"><p>"Собрал ПК мечты. Цены ниже рынка!"</p><div class="author">— Дмитрий</div></div></div></div></div>
    </div>

    <h2 class="page-title">Новинки</h2>
    <div class="row"><?php foreach ($products as $product): ?><div class="col-md-3 mb-4"><div class="product-card"><?php if ($product['image_path'] && file_exists($product['image_path'])): ?><img src="<?= $product['image_path'] ?>" alt="<?= htmlspecialchars($product['name']) ?>"><?php else: ?><img src="https://via.placeholder.com/300x200?text=No+Image" alt="Нет фото"><?php endif; ?><div class="product-info"><div class="product-title"><?= htmlspecialchars($product['name']) ?></div><div class="product-price"><?= number_format($product['price'], 0, '.', ' ') ?> ₽</div><p class="text-muted small mt-2"><?= mb_substr(htmlspecialchars($product['description']), 0, 60) ?>...</p><div class="d-flex justify-content-between align-items-center mt-3"><a href="product.php?id=<?= $product['id'] ?>" class="btn-outline-primary-custom">Подробнее</a><?php if (isLoggedIn()): ?><a href="add_to_cart.php?id=<?= $product['id'] ?>" class="btn-success-custom">В корзину</a><?php endif; ?></div></div></div></div><?php endforeach; ?></div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>