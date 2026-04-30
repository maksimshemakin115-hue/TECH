<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) $rating = 5;
    if (empty($comment)) {
        $_SESSION['review_error'] = 'Комментарий не может быть пустым';
        header("Location: product.php?id=$product_id");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$product_id, $_SESSION['user_id'], $rating, $comment]);

    header("Location: product.php?id=$product_id");
    exit;
}
?>