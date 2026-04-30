<?php
require_once 'config.php';
requireAdmin();

$product_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Товар не найден");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_path = $product['image_path'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            if ($product['image_path'] && file_exists($product['image_path'])) {
                unlink($product['image_path']);
            }
            $image_path = $destination;
        } else {
            $error = 'Ошибка загрузки нового файла';
        }
    }

    if (empty($error) && !empty($name) && !empty($description) && $price > 0) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_path = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $image_path, $product_id]);
        $success = 'Товар обновлён!';
    } elseif (empty($error)) {
        $error = 'Заполните все поля корректно (цена > 0)';
    }
}

$cart_count = getCartCount($pdo, $_SESSION['user_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование товара — TechPeriph</title>
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
            <a href="admin/index.php">Админ-панель</a>
            <a href="logout.php">Выйти</a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h1 class="page-title">Редактирование товара</h1>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="product.php?id=<?= $product_id ?>">Вернуться к товару</a></div><?php endif; ?>
    
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3"><label>Название</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required></div>
                        <div class="mb-3"><label>Описание</label><textarea name="description" rows="5" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea></div>
                        <div class="mb-3"><label>Цена (руб.)</label><input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required></div>
                        <div class="mb-3">
                            <label>Текущее фото</label><br>
                            <?php if ($product['image_path'] && file_exists($product['image_path'])): ?>
                                <img src="<?= $product['image_path'] ?>" style="max-height: 150px;" class="mb-2"><br>
                            <?php endif; ?>
                            <label>Заменить фото</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                        <a href="product.php?id=<?= $product_id ?>" class="btn btn-secondary">Отмена</a>
                    </form>
                </div>
            </div>
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