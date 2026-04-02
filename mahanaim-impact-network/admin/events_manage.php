<?php
// === admin/events_manage.php ===
session_start();
require_once '../config/db.php';

// Check if logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$errors = [];

// Handle Add/Edit Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    $id = $_POST['event_id'] ?? null;
    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $event_date = $_POST['event_date'];
    $early_bird_deadline = $_POST['early_bird_deadline'];
    $early_bird_price = $_POST['early_bird_price'];
    $regular_price = $_POST['regular_price'];
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    
    if (empty($title) || empty($location) || empty($event_date)) {
        $errors[] = "Title, location, and event date are required";
    }
    
    if (empty($errors)) {
        try {
            if ($id) {
                // Update existing event
                $stmt = $pdo->prepare("UPDATE events SET title=?, location=?, event_date=?, early_bird_deadline=?, early_bird_price=?, regular_price=?, description=?, status=? WHERE id=?");
                $stmt->execute([$title, $location, $event_date, $early_bird_deadline, $early_bird_price, $regular_price, $description, $status, $id]);
                $success = "Event updated successfully!";
            } else {
                // Add new event
                $stmt = $pdo->prepare("INSERT INTO events (title, location, event_date, early_bird_deadline, early_bird_price, regular_price, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $location, $event_date, $early_bird_deadline, $early_bird_price, $regular_price, $description, $status]);
                $success = "Event added successfully!";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Handle Delete Event
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Event deleted successfully!";
    } catch (PDOException $e) {
        $errors[] = "Cannot delete event: " . $e->getMessage();
    }
}

// Fetch all events
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll();

// Get event for editing
$editEvent = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editEvent = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - LIN Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%);
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
            padding: 0;
        }
        .sidebar-header {
            padding: 2rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-menu a {
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            display: block;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(212, 175, 55, 0.2);
            border-left: 4px solid #D4AF37;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        .card-custom {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-hands-helping fa-3x mb-2" style="color: #D4AF37;"></i>
            <h5 class="mb-0">LIN Admin</h5>
            <small>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="events_manage.php" class="active"><i class="fas fa-calendar me-2"></i>Manage Events</a>
            <a href="gallery_manage.php"><i class="fas fa-images me-2"></i>Manage Gallery</a>
            <a href="../index.php" target="_blank"><i class="fas fa-globe me-2"></i>View Website</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: #4B0082;"><i class="fas fa-calendar-alt me-2"></i>Manage Events</h2>
            <a href="dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Event Form -->
        <div class="card card-custom mb-4">
            <div class="card-body p-4">
                <h4 class="mb-4" style="color: #4B0082;">
                    <?php echo $editEvent ? 'Edit Event' : 'Add New Event'; ?>
                </h4>
                <form method="POST" action="">
                    <input type="hidden" name="event_id" value="<?php echo $editEvent['id'] ?? ''; ?>">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Event Title *</label>
                            <input type="text" name="title" class="form-control" required 
                                   value="<?php echo htmlspecialchars($editEvent['title'] ?? ''); ?>"
                                   placeholder="e.g., 21 Times Better Webinar">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Status *</label>
                            <select name="status" class="form-select" required>
                                <option value="active" <?php echo ($editEvent['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($editEvent['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Location *</label>
                            <input type="text" name="location" class="form-control" required 
                                   value="<?php echo htmlspecialchars($editEvent['location'] ?? ''); ?>"
                                   placeholder="e.g., Online Webinar or Lagos Convention Center">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Event Date *</label>
                            <input type="date" name="event_date" class="form-control" required 
                                   value="<?php echo $editEvent['event_date'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Early Bird Deadline</label>
                            <input type="date" name="early_bird_deadline" class="form-control" 
                                   value="<?php echo $editEvent['early_bird_deadline'] ?? ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Early Bird Price (₦)</label>
                            <input type="number" name="early_bird_price" class="form-control" step="0.01" 
                                   value="<?php echo $editEvent['early_bird_price'] ?? '0'; ?>"
                                   placeholder="0 for free events">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Regular Price (₦)</label>
                            <input type="number" name="regular_price" class="form-control" step="0.01" 
                                   value="<?php echo $editEvent['regular_price'] ?? '0'; ?>"
                                   placeholder="0 for free events">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($editEvent['description'] ?? ''); ?></textarea>
                        <small class="text-muted">Include speakers, topics, time, and other details</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>For free events:</strong> Set both prices to 0. Registrations will be recorded without payment.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="save_event" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo $editEvent ? 'Update Event' : 'Add Event'; ?>
                        </button>
                        <?php if ($editEvent): ?>
                            <a href="events_manage.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events List -->
        <div class="card card-custom">
            <div class="card-body p-4">
                <h4 class="mb-4" style="color: #4B0082;">All Events</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Registrations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <?php
                                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM registrations WHERE event_id = ?");
                                $stmt->execute([$event['id']]);
                                $regCount = $stmt->fetch()['count'];
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($event['title']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($event['location']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                    <td>
                                        <?php if ($event['regular_price'] == 0): ?>
                                            <span class="badge bg-success">FREE</span>
                                        <?php else: ?>
                                            ₦<?php echo number_format($event['regular_price'], 0); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($event['status'] == 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-primary"><?php echo $regCount; ?></span></td>
                                    <td>
                                        <a href="?edit=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Are you sure you want to delete this event?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
