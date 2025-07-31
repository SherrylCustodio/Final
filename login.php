<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>

  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body, html {
      margin: 0;
      height: 100%;
      font-family: Arial, sans-serif;
    }

    .login-container {
      background-image: url('/solar/images/bg.jpg.png');
      background-size: cover;
      background-position: center;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background-color: rgba(255, 255, 255, 0.85);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      text-align: center;
      width: 300px;
      box-sizing: border-box;
    }

    .login-box h2 {
      margin-bottom: 25px;
      color: #333;
    }

    .input-group {
      position: relative;
      margin: 10px 0;
    }

    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 100%;
      padding: 10px;
      border-radius: 20px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
      font-size: 0.9em;
      user-select: none;
    }

    .remember {
      text-align: left;
      margin: 10px 0;
      font-size: 0.9em;
    }

    .login-btn {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      background-color: #8bc34a;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 20px;
      cursor: pointer;
    }

    .login-btn:hover {
      background-color: #7cb342;
    }

    .signup-link {
      margin-top: 20px;
      font-size: 0.9em;
      color: #333;
    }

    .signup-link a {
      color: #8bc34a;
      text-decoration: none;
      font-weight: bold;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }
    /* SweetAlert2 minimalist small modal */
    .swal2-popup.swal2-minimalist {
      width: 270px !important;
      padding: 1.2em 1.2em 1em 1.2em !important;
      font-size: 0.95em;
      border-radius: 12px;
    }
    .swal2-title {
      font-size: 1.1em !important;
      margin-bottom: 0.5em !important;
    }
    .swal2-html-container, .swal2-content {
      font-size: 0.95em !important;
    }
  </style>
</head>
<body>

<?php
$success_message = '';
$show_login_success = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
  $success_message = 'Login successfully';
  $show_login_success = true;
}
if (isset($_SESSION['register_success']) && $_SESSION['register_success'] === true) {
  $success_message = 'Register Successfully';
  unset($_SESSION['register_success']);
}
$error_message = '';
if (isset($_SESSION['message'])) {
  $error_message = $_SESSION['message'];
  unset($_SESSION['message']);
}
?>

<div class="login-container">
  <div class="login-box">
    <h2>Login</h2>
    <form method="POST" action="../login_process.php">
      <input type="text" name="username" placeholder="Username" required value="<?= isset($_COOKIE['remember_username']) ? htmlspecialchars($_COOKIE['remember_username']) : '' ?>"><br>

      <div class="input-group">
        <input type="password" id="password" name="password" placeholder="Password" required>
        
      </div>

      <div class="remember">
        <input type="checkbox" id="remember" name="remember" <?= isset($_COOKIE['remember_username']) ? 'checked' : '' ?>>
        <label for="remember">Remember me</label>
      </div>

      <button type="submit" class="login-btn">Login</button>

      <div class="signup-link">
        Don't have an account? <a href="../user/signup.php">Sign up</a>
      </div>
    </form>
  </div>
</div>

<script>
  // SweetAlert2 for login success and error
  <?php if ($success_message): ?>
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: '<?= addslashes($success_message) ?>',
      showConfirmButton: false,
      timer: 1200,
      timerProgressBar: true,
      background: '#fff',
      color: '#222',
      width: 270,
      customClass: {
        popup: 'swal2-minimalist'
      }
    });
    <?php if ($show_login_success): ?>
      setTimeout(function() { window.location.href = 'dashboard.php'; }, 1200);
      <?php unset($_SESSION['login_success']); ?>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($error_message): ?>
    Swal.fire({
      icon: 'error',
      title: 'Login Failed',
      text: '<?= addslashes($error_message) ?>',
      showConfirmButton: false,
      timer: 1500,
      timerProgressBar: true,
      background: '#fff',
      color: '#222',
      width: 270,
      customClass: {
        popup: 'swal2-minimalist'
      }
    });
  <?php endif; ?>

  // Password toggle (if you want to add it back, add an element with id="togglePassword")
  // const passwordInput = document.getElementById('password');
  // const togglePassword = document.getElementById('togglePassword');
  // if (togglePassword) {
  //   togglePassword.addEventListener('click', function () {
  //     const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  //     passwordInput.setAttribute('type', type);
  //     this.textContent = type === 'password' ? 'Show' : 'Hide';
  //   });
  // }
</script>

</body>
</html>
