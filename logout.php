<?php
session_start();
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session.
session_destroy();

// Remove remember me cookie if set
if (isset($_COOKIE['remember_username'])) {
    setcookie('remember_username', '', time() - 3600, "/");
}

// Redirect to login page
header("Location: login.php");
exit(); 