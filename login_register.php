<?php
session_start();
require_once 'config/db.php';

/* ================= REGISTER ================= */
if (isset($_POST['register'])) {

    $fullname = $_POST['name']; // from form
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // admin / employee

    // Check if email already exists
    $checkEmail = $conn->query("SELECT email FROM employees WHERE email = '$email'");
    
    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query(
            "INSERT INTO employees (fullname, email, password, role) 
             VALUES ('$fullname', '$email', '$password', '$role')"
        );
    }

    header("Location: index.php");
    exit();
}

/* ================= LOGIN ================= */
if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM employees WHERE email = '$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: employee/dashboard.php");
            }
            exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}
?>
