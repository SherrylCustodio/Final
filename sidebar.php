<?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_home_page = in_array($current_page, ['dashboard.php', 'nonsolar_dashboard.php']);
?>

<style>
.sidebar {
  width: 120px;
  background-color: #004aad;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 20px;
  min-height: 100vh;
}

.sidebar a {
  width: 90%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin: 15px 0;
  padding: 15px 10px;
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  border-radius: 12px;
  transition: background 0.2s, color 0.2s;
}

.sidebar a:hover {
  background-color: #00337a;
  color: #ffd700;
}

.sidebar a.active {
  background-color: #00337a;
  color: #ffd700;
}

.sidebar img {
  width: 30px;
  height: 30px;
  filter: brightness(0) invert(1);
  margin-bottom: 5px;
}

.sidebar span {
  font-size: 12px;
  margin-top: 2px;
}
</style>

<div class="sidebar">
  <a href="dashboard.php" class="<?= $is_home_page ? 'active' : '' ?>">
    <img src="https://img.icons8.com/ios-filled/50/ffffff/home.png" alt="Home">
    <span>Home</span>
  </a>
  <a href="comparison.php" class="<?= $current_page == 'comparison.php' ? 'active' : '' ?>">
    <img src="https://img.icons8.com/ios-filled/50/ffffff/combo-chart.png" alt="Comparison">
    <span>Comparison</span>
  </a>
  <a href="sensor_report.php" class="<?= $current_page == 'sensor_report.php' ? 'active' : '' ?>">
    <img src="https://img.icons8.com/ios-filled/50/ffffff/file.png" alt="Sensor Report">
    <span>Report</span>
  </a>
  <a href="sensor_status.php" class="<?= $current_page == 'sensor_status.php' ? 'active' : '' ?>">
    <img src="https://img.icons8.com/ios-filled/50/ffffff/sensor.png" alt="Sensor Status">
    <span>Status</span>
  </a>
  <a href="#" id="logout-link" style="background:#e74c3c; color:#fff;" title="Logout">
    <img src="https://img.icons8.com/ios-filled/50/ffffff/logout-rounded-left.png" alt="Logout">
    <span>Logout</span>
  </a>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
      logoutLink.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Are you sure?',
          text: 'You will be logged out of your session.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#e74c3c',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, logout',
          cancelButtonText: 'Cancel',
          background: '#fff',
          color: '#222',
          width: 300,
          customClass: {
            popup: 'swal2-minimalist'
          },
          reverseButtons: true 
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'logout.php';
          }
        });
      });
    }
  });
</script>
