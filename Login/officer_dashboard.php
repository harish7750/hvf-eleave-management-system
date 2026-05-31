<?php
session_start();

// Check if officer is logged in
if (!isset($_SESSION['officer_id'])) {
  echo "<script>alert('Session expired. Please login again.'); window.location.href='login.html';</script>";
  exit();
}

$officer_id = $_SESSION['officer_id'];

// Database connection
include 'db_connect.php';

// Fetch officer details
$officer_sql = "SELECT * FROM officer WHERE id = ?";
$stmt = $conn->prepare($officer_sql);
$stmt->bind_param("i", $officer_id);
$stmt->execute();
$officer_result = $stmt->get_result();

if ($officer_result->num_rows > 0) {
  $officer = $officer_result->fetch_assoc();
} else {
  $officer = ['name' => 'Not Available', 'designation' => 'N/A', 'email' => 'N/A'];
}

// Fetch leaves applied
$leaves_sql = "SELECT * FROM leaves WHERE officer_id = ?";
$leave_stmt = $conn->prepare($leaves_sql);
$leave_stmt->bind_param("i", $officer_id);
$leave_stmt->execute();
$leaves_result = $leave_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Officer Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    @font-face {
      font-family: 'Raleway';
      src: url('fonts/Raleway-Regular.ttf') format('truetype');
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Raleway', sans-serif;
      display: flex;
      min-height: 100vh;
      background: linear-gradient(to right, #dfe9f3, #ffffff);
      overflow-x: hidden;
    }

    .sidebar {
      width: 240px;
      background: #003366;
      color: white;
      padding: 30px 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      animation: slideIn 1s ease-out;
    }

    .sidebar h2 {
      font-size: 22px;
      margin-bottom: 20px;
      border-bottom: 2px solid #00ffff;
      padding-bottom: 5px;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      font-size: 17px;
      padding: 10px;
      border-left: 4px solid transparent;
      transition: 0.3s ease;
    }

    .sidebar a:hover {
      border-left: 4px solid #00ffff;
      background: #002244;
      padding-left: 15px;
      font-weight: bold;
    }

    .main {
      flex: 1;
      padding: 40px;
      animation: fadeIn 0.8s ease-in;
    }

    .logout {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #cc0000;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .logout:hover {
      background-color: #990000;
    }

    .section {
      display: none;
    }

    .section.active {
      display: block;
      animation: fadeIn 0.5s ease-in-out;
    }

    h2 {
      color: #003366;
      margin-bottom: 20px;
    }

    p {
      font-size: 16px;
      margin: 10px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      animation: fadeIn 1s ease-in;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    th {
      background: #004080;
      color: white;
    }

    select, textarea, input[type="date"] {
      width: 100%;
      padding: 8px;
      font-family: 'Raleway';
      border: 1px solid #999;
      border-radius: 4px;
      margin-top: 5px;
    }

    button[type="submit"] {
      background-color: #004080;
      color: white;
      padding: 10px 18px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      margin-top: 10px;
    }

    button[type="submit"]:hover {
      background-color: #003060;
    }

    @keyframes slideIn {
      from { transform: translateX(-100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Officer Panel</h2>
    <a href="#" onclick="showSection('info')">Officer Info</a>
    <a href="#" onclick="showSection('leaves')">Leaves Applied</a>
    <a href="#" onclick="showSection('form')">Apply for Leave</a>
  </div>

  <div class="main">
    <a href="logout.php"><button class="logout">Logout</button></a>

    <div id="info" class="section active">
      <h2>Officer Info</h2>
      <p><strong>Name:</strong> <?php echo htmlspecialchars($officer['name']); ?></p>
      <p><strong>Designation:</strong> <?php echo htmlspecialchars($officer['designation']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($officer['email']); ?></p>
    </div>

    <div id="leaves" class="section">
      <h2>Leaves Applied</h2>
      <?php if ($leaves_result->num_rows > 0): ?>
        <table>
          <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Reason</th>
          </tr>
          <?php while($leave = $leaves_result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($leave['date']); ?></td>
              <td><?php echo ucfirst(htmlspecialchars($leave['type'])); ?></td>
              <td><?php echo htmlspecialchars($leave['reason']); ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      <?php else: ?>
        <p>No leaves applied yet.</p>
      <?php endif; ?>
    </div>

    <div id="form" class="section">
      <h2>Apply Leave</h2>
      <form action="apply_leave.php" method="POST">
        <label>Date:</label>
        <input type="date" name="date" required>

        <label>Type:</label>
        <select name="type" required>
          <option value="casual">Casual</option>
          <option value="sick">Sick</option>
          <option value="earned">Earned</option>
        </select>

        <label>Reason:</label>
        <textarea name="reason" rows="4" required></textarea>

        <button type="submit">Submit</button>
      </form>
    </div>
  </div>

  <script>
    function showSection(id) {
      document.querySelectorAll('.section').forEach(el => el.classList.remove('active'));
      document.getElementById(id).classList.add('active');
    }
  </script>
</body>
</html>
