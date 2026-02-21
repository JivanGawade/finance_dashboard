<?php
session_start();
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// NEW: Fetch user details to get the username
$userStmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$userStmt->execute([$user_id]);
$userData = $userStmt->fetch();
$username = $userData['username'] ?? 'User';
$view = $_GET['view'] ?? 'dashboard';

// 2. Fetch Totals for the Top Cards
$statsStmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
    FROM expenses WHERE user_id = ?
");
$statsStmt->execute([$user_id]);
$stats = $statsStmt->fetch();

$income = $stats['total_income'] ?? 0;
$expenses = $stats['total_expense'] ?? 0;
$balance = $income - $expenses;
$savingsRate = ($income > 0) ? round((($income - $expenses) / $income) * 100, 1) : 0;

// 3. Fetch Category Breakdown for the Chart
$catStmt = $pdo->prepare("SELECT category, SUM(amount) as total FROM expenses WHERE user_id = ? AND type = 'expense' GROUP BY category");
$catStmt->execute([$user_id]);
$categories = $catStmt->fetchAll();
// 4. Fetch Recent Transactions (For Dashboard Sidebar/Card)
$transStmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = ? ORDER BY date_added DESC LIMIT 5");
$transStmt->execute([$user_id]);
$recentTrans = $transStmt->fetchAll();

if ($view == 'transactions') {
    $allTransStmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = ? ORDER BY date_added DESC");
    $allTransStmt->execute([$user_id]);
    $expenses = $allTransStmt->fetchAll(); // This populates the variable your table expects
} ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FinanceMaster | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="logo">
                <h2><i class="fas fa-chart-line"></i> Master<span>Finance</span></h2>
            </div>
            <ul class="nav-menu">
                <li class="<?= $view == 'dashboard' ? 'active' : '' ?>"><a href="index.php?view=dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="<?= $view == 'transactions' ? 'active' : '' ?>"><a href="index.php?view=transactions"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                <li class="<?= $view == 'budgets' ? 'active' : '' ?>"><a href="index.php?view=budgets"><i class="fas fa-chart-pie"></i> Budgets</a></li>
                <li class="<?= $view == 'goals' ? 'active' : '' ?>"><a href="index.php?view=goals"><i class="fas fa-bullseye"></i> Goals</a></li>
                <li class="divider"></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
            <button class="btn-new-transaction" onclick="openModal()"><i class="fas fa-plus-circle"></i> New Entry</button>
        </nav>

        <main class="main-content">
            <header class="main-header">
                <h1>Financial Dashboard</h1>
                <div class="user-profile">
                    <p>Welcome, <i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?></p>
                   
                </div>
            </header>

            <?php if ($view == 'dashboard'): ?>
                <section class="stats-overview">
                    <div class="stat-card income">
                        <i class="fas fa-arrow-up icon"></i>
                        <div>
                            <h3>Income</h3>
                            <p>₹<?= number_format($income, 2) ?></p>
                        </div>
                    </div>
                    <div class="stat-card expense">
                        <i class="fas fa-arrow-down icon"></i>
                        <div>
                            <h3>Expenses</h3>
                            <p>₹<?= number_format($expenses, 2) ?></p>
                        </div>
                    </div>
                    <div class="stat-card balance">
                        <i class="fas fa-wallet icon"></i>
                        <div>
                            <h3>Net Balance</h3>
                            <p>₹<?= number_format($balance, 2) ?></p>
                        </div>
                    </div>
                    <div class="stat-card savings">
                        <i class="fas fa-piggy-bank icon"></i>
                        <div>
                            <h3>Savings Rate</h3>
                            <p><?= $savingsRate ?>%</p>
                        </div>
                    </div>
                </section>

                <section class="dashboard-grid">
                    <div class="chart-card">
                        <h3>Expenses by Category</h3>
                        <canvas id="categoryChart"></canvas>
                    </div>

                    <div class="activity-card">
                        <h3>Recent Transactions</h3>
                        <div class="trans-list">
                            <?php foreach ($recentTrans as $t): ?>
                                <div class="trans-item">
                                    <div class="trans-info">
                                        <strong><?= $t['category'] ?></strong>
                                        <small><?= $t['date_added'] ?></small>
                                    </div>
                                    <span class="<?= $t['type'] ?>">
                                        <?= $t['type'] == 'income' ? '+' : '-' ?> ₹<?= number_format($t['amount'], 2) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

            <?php elseif ($view == 'transactions'): ?>
                <div class="table-card">
                    <h2>History</h2>
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // 1. Check if the variable is valid and has data
                            if (isset($expenses) && (is_array($expenses) || is_object($expenses))):

                                // 2. Loop through the actual data
                                foreach ($expenses as $e): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($e['date_added']) ?></td>
                                        <td><?= htmlspecialchars($e['category']) ?></td>
                                        <td class="<?= htmlspecialchars($e['type']) ?>">
                                            <?= ucfirst(htmlspecialchars($e['type'])) ?>
                                        </td>
                                        <td>₹<?= number_format($e['amount'], 2) ?></td>
                                        <td>
                                            <a href="delete_expense.php?id=<?= $e['id'] ?>"
                                                onclick="return confirm('Are you sure you want to delete this?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;

                            // 3. What to show if there is no data
                            else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">No data available or invalid format.</td>
                                </tr>
                            <?php endif; ?>
                        <?php elseif ($view == 'budgets'): ?>
                            <div class="table-card">
                                <h2>Budget Management</h2>
                                <p>This feature is coming soon! Here you will be able to set monthly limits for categories like Food and Rent.</p>
                            </div>

                        <?php elseif ($view == 'goals'): ?>
                            <div class="table-card">
                                <h2>Financial Goals</h2>
                                <p>Track your progress toward savings goals (e.g., "New Laptop" or "Emergency Fund") here.</p>
                            </div>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>New Transaction</h2>
            <form action="add_transaction.php" method="POST">
                <select name="type" required>
                    <option value="expense">Expense</option>
                    <option value="income">Income</option>
                </select>
                <input type="number" name="amount" placeholder="0.00" step="0.01" required>
                <input type="text" name="category" placeholder="e.g. Food, Salary, Rent" required>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                <button type="submit">Save Entry</button>
            </form>
        </div>
    </div>

    <script>
        // Data for Chart
        const catLabels = <?= json_encode(array_column($categories, 'category')) ?>;
        const catData = <?= json_encode(array_column($categories, 'total')) ?>;

        const ctx = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: ['#4361ee', '#2ec4b6', '#e71d36', '#f39c12', '#9b59b6']
                }]
            }
        });

        function openModal() {
            document.getElementById('modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>

</html>