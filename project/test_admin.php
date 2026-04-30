<?php
session_start();
require_once 'config.php';

echo "<h2>Диагностика админа</h2>";

echo "<h3>1. Содержимое сессии:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>2. Ваш ID из сессии: " . ($_SESSION['user_id'] ?? 'НЕТ') . "</h3>";

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    echo "<h3>3. Данные из базы по вашему ID:</h3>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
    if ($user && $user['role'] === 'admin') {
        echo "<h3 style='color:green'>✓ В базе данных роль = admin</h3>";
        // Принудительно устанавливаем сессию
        $_SESSION['role'] = 'admin';
        echo "<h3 style='color:green'>✓ Сессия принудительно установлена. <a href='admin/index.php'>Проверьте админ-панель</a></h3>";
    } else {
        echo "<h3 style='color:red'>✗ В базе данных роль НЕ admin! ($user[role])</h3>";
        echo "<h3>Выполните SQL запрос: UPDATE users SET role = 'admin' WHERE id = {$_SESSION['user_id']};</h3>";
    }
} else {
    echo "<h3 style='color:red'>✗ Вы не авторизованы! <a href='login.php'>Войдите</a></h3>";
}
?>