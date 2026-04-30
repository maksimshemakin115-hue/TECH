<?php
require_once 'config.php';
requireAdmin();

$review_id = (int)$_GET['id'];
$product_id = (int)$_GET['product_id'];

$stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
$stmt->execute([$review_id]);

header("Location: product.php?id=$product_id");
exit;