<?php
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
$date = date("Y-m-d");
$currentPage = 'comparison'; // ✅ For active tab highlight
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Comparison</title>
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
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .top-nav h2 {
      color: #004aad;
      font-size: 22px;
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
      height: 280px;
    }
    canvas {
      height: 180px !important;
    }
  </style>
</head>
<body>

<!-- ✅ Include sidebar with active tab support -->
<?php include 'sidebar.php'; ?>

<div class="main">
  <div class="top-nav">
    <h2 style="margin-top:0; margin-bottom:10px; color:#004aad; font-size:20px;">Solar Farm and Non-solar Farm Comparison</h2>
    <div style="display:flex;align-items:center;gap:10px;">
      <span style="font-size:15px; color:#004aad; font-weight:600;">Filter Date</span>
      <div class="date-box">
        <input type="date" id="datePicker" value="<?php echo $date; ?>" />
      </div>
    </div>
  </div>

  <div class="charts">
    <div class="chart-box">
      <h4>Soil Moisture</h4>
      <canvas id="moistureChart"></canvas>
    </div>
    <div class="chart-box">
      <h4>Soil Temperature</h4>
      <canvas id="tempChart"></canvas>
    </div>
    <div class="chart-box">
      <h4>Humidity</h4>
      <canvas id="humidityChart"></canvas>
    </div>
    <div class="chart-box">
      <h4>Air Temperature</h4>
      <canvas id="airTempChart"></canvas>
    </div>
  </div>

  <footer style="margin-top:auto; text-align:center; color:#888; font-size:13px; padding:10px 0;">
    &copy; <?php echo date('Y'); ?> Solar Farm Dashboard. All rights reserved.
  </footer>
</div>

<script>
  const moistureChart = new Chart(document.getElementById('moistureChart'), createCompareConfig('Soil Moisture'));
  const tempChart = new Chart(document.getElementById('tempChart'), createCompareConfig('Soil Temperature'));
  const humidityChart = new Chart(document.getElementById('humidityChart'), createCompareConfig('Humidity'));
  const airTempChart = new Chart(document.getElementById('airTempChart'), createCompareConfig('Air Temperature'));

  function createCompareConfig(label) {
    return {
      type: 'line',
      data: {
        labels: [],
        datasets: [
          {
            label: 'Solar Farm',
            data: [],
            borderColor: 'green',
            backgroundColor: 'rgba(0,128,0,0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 2
          },
          {
            label: 'Non-Solar Farm',
            data: [],
            borderColor: 'blue',
            backgroundColor: 'rgba(0,0,255,0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: false } }
      }
    };
  }

  function loadComparisonData(date) {
    Promise.all([
      fetch(`get_data.php?site=solar&date=${date}`).then(res => res.json()),
      fetch(`get_data.php?site=nonsolar&date=${date}`).then(res => res.json())
    ])
    .then(([solar, nonsolar]) => {
      const labels = solar.time.length > nonsolar.time.length ? solar.time : nonsolar.time;

      moistureChart.data.labels = labels;
      moistureChart.data.datasets[0].data = solar.moisture;
      moistureChart.data.datasets[1].data = nonsolar.moisture;
      moistureChart.update();

      tempChart.data.labels = labels;
      tempChart.data.datasets[0].data = solar.soil_temp;
      tempChart.data.datasets[1].data = nonsolar.soil_temp;
      tempChart.update();

      humidityChart.data.labels = labels;
      humidityChart.data.datasets[0].data = solar.humidity;
      humidityChart.data.datasets[1].data = nonsolar.humidity;
      humidityChart.update();

      airTempChart.data.labels = labels;
      airTempChart.data.datasets[0].data = solar.air_temp;
      airTempChart.data.datasets[1].data = nonsolar.air_temp;
      airTempChart.update();
    })
    .catch(err => console.error("Failed to load comparison data:", err));
  }

  window.onload = () => {
    const currentDate = document.getElementById("datePicker").value;
    loadComparisonData(currentDate);
  };

  document.getElementById("datePicker").addEventListener("change", function () {
    loadComparisonData(this.value);
  });
</script>
</body>
</html>
