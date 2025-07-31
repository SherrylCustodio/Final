<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../db.php';  
date_default_timezone_set("Asia/Manila");
$date = date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sensor Status</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
    }
    .main {
      flex: 1;
      padding: 20px;
      background-color: #f4f4f4;
    }
    .main h2 {
      color: #004aad;
      margin-top: 0;
    }
    .status-table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
    }
    .status-table th, .status-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .status-table th {
      background-color: #004aad;
      color: white;
    }
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: bold;
      color: white;
      display: inline-block;
    }
    .active { background-color: #4caf50; }
    .disconnected { background-color: #f44336; }
    .idle { background-color: #ff9800; }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>
  <div class="main">
    <h2>Solar Farm Status</h2>
    <table class="status-table">
      <thead>
        <tr>
          <th>Sensor</th>
          <th>Last Reported</th>
          <th>Status</th>
        </tr>
      </thead>
     <tbody>
<?php
// SOLAR FARM DATA
$sql = "SELECT * FROM sensor_status WHERE farm_type = 'solar' ORDER BY last_reported DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
  $statusClass = strtolower($row['status']); // e.g. "active"
  echo "<tr>
          <td>{$row['sensor_name']}</td>
          <td>" . date("Y-m-d h:i A", strtotime($row['last_reported'])) . "</td>
          <td><span class='status-badge {$statusClass}'>{$row['status']}</span></td>
        </tr>";
}
?>
</tbody>
      </table>
      </div>

  <div class="main">
    <h2>Non-solar Farm Status</h2>
    <table class="status-table">
      <thead>
        <tr>
          <th>Sensor</th>
          <th>Last Reported</th>
          <th>Status</th>
        </tr>
      </thead>
            <tbody>
<?php
// NON-SOLAR FARM DATA
$sql = "SELECT * FROM sensor_status WHERE farm_type = 'non-solar' ORDER BY last_reported DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
  $statusClass = strtolower($row['status']);
  echo "<tr>
          <td>{$row['sensor_name']}</td>
          <td>" . date("Y-m-d h:i A", strtotime($row['last_reported'])) . "</td>
          <td><span class='status-badge {$statusClass}'>{$row['status']}</span></td>
        </tr>";
}
?>
      </tbody>
    </table>
  </div>
</body>
</html>
