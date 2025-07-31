<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
date_default_timezone_set("Asia/Manila");
$today = date("Y-m-d");
$currentPage = 'report'; 
// Ensure filter variables are set before HTML output
$from = isset($_GET['from']) ? $_GET['from'] : $today;
$to = isset($_GET['to']) ? $_GET['to'] : $today;
$site = isset($_GET['site']) ? $_GET['site'] : 'solar';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sensor Report</title>
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
    h2 {
      color: #004aad;
      margin-top: 0;
    }
    .filters {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .filters label {
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 5px;
      display: block;
    }
    .filters input,
    .filters select {
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-family: 'Segoe UI', sans-serif;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
      font-size: 14px;
    }
    th {
      background-color: #004aad;
      color: white;
    }
    .export-btn {
      background: #004aad;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }
    .export-btn:hover {
      background: #00337a;
    }
  </style>
</head>
<body>

<!-- ✅ Use sidebar.php with active tab support -->
<?php include 'sidebar.php'; ?>

<div class="main">
  <h2>Sensor Data Report</h2>

  <form class="filters" method="GET">
    <div>
      <label for="from">From:</label>
      <input type="date" name="from" id="from" value="<?php echo htmlspecialchars($from); ?>">
    </div>
    <div>
      <label for="to">To:</label>
      <input type="date" name="to" id="to" value="<?php echo htmlspecialchars($to); ?>">
    </div>
    <div>
      <label for="site">Site:</label>
      <select name="site" id="site">
        <option value="solar" <?php if($site==="solar") echo "selected"; ?>>Solar Farm</option>
        <option value="non_solar" <?php if($site==="non_solar") echo "selected"; ?>>Non-Solar Farm</option>
      </select>
    </div>
    <div style="align-self: end;">
      <button type="submit" class="export-btn">View Report</button>
    </div>
  </form>

  <table>
    <thead>
      <tr>
        <th>Timestamp</th>
        <th>Soil Moisture</th>
        <th>Soil Temp</th>
        <th>Humidity</th>
        <th>Air Temp</th>
      </tr>
    </thead>
    <tbody>
      <?php
      include '../db.php';
      // Normalize site name for DB
      $dbSite = ($site === 'non_solar') ? 'nonsolar' : 'solar';
      // Pagination setup
      $limit = 10; // records per page
      $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
      if ($page < 1) $page = 1;
      $offset = ($page - 1) * $limit;
      // Count total records for pagination
      $count_sql = "SELECT COUNT(*) as total FROM readings WHERE location = '$dbSite' AND DATE(timestamp) BETWEEN '$from' AND '$to'";
      $count_result = mysqli_query($conn, $count_sql);
      $total_records = 0;
      if ($count_result && $row = mysqli_fetch_assoc($count_result)) {
        $total_records = (int)$row['total'];
      }
      $total_pages = ($total_records > 0) ? ceil($total_records / $limit) : 1;
      // Query sensor readings for the selected site and date range with LIMIT
      $sql = "SELECT * FROM readings WHERE location = '$dbSite' AND DATE(timestamp) BETWEEN '$from' AND '$to' ORDER BY timestamp ASC LIMIT $limit OFFSET $offset";
      $result = mysqli_query($conn, $sql);
      if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          echo '<tr>';
          echo '<td>' . date('Y-m-d h:i A', strtotime($row['timestamp'])) . '</td>';
          echo '<td>' . $row['soil'] . '%</td>';
          echo '<td>' . $row['temp'] . '°C</td>';
          echo '<td>' . $row['humidity'] . '%</td>';
          echo '<td>' . $row['temp'] . '°C</td>';
          echo '</tr>';
        }
      } else {
        echo '<tr><td colspan="5" style="text-align:center;color:#888;">No data found for selected date/site.</td></tr>';
      }
      ?>
    </tbody>
  </table>
  <?php
  // Pagination controls
  if ($total_pages > 1) {
    echo '<div style="margin-top:30px; text-align:center;">';
    $queryStr = http_build_query(array_merge($_GET, ['page' => '']));
    $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
    $getPageUrl = function($p) use ($baseUrl, $queryStr) {
      return $baseUrl . '?' . preg_replace('/page=([^&]*)/', '', $queryStr) . 'page=' . $p;
    };
    // Pagination styles
    echo '<style>
      .pagination-btn { display:inline-block; margin:0 3px; padding:5px 12px; border-radius:4px; border:none; background:#f0f0f0; color:#004aad; font-size:15px; text-decoration:none; cursor:pointer; transition:background 0.2s; }
      .pagination-btn:hover:not([disabled]) { background:#e0eaff; }
      .pagination-btn[disabled] { color:#aaa; background:#f9f9f9; cursor:default; pointer-events:none; }
      .pagination-active { background:#004aad; color:white !important; font-weight:bold; }
      .pagination-info { margin:0 10px; color:#555; font-size:15px; }
    </style>';
    // First button
    echo '<a class="pagination-btn" href="' . $getPageUrl(1) . '" ' . ($page == 1 ? 'disabled' : '') . '>First</a>';
    // Prev button
    echo '<a class="pagination-btn" href="' . $getPageUrl($page-1) . '" ' . ($page == 1 ? 'disabled' : '') . '>&laquo; Prev</a>';
    // Page numbers (show max 5 around current)
    $start = max(1, $page-2);
    $end = min($total_pages, $page+2);
    if ($start > 1) echo '<span class="pagination-info">...</span>';
    for ($i = $start; $i <= $end; $i++) {
      if ($i == $page) {
        echo '<span class="pagination-btn pagination-active">' . $i . '</span>';
      } else {
        echo '<a class="pagination-btn" href="' . $getPageUrl($i) . '">' . $i . '</a>';
      }
    }
    if ($end < $total_pages) echo '<span class="pagination-info">...</span>';
    // Next button
    echo '<a class="pagination-btn" href="' . $getPageUrl($page+1) . '" ' . ($page == $total_pages ? 'disabled' : '') . '>Next &raquo;</a>';
    // Last button
    echo '<a class="pagination-btn" href="' . $getPageUrl($total_pages) . '" ' . ($page == $total_pages ? 'disabled' : '') . '>Last</a>';
    // Page info
    echo '<span class="pagination-info">Page ' . $page . ' of ' . $total_pages . '</span>';
    echo '</div>';
  }
  ?>
</div>

</body>
</html>
