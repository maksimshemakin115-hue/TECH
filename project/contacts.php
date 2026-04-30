<?php
require_once 'config.php';
$cart_count = getCartCount($pdo, $_SESSION['user_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Контакты — TechPeriph</title>
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

<div class="container">
    <h1 class="page-title">Контакты</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3>Наш адрес</h3>
                    <p>г. Ижевск, ТЦ "Сигма", 2 этаж</p>
                    <h3>Телефон</h3>
                    <p>+7 (495) 123-45-67</p>
                    <h3>Email</h3>
                    <p>support@techperiph.ru</p>
                    <h3>Режим работы</h3>
                    <p>Ежедневно: 10:00 – 21:00</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3>Свяжитесь с нами</h3>
                    <form>
                        <div class="mb-3"><input type="text" class="form-control" placeholder="Ваше имя"></div>
                        <div class="mb-3"><input type="email" class="form-control" placeholder="Ваш Email"></div>
                        <div class="mb-3"><textarea class="form-control" rows="5" placeholder="Сообщение"></textarea></div>
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </form>
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