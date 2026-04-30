<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин/email или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход — TechPeriph</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-header bg-success text-white"><h4 class="mb-0">Авторизация</h4></div>
        <div class="card-body">
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-3"><label>Логин или Email</label><input type="text" name="login" class="form-control" required></div>
                <div class="mb-3"><label>Пароль</label><input type="password" name="password" class="form-control" required></div>
                <button type="submit" class="btn btn-success w-100">Войти</button>
            </form>
            <div class="mt-3 text-center">Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></div>
        </div>
    </div>
</div>

<footer style="margin-top: 50px;">
    <div class="container text-center"><p class="mb-0">© 2025 TechPeriph — Компьютеры и лучшая периферия</p></div>
</footer>
</body>
</html>