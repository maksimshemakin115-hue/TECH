<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Заполните все поля';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким логином или email уже существует';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed]);
            $success = 'Регистрация успешна! Теперь вы можете войти.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация — TechPeriph</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h4 class="mb-0">Регистрация</h4></div>
        <div class="card-body">
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="login.php">Войти</a></div><?php endif; ?>
            <form method="post">
                <div class="mb-3"><label>Логин</label><input type="text" name="username" class="form-control" required></div>
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label>Пароль</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-3"><label>Подтверждение пароля</label><input type="password" name="confirm_password" class="form-control" required></div>
                <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
            </form>
            <div class="mt-3 text-center">Уже есть аккаунт? <a href="login.php">Войти</a></div>
        </div>
    </div>
</div>

<footer style="margin-top: 50px;">
    <div class="container text-center"><p class="mb-0">© 2025 TechPeriph — Компьютеры и лучшая периферия</p></div>
</footer>
</body>
</html>