<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'database.php';

$id = (int)($_GET['id'] ?? 0);
mysqli_query($conn, "DELETE FROM leave_requests WHERE id=$id");
header('Location: list.php?status=deleted');
exit;
