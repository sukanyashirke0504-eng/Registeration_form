<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbName = "leave_management";

$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $dbName");
mysqli_select_db($conn, $dbName);
mysqli_set_charset($conn, "utf8mb4");

$sqlUsers = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $sqlUsers);

$sqlRecords = "CREATE TABLE IF NOT EXISTS leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    role VARCHAR(50) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    leave_type VARCHAR(50) DEFAULT NULL,
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    reason TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $sqlRecords);

$columnsToCheck = [
    ['email', 'VARCHAR(100) DEFAULT NULL'],
    ['phone', 'VARCHAR(30) DEFAULT NULL'],
    ['role', 'VARCHAR(50) DEFAULT NULL'],
    ['status', 'VARCHAR(20) DEFAULT "Active"'],
    ['leave_type', 'VARCHAR(50) DEFAULT NULL'],
    ['start_date', 'DATE DEFAULT NULL'],
    ['end_date', 'DATE DEFAULT NULL'],
    ['reason', 'TEXT DEFAULT NULL'],
    ['created_at', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP']
];

foreach ($columnsToCheck as $col) {
    $columnName = $col[0];
    $definition = $col[1];
    $check = mysqli_query($conn, "SHOW COLUMNS FROM leave_requests LIKE '$columnName'");
    if (mysqli_num_rows($check) === 0) {
        mysqli_query($conn, "ALTER TABLE leave_requests ADD COLUMN `$columnName` $definition");
    }
}

?>