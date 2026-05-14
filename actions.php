<?php
session_start();
require_once 'Database.php';
$db = (new Database())->connect();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT id, password, status, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] == 'Active') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php?tab=dashboard");
        } else {
            $_SESSION['error'] = "Account is inactive.";
            header("Location: index.php?tab=login");
        }
    } else {
        $_SESSION['error'] = "Invalid credentials.";
        header("Location: index.php?tab=login");
    }
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php?tab=login");
    exit();
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'Admin') {
    if (isset($_POST['add_user']) || isset($_POST['toggle_status'])) {
        die("Unauthorized Access: Only Admins can perform this action.");
    }
}

if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $status = $_POST['status'];
    $role = $_POST['role']; // Capture new role
    
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existing = $stmt->fetch();

    if ($existing) {
        if (!empty($_POST['password'])) {
            $hashed_pw = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET password = ?, status = ?, role = ? WHERE username = ?");
            $update->execute([$hashed_pw, $status, $role, $username]);
        } else {
            $update = $db->prepare("UPDATE users SET status = ?, role = ? WHERE username = ?");
            $update->execute([$status, $role, $username]);
        }
    } else {
        $hashed_pw = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $insert = $db->prepare("INSERT INTO users (username, password, status, role) VALUES (?, ?, ?, ?)");
        $insert->execute([$username, $hashed_pw, $status, $role]);
    }
    header("Location: index.php?tab=users");
    exit();
}

if (isset($_POST['toggle_status'])) {
    $id = $_POST['user_id'];
    $new_status = ($_POST['current_status'] == 'Active') ? 'Inactive' : 'Active';
    
    $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    
    header("Location: index.php?tab=users");
    exit();
}

if (isset($_POST['recover'])) {
    header("Location: index.php?tab=login");
    exit();
}
?>