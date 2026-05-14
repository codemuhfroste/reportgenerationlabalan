<?php
require_once 'Database.php';
$db = (new Database())->connect();

$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->execute([$hashed_password]);

echo "<h3>Success!</h3><p>The admin password has been securely updated to <b>admin123</b>.</p>";
echo "<a href='index.php'>Click here to go back to the Login Page</a>";
?>