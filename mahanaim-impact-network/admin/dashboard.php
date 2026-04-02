<?php
// === admin/dashboard.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$messageType = '';

// ──────────────────────────────────────────────
// HANDLE ACTIONS
// ──────────────────────────────────────────────

// CREATE EVENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'create_event') {
        $title        = trim($_POST['title']);
        $description  = trim($_POST['description']);
        $location     = trim($_POST['location']);
        $event_date   = $_POST['event_date'];
        $early_bird_deadline = $_POST['early_bird_deadline'] ?: null;
        $early_bird_price    = floatval($_POST['early_bird_price'] ?: 0);
        $regular_price       = floatval($_POST['regular_price']);
        $status       = $_POST['status'];

        try {
            $stmt = $pdo->prepare("INSERT INTO events (title, description, location, event_date, early_bird_deadline, early_bird_price, regular_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $location, $event_date, $early_bird_deadline, $early_bird_price, $regular_price, $status]);
            $message = "Event created successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error creating event: " . $e->getMessage();
            $messageType = "danger";
        }
    }

    if ($_POST['action'] === 'update_event') {
        $id           = intval($_POST['event_id']);
        $title        = trim($_POST['title']);
        $description  = trim($_POST['description']);
        $location     = trim($_POST['location']);
        $event_date   = $_POST['event_date'];
        $early_bird_deadline = $_POST['early_bird_deadline'] ?: null;
        $early_bird_price    = floatval($_POST['early_bird_price'] ?: 0);
        $regular_price       = floatval($_POST['regular_price']);
        $status       = $_POST['status'];

        try {
            $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, location=?, event_date=?, early_bird_deadline=?, early_bird_price=?, regular_price=?, status=? WHERE id=?");
            $stmt->execute([$title, $description, $location, $event_date, $early_bird_deadline, $early_bird_price, $regular_price, $status, $id]);
            $message = "Event updated successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error updating event: " . $e->getMessage();
            $messageType = "danger";
        }
    }

    if ($_POST['action'] === 'delete_event') {
        $id = intval($_POST['event_id']);
        try {
            $pdo->prepare("DELETE FROM registrations WHERE event_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$id]);
            $message = "Event and its registrations deleted.";
            $messageType = "warning";
        } catch (PDOException $e) {
            $message = "Error deleting event: " . $e->getMessage();
            $messageType = "danger";
        }
    }

    if ($_POST['action'] === 'update_payment') {
        $reg_id = intval($_POST['registration_id']);
        $status = $_POST['payment_status'];
        try {
            $pdo->prepare("UPDATE registrations SET payment_status=? WHERE id=?")->execute([$status, $reg_id]);
            $message = "Payment status updated.";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }

    if ($_POST['action'] === 'delete_registration') {
        $reg_id = intval($_POST['registration_id']);
        try {
            $pdo->prepare("DELETE FROM registrations WHERE id=?")->execute([$reg_id]);
            $message = "Registration deleted.";
            $messageType = "warning";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// ──────────────────────────────────────────────
// CSV EXPORT
// ──────────────────────────────────────────────
if (isset($_GET['export']) && isset($_GET['event_id'])) {
    $eventId = intval($_GET['event_id']);
    $stmt = $pdo->prepare("SELECT r.*, e.title as event_title, e.event_date, e.location FROM registrations r JOIN events e ON r.event_id = e.id WHERE r.event_id = ?");
    $stmt->execute([$eventId]);
    $data = $stmt->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="registrations_event_' . $eventId . '_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID','Event','Full Name','Email','Phone','Business Name','Ticket Type','Amount Paid','Payment Status','Transaction Ref','Registration Date']);
    foreach ($data as $row) {
        fputcsv($output, [$row['id'],$row['event_title'],$row['full_name'],$row['email'],$row['phone'],$row['business_name'],$row['ticket_type'],$row['amount_paid'],$row['payment_status'],$row['transaction_ref'],$row['created_at']]);
    }
    fclose($output);
    exit();
}

if (isset($_GET['export_all'])) {
    $data = $pdo->query("SELECT r.*, e.title as event_title, e.event_date, e.location FROM registrations r JOIN events e ON r.event_id = e.id ORDER BY r.created_at DESC")->fetchAll();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="all_registrations_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID','Event','Full Name','Email','Phone','Business Name','Ticket Type','Amount Paid','Payment Status','Transaction Ref','Registration Date']);
    foreach ($data as $row) {
        fputcsv($output, [$row['id'],$row['event_title'],$row['full_name'],$row['email'],$row['phone'],$row['business_name'],$row['ticket_type'],$row['amount_paid'],$row['payment_status'],$row['transaction_ref'],$row['created_at']]);
    }
    fclose($output);
    exit();
}

// ──────────────────────────────────────────────
// FETCH DATA
// ──────────────────────────────────────────────
$totalRegistrations  = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$paidRegistrations   = $pdo->query("SELECT COUNT(*) FROM registrations WHERE payment_status='paid'")->fetchColumn();
$pendingRegistrations= $pdo->query("SELECT COUNT(*) FROM registrations WHERE payment_status='pending'")->fetchColumn();
$totalRevenue        = $pdo->query("SELECT COALESCE(SUM(amount_paid),0) FROM registrations WHERE payment_status='paid'")->fetchColumn();

$allEvents           = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
$recentRegistrations = $pdo->query("SELECT r.*, e.title as event_title FROM registrations r JOIN events e ON r.event_id = e.id ORDER BY r.created_at DESC LIMIT 15")->fetchAll();

// Edit event fetch
$editEvent = null;
if (isset($_GET['edit_event'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
    $stmt->execute([intval($_GET['edit_event'])]);
    $editEvent = $stmt->fetch();
}

// Event detail view
$selectedEventId = $_GET['event_id'] ?? null;
$eventRegistrations = [];
if ($selectedEventId) {
    $stmt = $pdo->prepare("SELECT * FROM registrations WHERE event_id=? ORDER BY created_at DESC");
    $stmt->execute([$selectedEventId]);
    $eventRegistrations = $stmt->fetchAll();
}

// Active tab
$activeTab = $_GET['tab'] ?? 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — Mahanaim Impact Network</title>
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

/* ── STAT CARDS ── */
.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem; }

.stat-card {
  background: var(--white);
  border-radius: 16px;
  padding: 1.5rem;
  border: 1px solid rgba(0,0,0,.05);
  box-shadow: 0 2px 12px rgba(0,0,0,.04);
  position: relative;
  overflow: hidden;
  transition: transform .2s, box-shadow .2s;
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0,0,0,.08);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0; right: 0;
  width: 80px; height: 80px;
  border-radius: 0 16px 0 80px;
  opacity: .08;
}

.stat-card.purple::before { background: var(--royal); }
.stat-card.gold-c::before { background: var(--gold); }
.stat-card.green-c::before { background: var(--success); }
.stat-card.orange-c::before { background: var(--warning); }

.stat-icon {
  width: 44px; height: 44px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem;
  margin-bottom: 1rem;
}

.purple .stat-icon { background: rgba(75,0,130,.1); color: var(--royal); }
.gold-c .stat-icon { background: rgba(212,175,55,.12); color: #b8960e; }
.green-c .stat-icon { background: rgba(46,204,113,.1); color: var(--success); }
.orange-c .stat-icon { background: rgba(243,156,18,.1); color: var(--warning); }

.stat-value {
  font-family: 'Sora', sans-serif;
  font-size: 2rem;
  font-weight: 800;
  color: var(--ink);
  line-height: 1;
  margin-bottom: .25rem;
}

.stat-label { font-size: .8rem; color: var(--muted); font-weight: 500; }

/* ── SECTION CARDS ── */
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

/* ── TABS ── */
.dash-tabs {
  display: flex;
  gap: .25rem;
  background: rgba(75,0,130,.06);
  border-radius: 12px;
  padding: .3rem;
  margin-bottom: 2rem;
  width: fit-content;
}

.dash-tab {
  padding: .55rem 1.25rem;
  border-radius: 9px;
  font-size: .85rem;
  font-weight: 600;
  color: var(--muted);
  cursor: pointer;
  border: none;
  background: transparent;
  transition: all .2s;
  white-space: nowrap;
}

.dash-tab.active {
  background: var(--white);
  color: var(--royal);
  box-shadow: 0 2px 8px rgba(0,0,0,.1);
}

/* ── TABLES ── */
.custom-table { width: 100%; border-collapse: collapse; }
.custom-table th {
  padding: .75rem 1rem;
  font-size: .75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .06em;
  color: var(--muted);
  border-bottom: 1px solid rgba(0,0,0,.06);
  background: rgba(75,0,130,.02);
  white-space: nowrap;
}

.custom-table td {
  padding: .875rem 1rem;
  font-size: .875rem;
  border-bottom: 1px solid rgba(0,0,0,.04);
  vertical-align: middle;
}

.custom-table tr:last-child td { border-bottom: none; }
.custom-table tr:hover td { background: rgba(75,0,130,.02); }

/* ── BADGES ── */
.badge-status {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  padding: .3rem .7rem;
  border-radius: 50px;
  font-size: .72rem;
  font-weight: 700;
}

.badge-paid { background: rgba(46,204,113,.1); color: #1a9c54; }
.badge-pending { background: rgba(243,156,18,.1); color: #c07d00; }
.badge-active { background: rgba(75,0,130,.1); color: var(--royal); }
.badge-inactive { background: rgba(108,117,125,.1); color: var(--muted); }
.badge-vendor { background: rgba(52,152,219,.1); color: #1a6fa0; }
.badge-attendee { background: rgba(155,89,182,.1); color: #6c3483; }
.badge-sponsor { background: rgba(212,175,55,.15); color: #8a6914; }

/* ── BUTTONS ── */
.btn-royal {
  background: var(--royal);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: .6rem 1.25rem;
  font-size: .875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
}

.btn-royal:hover { background: var(--royal-light); color: #fff; }

.btn-gold-sm {
  background: var(--gold);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: .35rem .85rem;
  font-size: .78rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .3rem;
}

.btn-gold-sm:hover { background: #c49b25; color: #fff; }

.btn-ghost {
  background: transparent;
  border: 1.5px solid rgba(75,0,130,.2);
  color: var(--royal);
  border-radius: 8px;
  padding: .35rem .85rem;
  font-size: .78rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: .3rem;
}

.btn-ghost:hover { background: rgba(75,0,130,.06); color: var(--royal); border-color: var(--royal); }

.btn-danger-sm {
  background: rgba(231,76,60,.08);
  border: 1px solid rgba(231,76,60,.2);
  color: var(--danger);
  border-radius: 8px;
  padding: .35rem .85rem;
  font-size: .78rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  display: inline-flex;
  align-items: center;
  gap: .3rem;
}

.btn-danger-sm:hover { background: rgba(231,76,60,.15); }

/* ── FORM STYLING ── */
.form-section {
  background: var(--white);
  border-radius: 16px;
  border: 1px solid rgba(0,0,0,.05);
  padding: 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 12px rgba(0,0,0,.04);
}

.form-section h5 {
  font-family: 'Sora', sans-serif;
  font-size: 1rem;
  font-weight: 700;
  color: var(--royal);
  margin-bottom: 1.25rem;
  padding-bottom: .75rem;
  border-bottom: 1px solid rgba(0,0,0,.06);
}

.form-label {
  font-size: .8rem;
  font-weight: 600;
  color: #444;
  margin-bottom: .4rem;
}

.form-control, .form-select {
  border: 1.5px solid rgba(0,0,0,.1);
  border-radius: 10px;
  padding: .6rem .9rem;
  font-size: .875rem;
  transition: border-color .2s, box-shadow .2s;
}

.form-control:focus, .form-select:focus {
  border-color: var(--royal);
  box-shadow: 0 0 0 3px rgba(75,0,130,.08);
  outline: none;
}

/* ── ALERT ── */
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

/* ── EVENT CARDS ── */
.event-card {
  border: 1.5px solid rgba(0,0,0,.07);
  border-radius: 14px;
  padding: 1.25rem;
  background: var(--white);
  transition: box-shadow .2s, transform .2s;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.event-card:hover {
  box-shadow: 0 6px 20px rgba(75,0,130,.1);
  transform: translateY(-2px);
}

.event-card-title {
  font-family: 'Sora', sans-serif;
  font-size: .95rem;
  font-weight: 700;
  color: var(--royal);
  margin-bottom: .5rem;
}

.event-meta { font-size: .8rem; color: var(--muted); margin-bottom: .25rem; }
.event-meta i { width: 16px; color: var(--gold); }

.event-price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: .75rem;
  margin-top: auto;
  border-top: 1px solid rgba(0,0,0,.06);
}

.price-tag {
  font-family: 'Sora', sans-serif;
  font-weight: 700;
  font-size: 1rem;
  color: var(--royal);
}

.event-actions { display: flex; gap: .4rem; flex-wrap: wrap; }

/* ── EMPTY STATE ── */
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: var(--muted);
}

.empty-state i {
  font-size: 3rem;
  opacity: .3;
  margin-bottom: 1rem;
  display: block;
  color: var(--royal);
}

/* ── MODAL ── */
.modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
.modal-header { border-bottom: 1px solid rgba(0,0,0,.06); padding: 1.25rem 1.5rem; }
.modal-title { font-family: 'Sora', sans-serif; font-weight: 700; color: var(--royal); }
.modal-footer { border-top: 1px solid rgba(0,0,0,.06); }

/* ── RESPONSIVE ── */
@media (max-width: 991px) {
  .sidebar { width: 220px; }
  .main-wrap { margin-left: 220px; }
  .stat-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 767px) {
  .sidebar { display: none; }
  .main-wrap { margin-left: 0; }
  .stat-grid { grid-template-columns: 1fr 1fr; }
  .page-body { padding: 1rem; }
}

/* ── SEARCH BAR ── */
.search-bar {
  position: relative;
  width: 240px;
}

.search-bar input {
  padding-left: 2.4rem;
  border-radius: 50px;
  font-size: .82rem;
  border: 1.5px solid rgba(0,0,0,.1);
}

.search-bar i {
  position: absolute;
  left: .9rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  font-size: .8rem;
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
      <a href="?tab=overview" class="<?php echo $activeTab==='overview'?'active':''; ?>">
        <span class="nav-icon"><i class="fas fa-th-large"></i></span>Overview
      </a>
    </li>
    <li>
      <a href="?tab=events" class="<?php echo $activeTab==='events'?'active':''; ?>">
        <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>Events
      </a>
    </li>
    <li>
      <a href="?tab=registrations" class="<?php echo $activeTab==='registrations'?'active':''; ?>">
        <span class="nav-icon"><i class="fas fa-users"></i></span>Registrations
      </a>
    </li>
    <li>
      <a href="?tab=payments" class="<?php echo $activeTab==='payments'?'active':''; ?>">
        <span class="nav-icon"><i class="fas fa-credit-card"></i></span>Payments
      </a>
    </li>
    <li>
      <a href="?tab=create_event" class="<?php echo $activeTab==='create_event'?'active':''; ?>">
        <span class="nav-icon"><i class="fas fa-plus-circle"></i></span>Add Event
      </a>
    </li>
    <li>
      <a href="gallery_manage.php" class="<?php echo basename($_SERVER['PHP_SELF'])==='gallery_manage.php'?'active':''; ?>">
        <span class="nav-icon"><i class="fas fa-images"></i></span>Gallery
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
      <a href="?export_all=1">
        <span class="nav-icon"><i class="fas fa-file-csv"></i></span>Export All CSV
      </a>
    </li>
  </ul>
  
  <div class="sidebar-footer">
    <a href="settings.php"><i class="fas fa-gear"></i> Settings</a>
  </div>

  <div class="sidebar-footer">
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>

<!-- ══════════════════════════════════════ MAIN ══════════════════════════════════════ -->
<div class="main-wrap">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-title">
      <?php
        $titles = ['overview'=>'Dashboard Overview','events'=>'Manage Events','registrations'=>'Registrations','payments'=>'Payments','create_event'=>'Create New Event'];
        echo $titles[$activeTab] ?? 'Dashboard';
      ?>
    </div>
    <div class="topbar-actions">
      <a href="?tab=create_event" class="btn-royal"><i class="fas fa-plus"></i> New Event</a>
      <div class="admin-chip">
        <div class="dot"></div>
        <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
      </div>
    </div>
  </div>

  <div class="page-body">

    <?php if ($message): ?>
    <div class="alert-banner alert-<?php echo $messageType; ?>">
      <i class="fas fa-<?php echo $messageType==='success'?'check-circle':($messageType==='warning'?'exclamation-triangle':'times-circle'); ?>"></i>
      <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- ══ TAB: OVERVIEW ══ -->
    <?php if ($activeTab === 'overview'): ?>

    <!-- Stats -->
    <div class="stat-grid">
      <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-value"><?php echo $totalRegistrations; ?></div>
        <div class="stat-label">Total Registrations</div>
      </div>
      <div class="stat-card green-c">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?php echo $paidRegistrations; ?></div>
        <div class="stat-label">Confirmed Paid</div>
      </div>
      <div class="stat-card orange-c">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-value"><?php echo $pendingRegistrations; ?></div>
        <div class="stat-label">Pending Payment</div>
      </div>
      <div class="stat-card gold-c">
        <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-value" style="font-size:1.5rem;">₦<?php echo number_format($totalRevenue, 0); ?></div>
        <div class="stat-label">Total Revenue</div>
      </div>
    </div>

    <!-- Events Summary -->
    <div class="section-card">
      <div class="section-card-header">
        <h5><i class="fas fa-calendar-alt"></i> Events Summary</h5>
        <a href="?tab=events" class="btn-ghost"><i class="fas fa-arrow-right"></i> Manage Events</a>
      </div>
      <div style="overflow-x:auto;">
        <table class="custom-table">
          <thead>
            <tr>
              <th>Event</th>
              <th>Date</th>
              <th>Location</th>
              <th>Status</th>
              <th>Registrations</th>
              <th>Revenue</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allEvents as $ev):
              $s = $pdo->prepare("SELECT COUNT(*) as c, COALESCE(SUM(amount_paid),0) as r FROM registrations WHERE event_id=? AND payment_status='paid'");
              $s->execute([$ev['id']]); $es = $s->fetch();
            ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($ev['title']); ?></strong></td>
              <td><?php echo date('M d, Y', strtotime($ev['event_date'])); ?></td>
              <td><?php echo htmlspecialchars($ev['location']); ?></td>
              <td>
                <span class="badge-status badge-<?php echo $ev['status']==='active'?'active':'inactive'; ?>">
                  <i class="fas fa-circle" style="font-size:.4rem;"></i>
                  <?php echo ucfirst($ev['status']); ?>
                </span>
              </td>
              <td><strong><?php echo $es['c']; ?></strong></td>
              <td>₦<?php echo number_format($es['r'], 0); ?></td>
              <td>
                <a href="?tab=registrations&event_id=<?php echo $ev['id']; ?>" class="btn-ghost"><i class="fas fa-eye"></i></a>
                <a href="?export=1&event_id=<?php echo $ev['id']; ?>" class="btn-gold-sm"><i class="fas fa-download"></i></a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($allEvents)): ?>
            <tr><td colspan="7"><div class="empty-state"><i class="fas fa-calendar-times"></i>No events yet. <a href="?tab=create_event">Create one</a></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Registrations -->
    <div class="section-card">
      <div class="section-card-header">
        <h5><i class="fas fa-history"></i> Recent Registrations</h5>
        <a href="?tab=registrations" class="btn-ghost">View All</a>
      </div>
      <div style="overflow-x:auto;">
        <table class="custom-table">
          <thead>
            <tr><th>ID</th><th>Event</th><th>Name</th><th>Email</th><th>Ticket</th><th>Amount</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody>
            <?php foreach ($recentRegistrations as $reg): ?>
            <tr>
              <td><code style="color:var(--royal);font-size:.8rem;">#<?php echo $reg['id']; ?></code></td>
              <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($reg['event_title']); ?></td>
              <td><?php echo htmlspecialchars($reg['full_name']); ?></td>
              <td style="font-size:.8rem;"><?php echo htmlspecialchars($reg['email']); ?></td>
              <td><span class="badge-status badge-<?php echo $reg['ticket_type']; ?>"><?php echo ucfirst($reg['ticket_type']); ?></span></td>
              <td>₦<?php echo number_format($reg['amount_paid'], 0); ?></td>
              <td>
                <span class="badge-status badge-<?php echo $reg['payment_status']==='paid'?'paid':'pending'; ?>">
                  <?php echo $reg['payment_status']==='paid'?'✓ Paid':'⏳ Pending'; ?>
                </span>
              </td>
              <td style="font-size:.78rem;color:var(--muted);"><?php echo date('M d, H:i', strtotime($reg['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recentRegistrations)): ?>
            <tr><td colspan="8"><div class="empty-state"><i class="fas fa-inbox"></i>No registrations yet.</div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ══ TAB: EVENTS ══ -->
    <?php elseif ($activeTab === 'events'): ?>

    <?php if ($editEvent): ?>
    <!-- Edit Event Form -->
    <div class="form-section">
      <h5><i class="fas fa-edit me-2"></i>Edit Event: <?php echo htmlspecialchars($editEvent['title']); ?></h5>
      <form method="POST">
        <input type="hidden" name="action" value="update_event">
        <input type="hidden" name="event_id" value="<?php echo $editEvent['id']; ?>">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">Event Title *</label>
            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($editEvent['title']); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active" <?php echo $editEvent['status']==='active'?'selected':''; ?>>Active</option>
              <option value="inactive" <?php echo $editEvent['status']==='inactive'?'selected':''; ?>>Inactive</option>
              <option value="cancelled" <?php echo $editEvent['status']==='cancelled'?'selected':''; ?>>Cancelled</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($editEvent['description']); ?></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Location *</label>
            <input type="text" name="location" class="form-control" required value="<?php echo htmlspecialchars($editEvent['location']); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Event Date *</label>
            <input type="date" name="event_date" class="form-control" required value="<?php echo $editEvent['event_date']; ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Regular Price (₦) *</label>
            <input type="number" name="regular_price" class="form-control" required min="0" value="<?php echo $editEvent['regular_price']; ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Early Bird Price (₦)</label>
            <input type="number" name="early_bird_price" class="form-control" min="0" value="<?php echo $editEvent['early_bird_price']; ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Early Bird Deadline</label>
            <input type="date" name="early_bird_deadline" class="form-control" value="<?php echo $editEvent['early_bird_deadline']; ?>">
          </div>
          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn-royal"><i class="fas fa-save"></i> Save Changes</button>
            <a href="?tab=events" class="btn-ghost">Cancel</a>
          </div>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <!-- Events Grid -->
    <div class="section-card-header" style="margin-bottom:1rem;">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
        <h5 style="font-family:'Sora',sans-serif;font-weight:700;color:var(--royal);margin:0;">
          <i class="fas fa-calendar-alt me-2"></i> All Events (<?php echo count($allEvents); ?>)
        </h5>
        <a href="?tab=create_event" class="btn-royal"><i class="fas fa-plus"></i> New Event</a>
      </div>
    </div>

    <?php if (empty($allEvents)): ?>
    <div class="section-card"><div class="empty-state">
      <i class="fas fa-calendar-plus"></i>
      <p>No events yet.</p>
      <a href="?tab=create_event" class="btn-royal">Create Your First Event</a>
    </div></div>
    <?php else: ?>
    <div class="row g-3">
      <?php foreach ($allEvents as $ev):
        $s = $pdo->prepare("SELECT COUNT(*) as c, COALESCE(SUM(amount_paid),0) as r FROM registrations WHERE event_id=? AND payment_status='paid'");
        $s->execute([$ev['id']]); $es = $s->fetch();
        $isPast = strtotime($ev['event_date']) < time();
      ?>
      <div class="col-md-6 col-lg-4">
        <div class="event-card">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.5rem;">
            <span class="badge-status badge-<?php echo $ev['status']==='active'?'active':'inactive'; ?>">
              <?php echo ucfirst($ev['status']); ?>
              <?php if ($isPast): ?> · Past<?php endif; ?>
            </span>
            <span style="font-size:.75rem;color:var(--muted);">#<?php echo $ev['id']; ?></span>
          </div>

          <div class="event-card-title"><?php echo htmlspecialchars($ev['title']); ?></div>
          <div class="event-meta"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ev['location']); ?></div>
          <div class="event-meta"><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($ev['event_date'])); ?></div>
          <div class="event-meta"><i class="fas fa-users"></i> <?php echo $es['c']; ?> paid registrations · ₦<?php echo number_format($es['r'],0); ?> revenue</div>

          <div class="event-price-row">
            <div>
              <div style="font-size:.7rem;color:var(--muted);">Regular Price</div>
              <div class="price-tag">₦<?php echo number_format($ev['regular_price'],0); ?></div>
            </div>
            <div class="event-actions">
              <a href="?tab=events&edit_event=<?php echo $ev['id']; ?>" class="btn-ghost"><i class="fas fa-edit"></i></a>
              <a href="?tab=registrations&event_id=<?php echo $ev['id']; ?>" class="btn-gold-sm"><i class="fas fa-eye"></i></a>
              <a href="?export=1&event_id=<?php echo $ev['id']; ?>" class="btn-ghost"><i class="fas fa-download"></i></a>
              <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this event and ALL its registrations?');">
                <input type="hidden" name="action" value="delete_event">
                <input type="hidden" name="event_id" value="<?php echo $ev['id']; ?>">
                <button type="submit" class="btn-danger-sm"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ══ TAB: REGISTRATIONS ══ -->
    <?php elseif ($activeTab === 'registrations'): ?>

    <!-- Filter by Event -->
    <div class="section-card" style="margin-bottom:1rem;">
      <div class="section-card-body" style="padding:1rem 1.5rem;">
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
          <label style="font-size:.82rem;font-weight:600;color:var(--muted);">Filter by Event:</label>
          <select class="form-select" style="max-width:320px;" onchange="window.location='?tab=registrations&event_id='+this.value">
            <option value="">All Events</option>
            <?php foreach ($allEvents as $ev): ?>
            <option value="<?php echo $ev['id']; ?>" <?php echo $selectedEventId==$ev['id']?'selected':''; ?>>
              <?php echo htmlspecialchars($ev['title']); ?> (<?php echo date('M d, Y',strtotime($ev['event_date'])); ?>)
            </option>
            <?php endforeach; ?>
          </select>
          <?php if ($selectedEventId): ?>
          <a href="?export=1&event_id=<?php echo $selectedEventId; ?>" class="btn-gold-sm"><i class="fas fa-download"></i> Export CSV</a>
          <?php else: ?>
          <a href="?export_all=1" class="btn-gold-sm"><i class="fas fa-download"></i> Export All</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php
    // Decide which registrations to show
    if ($selectedEventId) {
      $displayRegs = $eventRegistrations;
    } else {
      $displayRegs = $pdo->query("SELECT r.*, e.title as event_title FROM registrations r JOIN events e ON r.event_id=e.id ORDER BY r.created_at DESC")->fetchAll();
    }
    ?>

    <div class="section-card">
      <div class="section-card-header">
        <h5><i class="fas fa-users"></i> Registrations (<?php echo count($displayRegs); ?>)</h5>
      </div>
      <div style="overflow-x:auto;">
        <table class="custom-table">
          <thead>
            <tr><th>ID</th><th>Event</th><th>Name</th><th>Email</th><th>Phone</th><th>Business</th><th>Ticket</th><th>Amount</th><th>Status</th><th>Ref</th><th>Date</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($displayRegs as $reg): ?>
            <tr>
              <td><code style="color:var(--royal);font-size:.78rem;">#<?php echo $reg['id']; ?></code></td>
              <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.8rem;"><?php echo htmlspecialchars($reg['event_title'] ?? '—'); ?></td>
              <td style="white-space:nowrap;"><?php echo htmlspecialchars($reg['full_name']); ?></td>
              <td style="font-size:.8rem;"><?php echo htmlspecialchars($reg['email']); ?></td>
              <td style="font-size:.8rem;"><?php echo htmlspecialchars($reg['phone']); ?></td>
              <td style="font-size:.8rem;"><?php echo htmlspecialchars($reg['business_name'] ?: '—'); ?></td>
              <td><span class="badge-status badge-<?php echo $reg['ticket_type']; ?>"><?php echo ucfirst($reg['ticket_type']); ?></span></td>
              <td style="white-space:nowrap;font-weight:600;">₦<?php echo number_format($reg['amount_paid'], 0); ?></td>
              <td>
                <span class="badge-status badge-<?php echo $reg['payment_status']==='paid'?'paid':'pending'; ?>">
                  <?php echo $reg['payment_status']==='paid'?'✓ Paid':'⏳ Pending'; ?>
                </span>
              </td>
              <td style="font-size:.72rem;color:var(--muted);"><?php echo htmlspecialchars($reg['transaction_ref'] ?? '—'); ?></td>
              <td style="font-size:.78rem;white-space:nowrap;color:var(--muted);"><?php echo date('M d, Y', strtotime($reg['created_at'])); ?></td>
              <td style="white-space:nowrap;">
                <!-- Update Status -->
                <form method="POST" style="display:inline;" class="d-inline-flex gap-1 align-items-center">
                  <input type="hidden" name="action" value="update_payment">
                  <input type="hidden" name="registration_id" value="<?php echo $reg['id']; ?>">
                  <input type="hidden" name="tab" value="registrations">
                  <select name="payment_status" class="form-select form-select-sm" style="font-size:.72rem;min-width:90px;border-radius:6px;padding:.2rem .4rem;">
                    <option value="pending" <?php echo $reg['payment_status']==='pending'?'selected':''; ?>>Pending</option>
                    <option value="paid"    <?php echo $reg['payment_status']==='paid'?'selected':''; ?>>Paid</option>
                    <option value="failed"  <?php echo $reg['payment_status']==='failed'?'selected':''; ?>>Failed</option>
                  </select>
                  <button type="submit" class="btn-gold-sm" style="padding:.25rem .5rem;" title="Save"><i class="fas fa-check"></i></button>
                </form>
                <!-- Delete -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this registration?');">
                  <input type="hidden" name="action" value="delete_registration">
                  <input type="hidden" name="registration_id" value="<?php echo $reg['id']; ?>">
                  <button type="submit" class="btn-danger-sm" style="padding:.25rem .5rem;" title="Delete"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($displayRegs)): ?>
            <tr><td colspan="12"><div class="empty-state"><i class="fas fa-inbox"></i>No registrations found.</div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ══ TAB: PAYMENTS ══ -->
    <?php elseif ($activeTab === 'payments'): ?>

    <!-- Payment Stats -->
    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);">
      <div class="stat-card green-c">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value"><?php echo $paidRegistrations; ?></div>
        <div class="stat-label">Paid</div>
      </div>
      <div class="stat-card orange-c">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-value"><?php echo $pendingRegistrations; ?></div>
        <div class="stat-label">Pending</div>
      </div>
      <div class="stat-card gold-c">
        <div class="stat-icon"><i class="fas fa-coins"></i></div>
        <div class="stat-value" style="font-size:1.4rem;">₦<?php echo number_format($totalRevenue,0); ?></div>
        <div class="stat-label">Total Collected</div>
      </div>
    </div>

    <!-- Pending Payments — needs action -->
    <?php
    $pendingList = $pdo->query("SELECT r.*, e.title as event_title FROM registrations r JOIN events e ON r.event_id=e.id WHERE r.payment_status='pending' ORDER BY r.created_at DESC")->fetchAll();
    ?>
    <div class="section-card">
      <div class="section-card-header">
        <h5 style="color:#c07d00;"><i class="fas fa-exclamation-triangle"></i> Pending Payments (<?php echo count($pendingList); ?>)</h5>
        <small style="color:var(--muted);">Manually verify and mark as paid below</small>
      </div>
      <div style="overflow-x:auto;">
        <table class="custom-table">
          <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Event</th><th>Amount</th><th>Ref</th><th>Date</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php foreach ($pendingList as $reg): ?>
            <tr>
              <td><code style="color:var(--royal);font-size:.78rem;">#<?php echo $reg['id']; ?></code></td>
              <td><?php echo htmlspecialchars($reg['full_name']); ?></td>
              <td style="font-size:.8rem;"><?php echo htmlspecialchars($reg['email']); ?></td>
              <td style="font-size:.8rem;"><?php echo htmlspecialchars($reg['event_title']); ?></td>
              <td style="font-weight:600;">₦<?php echo number_format($reg['amount_paid'],0); ?></td>
              <td style="font-size:.72rem;color:var(--muted);font-family:monospace;"><?php echo htmlspecialchars($reg['transaction_ref'] ?? '—'); ?></td>
              <td style="font-size:.78rem;color:var(--muted);"><?php echo date('M d, Y H:i', strtotime($reg['created_at'])); ?></td>
              <td>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="update_payment">
                  <input type="hidden" name="registration_id" value="<?php echo $reg['id']; ?>">
                  <input type="hidden" name="payment_status" value="paid">
                  <button type="submit" class="btn-gold-sm" onclick="return confirm('Mark as PAID?')">
                    <i class="fas fa-check"></i> Mark Paid
                  </button>
                </form>
                <form method="POST" style="display:inline;margin-left:.3rem;">
                  <input type="hidden" name="action" value="update_payment">
                  <input type="hidden" name="registration_id" value="<?php echo $reg['id']; ?>">
                  <input type="hidden" name="payment_status" value="failed">
                  <button type="submit" class="btn-danger-sm" onclick="return confirm('Mark as FAILED?')">
                    <i class="fas fa-times"></i> Failed
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pendingList)): ?>
            <tr><td colspan="8"><div class="empty-state"><i class="fas fa-check-double" style="color:var(--success);opacity:1;"></i>All payments resolved!</div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ══ TAB: CREATE EVENT ══ -->
    <?php elseif ($activeTab === 'create_event'): ?>

    <div class="form-section">
      <h5><i class="fas fa-calendar-plus me-2"></i>Create New Event</h5>
      <form method="POST">
        <input type="hidden" name="action" value="create_event">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">Event Title *</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Mahanaim Trade Fair 2025" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="active">Active (visible on site)</option>
              <option value="inactive">Inactive (hidden)</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Describe the event..."></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Location *</label>
            <input type="text" name="location" class="form-control" placeholder="e.g. Lagos, Nigeria" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Event Date *</label>
            <input type="date" name="event_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
          </div>

          <div class="col-12">
            <div style="background:rgba(75,0,130,.04);border:1.5px dashed rgba(75,0,130,.15);border-radius:12px;padding:1.25rem;margin-top:.5rem;">
              <div style="font-size:.85rem;font-weight:700;color:var(--royal);margin-bottom:.75rem;"><i class="fas fa-tag me-1"></i> Pricing</div>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Regular Price (₦) *</label>
                  <input type="number" name="regular_price" class="form-control" placeholder="e.g. 5000" min="0" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Early Bird Price (₦)</label>
                  <input type="number" name="early_bird_price" class="form-control" placeholder="e.g. 3000" min="0">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Early Bird Deadline</label>
                  <input type="date" name="early_bird_deadline" class="form-control">
                </div>
              </div>
            </div>
          </div>

          <div class="col-12" style="padding-top:.5rem;">
            <button type="submit" class="btn-royal" style="padding:.75rem 2rem;font-size:.95rem;">
              <i class="fas fa-plus-circle"></i> Create Event
            </button>
            <a href="?tab=events" class="btn-ghost" style="margin-left:.75rem;">Cancel</a>
          </div>
        </div>
      </form>
    </div>

    <!-- Quick Fix Notice -->
   <!--  <div class="form-section" style="background:rgba(243,156,18,.05);border-color:rgba(243,156,18,.2);">
      <h5 style="color:#c07d00;"><i class="fas fa-lightbulb me-2"></i> Why is the event dropdown empty?</h5>
      <p style="font-size:.875rem;color:#555;line-height:1.7;">
        Your registration page and homepage only show events where <code>status = 'active'</code> AND <code>event_date >= CURDATE()</code>.
        If your database has no events, or all events have past dates, the dropdown will be empty.<br><br>
        <strong>Fix:</strong> Create a new event above with today's date or a future date and set status to <strong>Active</strong>.
        It will immediately appear on the registration page and homepage.
      </p>
      <div style="background:#fff;border-radius:10px;padding:1rem;border:1px solid rgba(0,0,0,.07);margin-top:.75rem;">
        <div style="font-size:.78rem;font-weight:700;color:var(--muted);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.06em;">SQL to manually insert a test event</div>
        <code style="font-size:.82rem;color:var(--royal);white-space:pre-wrap;">INSERT INTO events (title, description, location, event_date, early_bird_deadline, early_bird_price, regular_price, status)
VALUES ('Mahanaim Trade Fair 2025', 'Our flagship trade fair event.', 'Lagos, Nigeria',
        DATE_ADD(CURDATE(), INTERVAL 30 DAY),
        DATE_ADD(CURDATE(), INTERVAL 7 DAY),
        3000, 5000, 'active');</code>
      </div>
    </div> -->

    <?php endif; ?>

  </div><!-- /page-body -->
</div><!-- /main-wrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss flash messages
setTimeout(() => {
  const alert = document.querySelector('.alert-banner');
  if (alert) alert.style.opacity = '0', setTimeout(() => alert.remove(), 400);
}, 4000);
</script>
</body>
</html>
