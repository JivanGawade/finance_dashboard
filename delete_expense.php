<?php
session_start();
require 'db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Verify the expense belongs to the logged-in user before deleting
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
}

header("Location: index.php");
exit();
?>