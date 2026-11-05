<?php
// ─────────────────────────────────────────────────────────────
// includes/db.php — MySQL connection using mysqli
// ─────────────────────────────────────────────────────────────

$host   = "localhost";
$dbname = "finance_tracker";
$user   = "root";
$pass   = "";   // XAMPP default is blank

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("<p style='color:red;font-family:sans-serif;padding:2rem;'>
        <strong>Database connection failed:</strong> " . mysqli_connect_error() . "<br><br>
        Make sure:<br>
        1. XAMPP MySQL is running<br>
        2. The database <strong>finance_tracker</strong> exists in phpMyAdmin<br>
        3. You have run the <strong>finance_tracker_setup.sql</strong> file
    </p>");
}
