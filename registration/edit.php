<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'database.php';

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $status = trim($_POST['status'] ?? 'Active');
    $leave_type = trim($_POST['leave_type'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    $sql = "UPDATE leave_requests SET name=?, email=?, phone=?, role=?, status=?, leave_type=?, start_date=?, end_date=?, reason=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssssssi', $name, $email, $phone, $role, $status, $leave_type, $start_date, $end_date, $reason, $id);
    if (mysqli_stmt_execute($stmt)) {
        header('Location: list.php?status=updated');
        exit;
    }
}

$result = mysqli_query($conn, "SELECT * FROM leave_requests WHERE id=$id");
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="stylee.css">
</head>
<body class="dashboard-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="mb-4"><i class="fa-solid fa-user-pen me-2"></i>Edit Record</h3>
                        <form method="POST" class="row g-3">
                            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($row['phone'] ?? ''); ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Role</label><input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($row['role'] ?? ''); ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select"><option value="Active" <?php echo ($row['status'] ?? '') == 'Active' ? 'selected' : ''; ?>>Active</option><option value="Inactive" <?php echo ($row['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option></select></div>
                            <div class="col-md-6"><label class="form-label">Leave Type</label><select name="leave_type" class="form-select"><option value="">Select</option><option value="Casual Leave" <?php echo ($row['leave_type'] ?? '') == 'Casual Leave' ? 'selected' : ''; ?>>Casual Leave</option><option value="Sick Leave" <?php echo ($row['leave_type'] ?? '') == 'Sick Leave' ? 'selected' : ''; ?>>Sick Leave</option><option value="Earned Leave" <?php echo ($row['leave_type'] ?? '') == 'Earned Leave' ? 'selected' : ''; ?>>Earned Leave</option></select></div>
                            <div class="col-md-6"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($row['start_date'] ?? ''); ?>"></div>
                            <div class="col-md-6"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($row['end_date'] ?? ''); ?>"></div>
                            <div class="col-12"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="3"><?php echo htmlspecialchars($row['reason'] ?? ''); ?></textarea></div>
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Update</button>
                                <a href="list.php" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
