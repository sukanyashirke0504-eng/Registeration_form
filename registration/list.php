<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'database.php';

$search = trim($_GET['search'] ?? '');
$where = '';
$params = [];

if ($search !== '') {
    $where = "WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR role LIKE ? OR status LIKE ?";
    $like = "%$search%";
    $params = [$like, $like, $like, $like, $like];
}

$sql = "SELECT * FROM leave_requests $where ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt && $search !== '') {
    mysqli_stmt_bind_param($stmt, 'sssss', $params[0], $params[1], $params[2], $params[3], $params[4]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
}

$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM leave_requests"))['total'];
$activeUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM leave_requests WHERE status = 'Active'"))['total'];
$inactiveUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM leave_requests WHERE status = 'Inactive'"))['total'];
$statusMessage = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="stylee.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="dashboard-page">
    <nav class="navbar navbar-expand-lg dashboard-header">
        <div class="container-fluid px-4 py-2">
            <a class="navbar-brand fw-bold" href="#"><i class="fa-solid fa-users-gear me-2"></i>Admin Dashboard</a>
            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text me-2">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</a>
            </div>
        </div>
    </nav>

    <div class="container dashboard-shell">
        <?php if ($statusMessage === 'created') { ?><div class="alert alert-success">Record added successfully.</div><?php } ?>
        <?php if ($statusMessage === 'updated') { ?><div class="alert alert-info">Record updated successfully.</div><?php } ?>
        <?php if ($statusMessage === 'deleted') { ?><div class="alert alert-warning">Record deleted successfully.</div><?php } ?>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="dashboard-stat-card card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Users</h6>
                                <h3 class="mb-0"><?php echo $totalUsers; ?></h3>
                            </div>
                            <div class="stat-icon text-primary"><i class="fa-solid fa-users fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-stat-card card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Active</h6>
                                <h3 class="mb-0"><?php echo $activeUsers; ?></h3>
                            </div>
                            <div class="stat-icon text-success"><i class="fa-solid fa-circle-check fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-stat-card card border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Inactive</h6>
                                <h3 class="mb-0"><?php echo $inactiveUsers; ?></h3>
                            </div>
                            <div class="stat-icon text-danger"><i class="fa-solid fa-circle-xmark fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-panel border-0">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                    <h4 class="mb-0"><i class="fa-solid fa-table me-2"></i>Records</h4>
                    <div class="d-flex gap-2 flex-wrap">
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control search-input" placeholder="Search by name, email, role..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                        <a href="indexx.html" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add New</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table data-table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) { while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td><span class="badge bg-<?php echo ($row['status'] == 'Active') ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-warning me-1"><i class="fa-solid fa-pen"></i></a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger delete-btn"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php } } else { ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">No records found.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-btn').forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: 'Delete this record?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</body>
</html>
