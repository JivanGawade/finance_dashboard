<?php
session_start();
require 'db.php';
$stmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM expenses WHERE user_id = ? GROUP BY category");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));