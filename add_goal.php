<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['goal_name'];
    $target = $_POST['target_amount'];
    $current = $_POST['current_amount'] ?? 0;
    $deadline = $_POST['deadline'];

    try {
        $stmt = $pdo->prepare("INSERT INTO goals (user_id, goal_name, target_amount, current_amount, deadline) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $name, $target, $current, $deadline]);
        header("Location: index.php?view=goals&status=success");
    } catch (PDOException $e) {
        header("Location: index.php?view=goals&status=error");
    }
    exit();
}