<?php
// === admin/settings.php ===
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$errors = [];

$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!password_verify($current_password, $admin['password'])) {
        $errors[] = "Current password is incorrect";
    }
    if (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters";
    }
    if ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match";
    }
    if (empty($errors)) {
        try {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $_SESSION['admin_id']]);
            $success = "Password changed successfully!";
        } catch (PDOException $e) {
            $errors[] = "Error updating password: " . $e->getMessage();
        }
    }
}

// Handle username change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_username'])) {
    $new_username    = trim($_POST['new_username']);
    $password_confirm = $_POST['password_confirm'];

    if (!password_verify($password_confirm, $admin['password'])) {
        $errors[] = "Password is incorrect";
    }
    if (empty($new_username)) {
        $errors[] = "Username cannot be empty";
    }
    if (strlen($new_username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    }
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
    $stmt->execute([$new_username, $_SESSION['admin_id']]);
    if ($stmt->fetch()) {
        $errors[] = "Username already taken";
    }
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?");
            $stmt->execute([$new_username, $_SESSION['admin_id']]);
            $_SESSION['admin_username'] = $new_username;
            $success = "Username changed successfully!";
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();
        } catch (PDOException $e) {
            $errors[] = "Error updating username: " . $e->getMessage();
        }
    }
}

// Handle email change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_email'])) {
    $new_email             = trim($_POST['new_email']);
    $password_confirm_email = $_POST['password_confirm_email'];

    if (!password_verify($password_confirm_email, $admin['password'])) {
        $errors[] = "Password is incorrect";
    }
    if (empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE admins SET email = ? WHERE id = ?");
            $stmt->execute([$new_email, $_SESSION['admin_id']]);
            $success = "Email changed successfully!";
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();
        } catch (PDOException $e) {
            $errors[] = "Error updating email: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings — Mahanaim Impact Network</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
  --royal: #4B0082;
  --royal-light: #6A0DAD;
  --gold: #D4AF37;
  --gold-light: #F0D060;
  --ink: #1a1a2e;
  --muted: #6c757d;
  --surface: #f8f7ff;
  --white: #ffffff;
  --success: #2ecc71;
  --danger: #e74c3c;
  --warning: #f39c12;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'DM Sans', sans-serif;
  background: var(--surface);
  color: var(--ink);
  min-height: 100vh;
}

/* ── SIDEBAR ── */
.sidebar {
  position: fixed;
  top: 0; left: 0;
  width: 260px;
  height: 100vh;
  background: linear-gradient(160deg, #2d0060 0%, #4B0082 60%, #6A0DAD 100%);
  display: flex;
  flex-direction: column;
  z-index: 100;
  box-shadow: 4px 0 30px rgba(75,0,130,.25);
  overflow-y: auto;
}

.sidebar-brand {
  padding: 2rem 1.5rem 1.5rem;
  border-bottom: 1px solid rgba(255,255,255,.08);
}

.sidebar-brand .logo-ring {
  width: 56px; height: 56px;
  border-radius: 16px;
  background: rgba(212,175,55,.15);
  border: 1.5px solid rgba(212,175,55,.35);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: .75rem;
}

.sidebar-brand h4 {
  font-family: 'Sora', sans-serif;
  font-size: .9rem;
  font-weight: 700;
  color: #fff;
  margin: 0;
  line-height: 1.3;
}

.sidebar-brand small {
  color: rgba(255,255,255,.45);
  font-size: .72rem;
}

.nav-section {
  padding: .5rem 1rem;
  font-size: .65rem;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: rgba(255,255,255,.3);
  margin-top: 1rem;
}

.sidebar-nav { list-style: none; padding: 0 .75rem; flex: 1; }
.sidebar-nav li { margin-bottom: .2rem; }

.sidebar-nav a {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .75rem 1rem;
  color: rgba(255,255,255,.65);
  text-decoration: none;
  border-radius: 10px;
  font-size: .875rem;
  font-weight: 500;
  transition: all .2s;
}

.sidebar-nav a:hover,
.sidebar-nav a.active {
  background: rgba(212,175,55,.15);
  color: #fff;
  border-left: 3px solid var(--gold);
}

.sidebar-nav a .nav-icon {
  width: 32px; height: 32px;
  border-radius: 8px;
  background: rgba(255,255,255,.07);
  display: flex; align-items: center; justify-content: center;
  font-size: .8rem;
  flex-shrink: 0;
}

.sidebar-nav a.active .nav-icon {
  background: rgba(212,175,55,.25);
  color: var(--gold);
}

.sidebar-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid rgba(255,255,255,.08);
}

.sidebar-footer a {
  color: rgba(255,255,255,.5);
  text-decoration: none;
  font-size: .8rem;
  display: flex; align-items: center; gap: .5rem;
}

