<?php
session_start();
// Determine which tab to show on load
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : (isset($_SESSION['user_id']) ? 'dashboard' : 'login');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AVD Burger Information System</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <div class="header">
    AVD Burger 
  </div>

  <div id="nav-menu" class="nav-bar" style="display: <?php echo isset($_SESSION['user_id']) ? 'block' : 'none'; ?>;">
    <button onclick="showSection('dashboard')">Dashboard</button>
    <button onclick="showSection('reports')">Report Generator</button>
    
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
        <button onclick="showSection('users')">User Management</button>
    <?php endif; ?>
    
    <button onclick="showSection('about')">About</button>
    <form action="actions.php" method="POST" style="display:inline;">
        <button type="submit" name="logout" class="logout-btn">Log Out</button>
    </form>
  </div>

  <div class="container">
    
<div id="login" class="module-section content-box" style="display: none;">
      <h2>Employee Login</h2>
      <?php if(isset($_SESSION['error'])) { echo "<p style='color:#e74c3c; font-weight:bold; text-align:center;'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); } ?>
      <form action="actions.php" method="POST">
          <input type="text" name="username" placeholder="Username" class="input-field" required>
          <input type="password" name="password" placeholder="Password" class="input-field" required>
          <button type="submit" name="login" class="full-width-btn">Log In</button>
      </form>
      <p class="link-text" onclick="showSection('recovery')">Forgot Password?</p>
    </div>

<div id="recovery" class="module-section content-box" style="display: none;">
      <h2>Password Recovery</h2>
      <form action="actions.php" method="POST">
          <p>Enter your employee email to receive a reset link.</p>
          <input type="email" name="email" placeholder="Employee Email" class="input-field" required>
          <button type="submit" name="recover" class="full-width-btn" onclick="alert('Recovery link sent to your email!')">Send Link</button>
      </form>
      <p class="link-text" onclick="showSection('login')">Back to Login</p>
    </div>

<div id="dashboard" class="module-section" style="display: none;">
    
    <div class="transaction-header">
        <h2>TRANSACTION # 0001</h2>
        <h3><?php echo date('F j, Y'); ?></h3>
    </div>

    <div class="dashboard-flex">
        
        <div class="menu-panel">
            <h4>Cashier Dashboard - Menu</h4>
            
            <div class="menu-item">
              <span>Classic Burger (₱65.00)</span>
              <button type="button" onclick="punchOrder('Classic Burger', 65)">Punch Order</button>
            </div>
            <div class="menu-item">
              <span>Cheese Burger (₱85.00)</span>
              <button type="button" onclick="punchOrder('Cheese Burger', 85)">Punch Order</button>
            </div>
            <div class="menu-item">
              <span>Bacon Burger (₱110.00)</span>
              <button type="button" onclick="punchOrder('Bacon Burger', 110)">Punch Order</button>
            </div>

            <div class="order-summary">
              <strong>Transaction Summary:</strong>
              <div id="transaction-items"></div>
            </div>
        </div>

        <div class="receipt-panel">
            <div id="receipt-items">
                
              </div>
              
              <div class="receipt-total">
                  <span>TOTAL</span>
                  <span id="receipt-total-price">₱0.00</span>
              </div>
          </div>

      </div>
    </div>

<?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
    <div id="users" class="module-section content-box" style="display: none;">
      <h2>User Management</h2>
      <form action="index.php" method="GET" style="margin-bottom: 15px; display: flex; gap: 10px;">
          <input type="hidden" name="tab" value="users">
          <input type="text" name="search" placeholder="Search accounts..." class="input-field" style="margin: 0;">
          <button type="submit" style="width: 30%;">Search</button>
      </form>
      <form action="actions.php" method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #eee;">
          <h4 style="margin-top: 0;">Add / Update User</h4>
          <input type="text" name="username" placeholder="Username" class="input-field" required>
          <input type="password" name="password" placeholder="Password (leave blank if updating)" class="input-field">
          <select name="role" class="input-field" required>
              <option value="Staff">Staff (Cashier)</option>
              <option value="Admin">Admin (Manager)</option>
          </select>
          <select name="status" class="input-field">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
          </select>
          <button type="submit" name="add_user" class="full-width-btn">Save Account</button>
      </form>
      <table class="report-table">
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Status</th><th>Action</th></tr>
        <?php
            require_once 'Database.php';
            $db = (new Database())->connect();
            $searchQuery = isset($_GET['search']) ? "%".$_GET['search']."%" : "%";
            $stmt = $db->prepare("SELECT id, username, role, status FROM users WHERE username LIKE ?");
            $stmt->execute([$searchQuery]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $statusColor = ($row['status'] == 'Active') ? '#27ae60' : '#e74c3c';
                echo "<tr>";
                echo "<td>{$row['id']}</td><td><b>{$row['username']}</b></td><td>{$row['role']}</td>";
                echo "<td style='color: {$statusColor}; font-weight: bold;'>{$row['status']}</td>";
                echo "<td><form action='actions.php' method='POST' style='display:inline; margin:0;'><input type='hidden' name='user_id' value='{$row['id']}'><input type='hidden' name='current_status' value='{$row['status']}'><button type='submit' name='toggle_status' style='padding: 6px 10px; font-size:12px; width: 100%;'>Toggle</button></form></td></tr>";
            }
        ?>
      </table>
    </div>
    <?php endif; ?>

<div id="reports" class="module-section content-box" style="display: none;">
      <h2>Generate Reports</h2>
      <p>Select a date range to generate the daily sales report.</p>
      <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 15px;">
        <input type="date" class="input-field" style="margin: 0;"><span>to</span><input type="date" class="input-field" style="margin: 0;">
      </div>
      <button onclick="alert('Generating PDF Report...')" class="full-width-btn">Generate Sales Report</button>
      <table class="report-table">
        <tr><th>Item</th><th>Qty</th><th>Total</th></tr>
        <tr><td>Classic Burger</td><td>15</td><td>₱975.00</td></tr>
      </table>
    </div>

<div id="about" class="module-section content-box" style="display: none;">
      <h2>About The Program</h2>
      <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
          <p style="margin-top: 0;"><strong>System Name:</strong> AVD Burger POS & Management System</p>
          <p><strong>Version:</strong> 1.0.0</p>
          <p><strong>Developer:</strong> Jose Manuel</p>
      </div>
    </div>

  </div>

  <script src="script.js"></script>
  <script>showSection('<?php echo $activeTab; ?>');</script>
</body>
</html>