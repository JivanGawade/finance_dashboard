**Finance Dashboard (FinanceMaster)**
A web-based financial tracking application designed to help users manage their income, track expenses, and visualize their spending habits.🚀 FeaturesSecure Authentication: Includes user registration, login, and secure session management using PHP password hashing.  Interactive Dashboard: Displays real-time financial metrics including total income, total expenses, net balance, and a calculated savings rate.  Transaction Management: Users can easily add new income or expense entries categorized by type and date, as well as delete specific transactions.  Data Visualization: Integrates Chart.js to render a dynamic doughnut chart that breaks down expenses by category.  Transaction History: Provides a tabular view of all recent transactions for easy auditing.  🛠️ Tech StackFrontend: HTML, CSS, JavaScript, and FontAwesome icons.  Backend: PHP with PDO (PHP Data Objects) for secure database interactions.  Database: MySQL.  Libraries: Chart.js for data visualization.  📋 PrerequisitesTo run this project locally, you will need a local server environment such as XAMPP, WAMP, or MAMP with PHP and MySQL enabled.⚙️ Installation & SetupClone or Download the Repository:Place the project files inside your local server's root directory (e.g., htdocs for XAMPP).Database Configuration:Open phpMyAdmin (or your preferred MySQL manager).Execute the following SQL commands to create the database and required tables:  SQLCREATE DATABASE IF NOT EXISTS finance_db;
USE finance_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    date_added DATE NOT NULL,
    type VARCHAR(50) NOT NULL, 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
(Note: Ensure your expenses table includes the type column as it is required for adding transactions).  Update Connection Settings:
The project connects to the database via the db.php file. By default, it is configured for a standard local setup:  Host: localhost  Database: finance_db  User: root  Password:   (Empty)  Update these values in db.php if your local database credentials differ.  Launch the Application:
Open your web browser and navigate to http://localhost/your-folder-name/register.php to create an account and start managing your finances
