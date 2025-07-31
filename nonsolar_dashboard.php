<?php
include '../db.php';
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['message'])) {
  echo "<script>alert('" . $_SESSION['message'] . "');</script>";
  unset($_SESSION['message']);
}
date_default_timezone_set("Asia/Manila");
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// eto yung sa pag fetch ng data galing dapat to sa get_data.php kaya lang de ko alam pano nagana haha
// kaya dito ko na lang muna nilagay sa dashboard para mag ka meron ng graph
// tapos ang get_data ayy dapat naka include as a include 'db.php'; 
// ang andon kasi is   $conn = new mysqli("localhost", "root", "", "dbsolar");
//so basically pag dineploy nyo to mag kakagulo connection ng bawat isa na dapat ay nasa db.php lang 
$readings = [];
if (isset($_GET['fetch']) && $_GET['fetch'] === '1') {
    $stmt = $conn->prepare("SELECT soil, temp, humidity, timestamp FROM readings WHERE location = ? AND DATE(timestamp) = ? ORDER BY timestamp ASC");
    $location = 'nonsolar';
    $stmt->bind_param("ss", $location, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $time = [];
    $moisture = [];
    $soilTemp = [];
    $humidity = [];
    $airTemp = [];
    while ($row = $result->fetch_assoc()) {
        $time[] = date('H:i', strtotime($row['timestamp']));
        $moisture[] = $row['soil'];
        $soilTemp[] = $row['temp'];
        $humidity[] = $row['humidity'];
        $airTemp[] = $row['temp']; 
    }
    header('Content-Type: application/json');
    echo json_encode([
        'time' => $time,
        'moisture' => $moisture,
        'soil_temp' => $soilTemp,
        'humidity' => $humidity,
        'air_temp' => $airTemp
    ]);
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Non-Solar Farm Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
    }

    .main {
      flex: 1;
      background-color: #f4f4f4;
      padding: 20px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .top-nav {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 30px;
    }

    .tabs {
      display: flex;
      justify-content: center;
      flex: 1;
    }

    .tabs a {
      text-decoration: none;
      color: black;
      margin-right: 20px;
      font-weight: bold;
      padding-bottom: 5px;
      border-bottom: 2px solid transparent;
    }

    .tabs a.active {
      color: #004aad;
      border-bottom: 2px solid #004aad;
    }

    .tabs a:hover {
      opacity: 0.8;
    }

    .date-box {
      background-color: white;
      padding: 10px 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .date-box input[type="date"] {
      border: none;
      background: transparent;
      font-size: 16px;
      font-family: 'Segoe UI', sans-serif;
      cursor: pointer;
    }

    .date-box input[type="date"]:focus {
      outline: none;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .card {
      background-color: white;
      border-radius: 15px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      cursor: pointer;
      transition: transform 0.2s;
    }

    .card:hover {
      transform: scale(1.03);
    }

    .card h3 {
      margin-bottom: 10px;
    }

    .charts {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }

    .chart-box {
      background-color: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      height: 250px;
    }

    canvas {
      height: 180px !important;
    }

    .dropdown {
      position: relative;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-left: auto;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      top: 45px;
      background-color: #fff;
      min-width: 160px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      border-radius: 10px;
      z-index: 1000;
      overflow: hidden;
    }

    .dropdown-content a {
      color: #004aad;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      font-weight: 500;
      font-size: 14px;
      border-bottom: 1px solid #f0f0f0;
    }

    .dropdown-content a:last-child {
      border-bottom: none;
    }

    .dropdown-content a:hover {
      background-color: #f4f4f4;
    }

    .show {
      display: block;
    }

    .profile-btn {
      background: #fff;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.08);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
    }

    .profile-btn:hover {
      background-color: #e3f2fd;
      transform: scale(1.05);
    }

    .username {
      color: #004aad;
      font-weight: 600;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main">
    <div class="top-nav">
      <div style="display: flex; align-items: center; gap: 10px;">
        <span style="font-size:15px; color:#004aad; font-weight:600;">Filter Date</span>
        <div class="date-box"><input type="date" id="datePicker" value="<?php echo $date; ?>" /></div>
      </div>

      <div class="tabs" style="flex:1; justify-content:center;">
        <a href="dashboard.php">Solar Farm Site</a>
        <a href="nonsolar_dashboard.php" class="active">Non-Solar Farm Site</a>
      </div>

      <div style="display: flex; align-items: center; gap: 10px; margin-left: auto;">
        <span class="username"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <span class="profile-btn" style="pointer-events:none;">
          <img src="https://img.icons8.com/ios-filled/32/004aad/user-male-circle.png" alt="Profile" style="width:28px; height:28px;">
        </span>
      </div>
    </div>

    <h2 style="margin-bottom:10px; color:#004aad;">Current Readings</h2>
    <div class="cards">
      <div class="card" id="overallCard"><h3>Overall Soil Readings</h3><p id="overallVal">--</p></div>
      <div class="card"><h3>Soil Moisture</h3><p id="moistureVal">--%</p></div>
      <div class="card"><h3>Soil Temperature</h3><p id="soilTempVal">--°C</p></div>
      <div class="card"><h3>Humidity</h3><p id="humidityVal">--%</p></div>
      <div class="card"><h3>Temperature</h3><p id="airTempVal">--°C</p></div>
    </div>

    <h2 style="margin-bottom:10px; color:#004aad;">Graphical Readings</h2>
    <div class="charts">
      <div class="chart-box"><h4>Graphical Readings</h4><canvas id="overallChart"></canvas></div>
      <div class="chart-box"><h4>Soil Moisture</h4><canvas id="moistureChart"></canvas></div>
      <div class="chart-box"><h4>Soil Temperature</h4><canvas id="tempChart"></canvas></div>
      <div class="chart-box"><h4>Humidity</h4><canvas id="humidityChart"></canvas></div>
      <div class="chart-box"><h4>Temperature</h4><canvas id="temperature"></canvas></div>
    </div>

    <footer style="margin-top:auto; text-align:center; color:#888; font-size:13px; padding:10px 0;">
      &copy; <?= date('Y') ?> Non-Solar Farm Dashboard. All rights reserved.
    </footer>
  </div>

  <script>
    const overallChart = new Chart(document.getElementById('overallChart'), createChartConfig('Overall Readings', '#6a1b9a'));
    const moistureChart = new Chart(document.getElementById('moistureChart'), createChartConfig('Soil Moisture', '#007bff'));
    const tempChart = new Chart(document.getElementById('tempChart'), createChartConfig('Soil Temperature', '#ff9800'));
    const humidityChart = new Chart(document.getElementById('humidityChart'), createChartConfig('Humidity', '#00b894'));
    const airTempChart = new Chart(document.getElementById('temperature'), createChartConfig('Air Temperature', '#e74c3c'));

    function createChartConfig(label, color) {
      return {
        type: 'line',
        data: {
          labels: [],
          datasets: [{
            label: label,
            data: [],
            borderColor: color,
            backgroundColor: color + '22',
            fill: true,
            tension: 0.4,
            pointRadius: 3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: true } },
          scales: { y: { beginAtZero: false } }
        }
      };
    }

    function loadData(site, date) {
        fetch(`nonsolar_dashboard.php?site=${site}&date=${date}&fetch=1`) 
        .then(res => res.json())
        .then(data => {
          const time = data.time;
          const moisture = data.moisture.map(Number);
          const soilTemp = data.soil_temp.map(Number);
          const humidity = data.humidity.map(Number);
          const airTemp = data.air_temp.map(Number);

          // Calculate overall average for each time point
          const overall = time.map((_, i) => {
            // Average of the four metrics at each time point
            return ((moisture[i] + soilTemp[i] + humidity[i] + airTemp[i]) / 4).toFixed(2);
          });

          // Update the overallChart
          overallChart.data.labels = time;
          overallChart.data.datasets[0].data = overall;
          overallChart.update();

          // Update the other charts as before
          moistureChart.data.labels = time;
          moistureChart.data.datasets[0].data = moisture;
          moistureChart.update();

          tempChart.data.labels = time;
          tempChart.data.datasets[0].data = soilTemp;
          tempChart.update();

          humidityChart.data.labels = time;
          humidityChart.data.datasets[0].data = humidity;
          humidityChart.update();

          airTempChart.data.labels = time;
          airTempChart.data.datasets[0].data = airTemp;
          airTempChart.update();

          // Update the cards with the latest values
          document.getElementById('moistureVal').textContent = moisture.at(-1) + '%';
          document.getElementById('soilTempVal').textContent = soilTemp.at(-1) + '°C';
          document.getElementById('humidityVal').textContent = humidity.at(-1) + '%';
          document.getElementById('airTempVal').textContent = airTemp.at(-1) + '°C';

          const average = (moisture.at(-1) + soilTemp.at(-1) + humidity.at(-1) + airTemp.at(-1)) / 4;
          const overallVal = document.getElementById("overallVal");
          overallVal.textContent = average.toFixed(1);
          overallVal.style.color = average >= 60 ? "green" : average >= 40 ? "orange" : "red";
        })
        .catch(err => console.error("Failed to load data:", err));
    }

    window.onload = () => {
      const currentDate = document.getElementById("datePicker").value;
      loadData("nonsolar", currentDate);
    };

    document.getElementById("datePicker").addEventListener("change", function () {
      loadData("nonsolar", this.value);
    });

    // ...dropdown JS removed as dropdown is no longer present...
  </script>

</body>
</html>
