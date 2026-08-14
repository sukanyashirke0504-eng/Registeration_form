<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$role = trim($_POST['role'] ?? '');
$status = trim($_POST['status'] ?? 'Active');
$leave_type = trim($_POST['leave_type'] ?? '');
$start_date = trim($_POST['start_date'] ?? '');
$end_date = trim($_POST['end_date'] ?? '');
$reason = trim($_POST['reason'] ?? '');

if ($name === '' || $email === '' || $phone === '' || $role === '') {
    header('Location: indexx.html?error=Please fill all required fields');
    exit;
}

$sql = "INSERT INTO leave_requests (name, email, phone, role, status, leave_type, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'sssssssss', $name, $email, $phone, $role, $status, $leave_type, $start_date, $end_date, $reason);

if (mysqli_stmt_execute($stmt)) {
    header('Location: list.php?status=created');
    exit;
}

header('Location: indexx.html?error=Unable to save record');
exit;
