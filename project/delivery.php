<?php
require_once 'config.php';
$cart_count = getCartCount($pdo, $_SESSION['user_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Доставка и оплата — TechPeriph</title>
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
            <a href="catalog.php">Товары</a>
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

<div class="container">
    <h1 class="page-title">Доставка и оплата</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h3>Доставка</h3>
                    <p><strong>По всей России:</strong> 1-3 дня (СДЭК, Почта России)</p>
                    <p><strong>По Ижевску:</strong> день в день, курьером</p>
                    <p><strong>Самовывоз:</strong> ТЦ "Сигма", 2 этаж</p>
                    <p><strong>Стоимость доставки:</strong></p>
                    <ul>
                        <li>При заказе от 5 000 ₽ — бесплатно</li>
                        <li>До 5 000 ₽ — 300 ₽</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h3>Оплата</h3>
                    <p><strong>Способы оплаты:</strong></p>
                    <ul>
                        <li>Банковской картой онлайн</li>
                        <li>Наличными при получении</li>
                        <li>Безналичный расчёт для юр. лиц</li>
                        <li>Рассрочка 0% на 6 месяцев</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
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