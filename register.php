<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$_POST['username'], $hashed]);
    header("Location: index.php");
}
?>
<link rel="stylesheet" href="style.css">
<form method="POST" class="login-card">
    <h2>Register</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Create Account</button><br>
    <p>Already registered ? <a href="login.php">Login here</a></p>
</form>