.sidebar-footer a:hover { color: #fff; }

/* ── MAIN ── */
.main-wrap {
  margin-left: 260px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.topbar {
  background: var(--white);
  border-bottom: 1px solid rgba(0,0,0,.06);
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 50;
}

.topbar-title {
  font-family: 'Sora', sans-serif;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--royal);
}

.topbar-actions { display: flex; gap: .75rem; align-items: center; }

.admin-chip {
  display: flex; align-items: center; gap: .5rem;
  background: var(--surface);
  border: 1px solid rgba(75,0,130,.12);
  border-radius: 50px;
  padding: .35rem .85rem;
  font-size: .8rem;
  font-weight: 600;
  color: var(--royal);
}

.admin-chip .dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--success);
}

.page-body { padding: 2rem; flex: 1; }

/* ── ALERT BANNERS ── */
.alert-banner {
  padding: .875rem 1.25rem;
  border-radius: 12px;
  font-size: .875rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: .6rem;
}

.alert-success { background: rgba(46,204,113,.1); color: #1a7a44; border: 1px solid rgba(46,204,113,.2); }
.alert-danger  { background: rgba(231,76,60,.08); color: #a91c0c; border: 1px solid rgba(231,76,60,.15); }
.alert-warning { background: rgba(243,156,18,.1); color: #7d5200; border: 1px solid rgba(243,156,18,.2); }

/* ── SECTION CARD ── */
.section-card {
  background: var(--white);
  border-radius: 16px;
  border: 1px solid rgba(0,0,0,.05);
  box-shadow: 0 2px 12px rgba(0,0,0,.04);
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.section-card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(0,0,0,.05);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.section-card-header h5 {
  font-family: 'Sora', sans-serif;
  font-weight: 700;
  font-size: 1rem;
  color: var(--royal);
  margin: 0;
  display: flex;
  align-items: center;
  gap: .5rem;
}

.section-card-body { padding: 1.5rem; }

/* ── ACCOUNT INFO ── */
.info-pill {
  background: var(--surface);
  border: 1px solid rgba(75,0,130,.08);
  border-radius: 12px;
  padding: 1rem 1.25rem;
}

.info-pill .label {
  font-size: .7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: var(--muted);
  margin-bottom: .3rem;
}

.info-pill .value {
  font-family: 'Sora', sans-serif;
  font-size: .95rem;
  font-weight: 700;
  color: var(--royal);
}

/* ── SETTINGS PANELS ── */
.settings-panel {
  background: var(--white);
  border-radius: 16px;
  border: 1px solid rgba(0,0,0,.05);
  box-shadow: 0 2px 12px rgba(0,0,0,.04);
  overflow: hidden;
  height: 100%;
  transition: box-shadow .2s, transform .2s;
}

.settings-panel:hover {
  box-shadow: 0 6px 24px rgba(75,0,130,.09);
  transform: translateY(-2px);
}

.panel-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(0,0,0,.05);
  display: flex;
  align-items: center;
  gap: .9rem;
}

.panel-icon {
  width: 44px; height: 44px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.05rem;
  flex-shrink: 0;
}

.panel-icon.purple { background: rgba(75,0,130,.1); color: var(--royal); }
.panel-icon.gold   { background: rgba(212,175,55,.12); color: #b8960e; }
.panel-icon.blue   { background: rgba(33,150,243,.1); color: #1565c0; }
.panel-icon.green  { background: rgba(46,204,113,.1); color: #1a9c54; }

.panel-header-text h6 {
  font-family: 'Sora', sans-serif;
  font-size: .95rem;
  font-weight: 700;
  color: var(--royal);
  margin: 0 0 .1rem;
}

.panel-header-text small {
  font-size: .75rem;
  color: var(--muted);
}

.panel-body { padding: 1.5rem; }

/* ── FORM ── */
.form-label {
  font-size: .78rem;
  font-weight: 600;
  color: #444;
  margin-bottom: .35rem;
  display: block;
}

.form-control, .form-select {
  border: 1.5px solid rgba(0,0,0,.1);
  border-radius: 10px;
  padding: .6rem .9rem;
  font-size: .875rem;
  width: 100%;
  transition: border-color .2s, box-shadow .2s;
  font-family: 'DM Sans', sans-serif;
  background: #fff;
}

.form-control:focus, .form-select:focus {
  border-color: var(--royal);
  box-shadow: 0 0 0 3px rgba(75,0,130,.08);
  outline: none;
}

.form-control:disabled {
  background: var(--surface);
  color: var(--muted);
  cursor: not-allowed;
}

.form-hint {
  font-size: .72rem;
  color: var(--muted);
  margin-top: .3rem;
}

.mb-form { margin-bottom: 1rem; }

/* ── BUTTONS ── */
.btn-royal {
  background: var(--royal);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: .65rem 1.4rem;
  font-size: .875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  width: 100%;
  justify-content: center;
  font-family: 'DM Sans', sans-serif;
}

.btn-royal:hover { background: var(--royal-light); color: #fff; }

.btn-gold {
  background: var(--gold);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: .65rem 1.4rem;
  font-size: .875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  width: 100%;
  justify-content: center;
  font-family: 'DM Sans', sans-serif;
}

.btn-gold:hover { background: #c49b25; color: #fff; }

.btn-blue {
  background: #1976d2;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: .65rem 1.4rem;
  font-size: .875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  width: 100%;
  justify-content: center;
  font-family: 'DM Sans', sans-serif;
}

.btn-blue:hover { background: #1565c0; color: #fff; }

.btn-ghost {
  background: transparent;
  border: 1.5px solid rgba(75,0,130,.2);
  color: var(--royal);
  border-radius: 10px;
  padding: .65rem 1.4rem;
  font-size: .875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  font-family: 'DM Sans', sans-serif;
}

.btn-ghost:hover { background: rgba(75,0,130,.06); color: var(--royal); border-color: var(--royal); }

/* ── SECURITY TIPS ── */
.tip-item {
  display: flex;
  align-items: center;
  gap: .6rem;
  padding: .5rem 0;
  font-size: .85rem;
  color: #444;
  border-bottom: 1px solid rgba(0,0,0,.04);
}

.tip-item:last-child { border-bottom: none; }

.tip-dot {
  width: 22px; height: 22px;
  border-radius: 50%;
  background: rgba(46,204,113,.12);
  color: var(--success);
  display: flex; align-items: center; justify-content: center;
  font-size: .65rem;
  flex-shrink: 0;
}

.last-login-strip {
  margin-top: 1rem;
  padding: .75rem 1rem;
  background: var(--surface);
  border-radius: 10px;
  font-size: .78rem;
  color: var(--muted);
  display: flex;
  align-items: center;
  gap: .5rem;
}

/* ── DANGER ZONE ── */
.danger-zone {
  background: var(--white);
  border-radius: 16px;
  border: 2px solid rgba(231,76,60,.2);
  padding: 1.5rem;
  margin-top: 1.5rem;
}

.danger-zone h6 {
  font-family: 'Sora', sans-serif;
  font-weight: 700;
  color: var(--danger);
  margin-bottom: .75rem;
  display: flex;
  align-items: center;
  gap: .5rem;
}

.danger-info {
  background: rgba(243,156,18,.08);
  border: 1px solid rgba(243,156,18,.2);
  border-radius: 10px;
  padding: .875rem 1rem;
  font-size: .83rem;
  color: #7d5200;
  display: flex;
  align-items: flex-start;
  gap: .6rem;
  margin-top: .75rem;
}

/* ── RESPONSIVE ── */
@media (max-width: 991px) {
  .sidebar { width: 220px; }
  .main-wrap { margin-left: 220px; }
}

@media (max-width: 767px) {
  .sidebar { display: none; }
  .main-wrap { margin-left: 0; }
  .page-body { padding: 1rem; }
}
</style>
</head>
<body>

<!-- ══════════════════════════════════════ SIDEBAR ══════════════════════════════════════ -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo-ring">
      <i class="fas fa-hands-helping" style="color: var(--gold); font-size:1.4rem;"></i>
    </div>
    <h4>Mahanaim Admin</h4>
    <small>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></small>
  </div>

  <div class="nav-section">Main</div>
  <ul class="sidebar-nav">
    <li>
      <a href="dashboard.php?tab=overview">
        <span class="nav-icon"><i class="fas fa-th-large"></i></span>Overview
      </a>
    </li>
    <li>
      <a href="dashboard.php?tab=events">
        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>Events
      </a>
    </li>
    <li>
      <a href="dashboard.php?tab=registrations">
        <span class="nav-icon"><i class="fas fa-users"></i></span>Registrations
      </a>
    </li>
    <li>
      <a href="dashboard.php?tab=payments">
        <span class="nav-icon"><i class="fas fa-credit-card"></i></span>Payments
      </a>
    </li>
    <li>
      <a href="dashboard.php?tab=create_event">
        <span class="nav-icon"><i class="fas fa-plus-circle"></i></span>Add Event
      </a>
    </li>
  </ul>

  <div class="nav-section">Links</div>
  <ul class="sidebar-nav">
    <li>
      <a href="../index.php" target="_blank">
        <span class="nav-icon"><i class="fas fa-globe"></i></span>View Website
      </a>
    </li>
    <li>
      <a href="dashboard.php?export_all=1">
        <span class="nav-icon"><i class="fas fa-file-csv"></i></span>Export All CSV
      </a>
    </li>
  </ul>

  <div class="sidebar-footer">
    <a href="settings.php" style="color:rgba(255,255,255,.85);"><i class="fas fa-gear"></i> Settings</a>
  </div>

  <div class="sidebar-footer">
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>

<!-- ══════════════════════════════════════ MAIN ══════════════════════════════════════ -->
<div class="main-wrap">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-title">Account Settings</div>
    <div class="topbar-actions">
      <a href="dashboard.php" class="btn-ghost"><i class="fas fa-arrow-left"></i> Dashboard</a>
      <div class="admin-chip">
        <div class="dot"></div>
        <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
      </div>
    </div>
  </div>

  <div class="page-body">

    <?php if ($success): ?>
    <div class="alert-banner alert-success">
      <i class="fas fa-check-circle"></i>
      <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="alert-banner alert-danger">
      <i class="fas fa-times-circle"></i>
      <div>
        <?php foreach ($errors as $error): ?>
          <div><?php echo htmlspecialchars($error); ?></div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- ── ACCOUNT INFO ── -->
    <div class="section-card" style="margin-bottom:1.75rem;">
      <div class="section-card-header">
        <h5><i class="fas fa-user-circle"></i> Current Account Information</h5>
        <span style="font-size:.75rem;color:var(--muted);">Member since <?php echo date('M Y', strtotime($admin['created_at'])); ?></span>
      </div>
      <div class="section-card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="info-pill">
              <div class="label">Username</div>
              <div class="value"><?php echo htmlspecialchars($admin['username']); ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-pill">
              <div class="label">Email Address</div>
              <div class="value" style="font-size:.85rem;"><?php echo htmlspecialchars($admin['email']); ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-pill">
              <div class="label">Account Created</div>
              <div class="value"><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── SETTINGS PANELS ── -->
    <div class="row g-4">

      <!-- Change Password -->
      <div class="col-lg-6">
        <div class="settings-panel">
          <div class="panel-header">
            <div class="panel-icon purple"><i class="fas fa-lock"></i></div>
            <div class="panel-header-text">
              <h6>Change Password</h6>
              <small>Update your account password</small>
            </div>
          </div>
          <div class="panel-body">
            <form method="POST" action="">
              <div class="mb-form">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
              </div>
              <div class="mb-form">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Min. 6 characters">
                <div class="form-hint">Use at least 8 characters with letters, numbers & symbols</div>
              </div>
              <div class="mb-form">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="Repeat new password">
              </div>
              <button type="submit" name="change_password" class="btn-royal">
                <i class="fas fa-save"></i> Update Password
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Change Username -->
      <div class="col-lg-6">
        <div class="settings-panel">
          <div class="panel-header">
            <div class="panel-icon gold"><i class="fas fa-user-edit"></i></div>
            <div class="panel-header-text">
              <h6>Change Username</h6>
              <small>Update your login username</small>
            </div>
          </div>
          <div class="panel-body">
            <form method="POST" action="">
              <div class="mb-form">
                <label class="form-label">Current Username</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" disabled>
              </div>
              <div class="mb-form">
                <label class="form-label">New Username</label>
                <input type="text" name="new_username" class="form-control" required minlength="3" placeholder="At least 3 characters">
              </div>
              <div class="mb-form">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirm" class="form-control" required placeholder="Enter current password to confirm">
              </div>
              <button type="submit" name="change_username" class="btn-gold">
                <i class="fas fa-save"></i> Update Username
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Change Email -->
      <div class="col-lg-6">
        <div class="settings-panel">
          <div class="panel-header">
            <div class="panel-icon blue"><i class="fas fa-envelope"></i></div>
            <div class="panel-header-text">
              <h6>Change Email</h6>
              <small>Update your email address</small>
            </div>
          </div>
          <div class="panel-body">
            <form method="POST" action="">
              <div class="mb-form">
                <label class="form-label">Current Email</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" disabled>
              </div>
              <div class="mb-form">
                <label class="form-label">New Email</label>
                <input type="email" name="new_email" class="form-control" required
                       value="info@mahanaimimpact.com"
                       placeholder="e.g. info@mahanaimimpact.com">
                <div class="form-hint">Pre-filled with the new organisation email</div>
              </div>
              <div class="mb-form">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirm_email" class="form-control" required placeholder="Enter current password to confirm">
              </div>
              <button type="submit" name="change_email" class="btn-blue">
                <i class="fas fa-save"></i> Update Email
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>

 </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss flash messages after 5 s
setTimeout(() => {
  document.querySelectorAll('.alert-banner').forEach(el => {
    el.style.transition = 'opacity .4s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 420);
  });
}, 5000);
</script>
</body>
</html>
