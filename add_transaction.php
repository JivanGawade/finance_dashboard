<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $type = $_POST['type']; // 'income' or 'expense' from the dropdown

    try {
        // Prepare SQL to include the 'type' column
        $stmt = $pdo->prepare("INSERT INTO expenses (user_id, amount, category, date_added, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $amount, $category, $date, $type]);
        
        // Redirect back to dashboard with a success message
        header("Location: index.php?view=dashboard&status=success");
        exit();
    } catch (PDOException $e) {
        // Redirect with error message if something goes wrong
        header("Location: index.php?view=dashboard&status=error");
        exit();
    }
}
?>