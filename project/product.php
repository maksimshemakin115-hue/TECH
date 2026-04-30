<?php
require_once 'config.php';

$product_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, u.username FROM products p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("<div class='container mt-5'><h3>Товар не найден</h3><a href='index.php'>Вернуться</a></div>");
}

$reviews = $pdo->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$reviews->execute([$product_id]);
$reviews = $reviews->fetchAll();

$avgRating = 0;
if (count($reviews) > 0) {
    $total = array_sum(array_column($reviews, 'rating'));
    $avgRating = round($total / count($reviews), 1);
}

$cart_count = getCartCount($pdo, $_SESSION['user_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> — TechPeriph</title>
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

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <?php if ($product['image_path'] && file_exists($product['image_path'])): ?>
                <img src="<?= $product['image_path'] ?>" class="img-fluid rounded shadow" alt="Фото">
            <?php else: ?>
                <img src="https://via.placeholder.com/500x400?text=No+Image" class="img-fluid rounded">
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <h3 class="text-success mt-3"><?= number_format($product['price'], 0, '.', ' ') ?> ₽</h3>
            <hr>
            <h5>Описание:</h5>
            <p class="lead"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p class="text-muted">Добавлен: <?= htmlspecialchars($product['username']) ?></p>
            <div class="mb-3">
                <strong>Рейтинг:</strong> <?= $avgRating ?> / 5 (<?= count($reviews) ?> отзывов)
            </div>
            
            <?php if (isLoggedIn()): ?>
                <form action="add_to_cart.php" method="get" class="mt-3">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <div class="row g-2">
                        <div class="col-auto">
                            <input type="number" name="quantity" value="1" min="1" max="99" class="form-control" style="width: 80px;">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success">Добавить в корзину</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            
            <div class="mt-3">
                <a href="index.php" class="btn btn-secondary">Назад</a>
                <?php if (isAdmin()): ?>
                    <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-warning">Редактировать</a>
                    <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Удалить товар?')">Удалить</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-8 mx-auto">
            <h3>Отзывы покупателей</h3>
            
            <?php if (isset($_SESSION['review_error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['review_error']; unset($_SESSION['review_error']); ?></div>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Оставить отзыв</h5>
                        <form action="add_review.php" method="post">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <div class="mb-2">
                                <label>Оценка (1-5):</label>
                                <select name="rating" class="form-select w-auto d-inline-block ms-2">
                                    <option value="5">5</option>
                                    <option value="4">4</option>
                                    <option value="3">3</option>
                                    <option value="2">2</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <textarea name="comment" rows="3" class="form-control" placeholder="Ваш комментарий..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Отправить отзыв</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <p><a href="login.php">Войдите</a>, чтобы оставить отзыв.</p>
            <?php endif; ?>

            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?= htmlspecialchars($review['username']) ?></strong>
                                    <span class="ms-2">Оценка: <?= $review['rating'] ?>/5</span>
                                </div>
                                <small class="text-muted"><?= $review['created_at'] ?></small>
                            </div>
                            <p class="mt-2"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            <?php if (isAdmin()): ?>
                                <a href="delete_review.php?id=<?= $review['id'] ?>&product_id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить отзыв?')">Удалить</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">Пока нет отзывов. Будьте первым!</div>
            <?php endif; ?>
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