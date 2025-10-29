<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: views/auth/login.php');
    exit();
}

// Redirect dựa trên role
switch ($_SESSION['role']) {
    case 'admin':
        header('Location: views/admin/dashboard.php');
        break;
    case 'staff':
        header('Location: views/staff/dashboard.php');
        break;
    case 'student':
        header('Location: views/student/dashboard.php');
        break;
    default:
        header('Location: views/auth/login.php');
        break;
}
exit();
?>