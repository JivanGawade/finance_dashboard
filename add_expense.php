<?php
session_start();
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO expenses (user_id, amount, category, date_added) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $_POST['amount'], $_POST['category'], $_POST['date']]);
    header("Location: index.php");
}
?>
<link rel="stylesheet" href="style.css">
<form method="POST" class="login-card">
    <h2>Add Expense</h2>
    <input type="number" name="amount" placeholder="Amount" required>
    <select name="category">
        <option>Food</option><option>Rent</option><option>Transport</option>
    </select>
    <input type="date" name="date" required>
    <button type="submit">Save Expense</button>
</form>