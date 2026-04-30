<?php
require_once 'config.php';

$sort = $_GET['sort'] ?? 'new';
if ($sort == 'price_asc') $order = "ORDER BY price ASC";
elseif ($sort == 'price_desc') $order = "ORDER BY price DESC";
else $order = "ORDER BY created_at DESC";

$stmt = $pdo->query("SELECT p.*, u.username FROM products p JOIN users u ON p.user_id = u.id $order");
$products = $stmt->fetchAll();

$cart_count = getCartCount($pdo, $_SESSION['user_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог — TechPeriph</title>
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
    <h1 class="page-title">Каталог товаров</h1>
    <div class="row" id="products-container">
        <?php foreach ($products as $product): ?>
            <div class="col-md-3 mb-4 product-item" data-name="<?= strtolower(htmlspecialchars($product['name'])) ?>">
                <div class="product-card">
                    <?php if ($product['image_path'] && file_exists($product['image_path'])): ?>
                        <img src="<?= $product['image_path'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x200?text=No+Image" alt="Нет фото">
                    <?php endif; ?>
                    <div class="product-info">
                        <div class="product-title"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="product-price"><?= number_format($product['price'], 0, '.', ' ') ?> ₽</div>
                        <p class="text-muted small mt-2"><?= mb_substr(htmlspecialchars($product['description']), 0, 60) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn-outline-primary-custom">Подробнее</a>
                            <?php if (isLoggedIn()): ?>
                                <a href="add_to_cart.php?id=<?= $product['id'] ?>" class="btn-success-custom">В корзину</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (count($products) === 0): ?><div class="col-12"><div class="alert alert-info text-center">Товаров пока нет.</div></div><?php endif; ?>
    </div>
</div>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4"><h5>Свяжитесь с нами</h5><p>г. Ижевск, ТЦ "Сигма"<br>support@techperiph.ru<br>+7 (495) 123-45-67<br>Ежедневно 10:00 – 21:00</p></div>
            <div class="col-md-4"><h5>Информация</h5><p><a href="about.php">О нас</a><br><a href="delivery.php">Доставка и оплата</a><br><a href="guarantee.php">Гарантия</a><br><a href="contacts.php">Контакты</a></p></div>
            <div class="col-md-4"><h5>Преимущества</h5><p><strong>Быстрая доставка</strong><br>По всей России за 1-3 дня.</p><p><strong>Гарантия 2 года</strong><br>На всю технику и периферию.</p><p><strong>Рассрочка</strong><br>Без переплат, одобрение за 5 минут.</p></div>
        </div>
        <hr><div class="text-center"><p class="mb-0">© 2025 TechPeriph — Компьютеры и лучшая периферия</p></div>
    </div>
</footer>

<script>
document.getElementById('search')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(function(item) {
        item.style.display = item.getAttribute('data-name').indexOf(filter) > -1 ? '' : 'none';
    });
});
</script>
</body>
</html>