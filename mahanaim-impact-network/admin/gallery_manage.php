<?php
// === admin/gallery_manage.php ===
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$messageType = '';

// ── UPLOAD ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'upload') {
        $title         = trim($_POST['title']);
        $category      = $_POST['category'];
        $description   = trim($_POST['description']);
        $display_order = intval($_POST['display_order'] ?: 0);
        $status        = $_POST['status'];

        if (empty($_FILES['image']['name'])) {
            $message = "Please select an image file.";
            $messageType = "danger";
        } else {
            $allowed  = ['image/jpeg','image/jpg','image/png','image/webp','image/gif'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);

            if (!in_array($fileType, $allowed)) {
                $message = "Only JPG, PNG, WEBP, and GIF are allowed.";
                $messageType = "danger";
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $message = "Image must be under 5MB.";
                $messageType = "danger";
            } else {
                $uploadDir = '../assets/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'upload_' . time() . '_' . rand(100,999) . '.' . strtolower($ext);
                $dest     = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image_path, description, display_order, status) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $category, 'assets/images/' . $filename, $description, $display_order, $status]);
                        $message = "Image uploaded successfully!";
                        $messageType = "success";
                    } catch (PDOException $e) {
                        @unlink($dest);
                        $message = "DB error: " . $e->getMessage();
                        $messageType = "danger";
                    }
                } else {
                    $message = "Upload failed — check folder permissions on assets/images/.";
                    $messageType = "danger";
                }
            }
        }
    }

    if ($_POST['action'] === 'update') {
        $id            = intval($_POST['gallery_id']);
        $title         = trim($_POST['title']);
        $category      = $_POST['category'];
        $description   = trim($_POST['description']);
        $display_order = intval($_POST['display_order'] ?: 0);
        $status        = $_POST['status'];
        try {
            $pdo->prepare("UPDATE gallery SET title=?, category=?, description=?, display_order=?, status=? WHERE id=?")
                ->execute([$title, $category, $description, $display_order, $status, $id]);
            $message = "Updated successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = "danger";
        }
    }

    if ($_POST['action'] === 'delete') {
        $id   = intval($_POST['gallery_id']);
        $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id=?");
        $stmt->execute([$id]);
        $row  = $stmt->fetch();
        if ($row) {
            $full = '../' . $row['image_path'];
            if (file_exists($full)) @unlink($full);
            $pdo->prepare("DELETE FROM gallery WHERE id=?")->execute([$id]);
            $message = "Image deleted.";
            $messageType = "warning";
        }
    }

    if ($_POST['action'] === 'toggle_status') {
        $id  = intval($_POST['gallery_id']);
        $new = $_POST['new_status'];
        $pdo->prepare("UPDATE gallery SET status=? WHERE id=?")->execute([$new, $id]);
        $message = "Status updated.";
        $messageType = "success";
    }
}

// ── FETCH ──
$filterCat = $_GET['cat'] ?? '';
if ($filterCat) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE category=? ORDER BY display_order ASC, upload_date DESC");
    $stmt->execute([$filterCat]);
} else {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY display_order ASC, upload_date DESC");
}
$images = $stmt->fetchAll();

$editItem     = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id=?");
    $stmt->execute([intval($_GET['edit'])]);
    $editItem = $stmt->fetch();
}

$totalImages  = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
$activeImages = $pdo->query("SELECT COUNT(*) FROM gallery WHERE status='active'")->fetchColumn();

$categories = [
    'trade_fair' => 'University Trade Fairs',
    'outreach'   => 'Orphanage & Elderly Outreaches',
    'mentorship' => 'Mentorship Programs',
    'academy'    => 'Academy & Training',
    'community'  => 'Community Impact',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gallery Manager — Mahanaim Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
  --royal:#4B0082; --royal-light:#6A0DAD; --gold:#D4AF37;
  --ink:#1a1a2e; --muted:#6c757d; --surface:#f8f7ff;
  --white:#ffffff; --success:#2ecc71; --danger:#e74c3c; --warning:#f39c12;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'DM Sans',sans-serif;background:var(--surface);color:var(--ink);min-height:100vh;}

/* SIDEBAR */
.sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;background:linear-gradient(160deg,#2d0060 0%,#4B0082 60%,#6A0DAD 100%);display:flex;flex-direction:column;z-index:100;box-shadow:4px 0 30px rgba(75,0,130,.25);overflow-y:auto;}
.sidebar-brand{padding:2rem 1.5rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.08);}
.sidebar-brand .logo-ring{width:56px;height:56px;border-radius:16px;background:rgba(212,175,55,.15);border:1.5px solid rgba(212,175,55,.35);display:flex;align-items:center;justify-content:center;margin-bottom:.75rem;}
.sidebar-brand h4{font-family:'Sora',sans-serif;font-size:.9rem;font-weight:700;color:#fff;margin:0;}
.sidebar-brand small{color:rgba(255,255,255,.45);font-size:.72rem;}
.nav-section{padding:.5rem 1rem;font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.3);margin-top:1rem;}
.sidebar-nav{list-style:none;padding:0 .75rem;flex:1;}
.sidebar-nav li{margin-bottom:.2rem;}
.sidebar-nav a{display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;color:rgba(255,255,255,.65);text-decoration:none;border-radius:10px;font-size:.875rem;font-weight:500;transition:all .2s;}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(212,175,55,.15);color:#fff;border-left:3px solid var(--gold);}
.sidebar-nav a .nav-icon{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,.07);display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;}
.sidebar-nav a.active .nav-icon{background:rgba(212,175,55,.25);color:var(--gold);}
.sidebar-footer{padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,.08);}
.sidebar-footer a{color:rgba(255,255,255,.5);text-decoration:none;font-size:.8rem;display:flex;align-items:center;gap:.5rem;}
.sidebar-footer a:hover{color:#fff;}

/* MAIN */
.main-wrap{margin-left:260px;min-height:100vh;display:flex;flex-direction:column;}
.topbar{background:var(--white);border-bottom:1px solid rgba(0,0,0,.06);padding:1rem 2rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
.topbar-title{font-family:'Sora',sans-serif;font-size:1.25rem;font-weight:700;color:var(--royal);}
.topbar-actions{display:flex;gap:.75rem;align-items:center;}
.admin-chip{display:flex;align-items:center;gap:.5rem;background:var(--surface);border:1px solid rgba(75,0,130,.12);border-radius:50px;padding:.35rem .85rem;font-size:.8rem;font-weight:600;color:var(--royal);}
.admin-chip .dot{width:8px;height:8px;border-radius:50%;background:var(--success);}
.page-body{padding:2rem;flex:1;}

/* ALERTS */
.alert-banner{padding:.875rem 1.25rem;border-radius:12px;font-size:.875rem;font-weight:500;margin-bottom:1.5rem;display:flex;align-items:center;gap:.6rem;}
.alert-success{background:rgba(46,204,113,.1);color:#1a7a44;border:1px solid rgba(46,204,113,.2);}
.alert-danger{background:rgba(231,76,60,.08);color:#a91c0c;border:1px solid rgba(231,76,60,.15);}
.alert-warning{background:rgba(243,156,18,.1);color:#7d5200;border:1px solid rgba(243,156,18,.2);}

/* STATS */
.stat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:2rem;}
.stat-card{background:var(--white);border-radius:16px;padding:1.5rem;border:1px solid rgba(0,0,0,.05);box-shadow:0 2px 12px rgba(0,0,0,.04);}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;margin-bottom:1rem;}
.stat-icon.purple{background:rgba(75,0,130,.1);color:var(--royal);}
.stat-icon.green{background:rgba(46,204,113,.1);color:var(--success);}
.stat-icon.gold{background:rgba(212,175,55,.12);color:#b8960e;}
.stat-value{font-family:'Sora',sans-serif;font-size:2rem;font-weight:800;color:var(--ink);line-height:1;margin-bottom:.25rem;}
.stat-label{font-size:.8rem;color:var(--muted);font-weight:500;}

/* CARDS */
.section-card{background:var(--white);border-radius:16px;border:1px solid rgba(0,0,0,.05);box-shadow:0 2px 12px rgba(0,0,0,.04);margin-bottom:1.5rem;overflow:hidden;}
.section-card-header{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(0,0,0,.05);display:flex;align-items:center;justify-content:space-between;}
.section-card-header h5{font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;color:var(--royal);margin:0;display:flex;align-items:center;gap:.5rem;}
.section-card-body{padding:1.5rem;}

/* FORM */
.form-label{font-size:.78rem;font-weight:600;color:#444;margin-bottom:.35rem;display:block;}
.form-control,.form-select{border:1.5px solid rgba(0,0,0,.1);border-radius:10px;padding:.6rem .9rem;font-size:.875rem;width:100%;transition:border-color .2s,box-shadow .2s;font-family:'DM Sans',sans-serif;}
.form-control:focus,.form-select:focus{border-color:var(--royal);box-shadow:0 0 0 3px rgba(75,0,130,.08);outline:none;}
.mb-form{margin-bottom:1rem;}

/* DROP ZONE */
.drop-zone{border:2px dashed rgba(75,0,130,.25);border-radius:14px;padding:2.5rem 1rem;text-align:center;cursor:pointer;transition:all .2s;background:rgba(75,0,130,.02);}
.drop-zone:hover,.drop-zone.dragover{border-color:var(--royal);background:rgba(75,0,130,.06);}
.drop-zone i{font-size:2.5rem;color:var(--royal);opacity:.4;margin-bottom:.75rem;display:block;}
.drop-zone p{font-size:.875rem;color:var(--muted);margin:0;}
#imagePreview{display:none;width:100%;max-height:200px;object-fit:cover;border-radius:10px;margin-top:1rem;}

/* BUTTONS */
.btn-royal{background:var(--royal);color:#fff;border:none;border-radius:10px;padding:.65rem 1.4rem;font-size:.875rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;font-family:'DM Sans',sans-serif;}
.btn-royal:hover{background:var(--royal-light);color:#fff;}
.btn-gold-sm{background:var(--gold);color:#fff;border:none;border-radius:8px;padding:.35rem .85rem;font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;}
.btn-gold-sm:hover{background:#c49b25;color:#fff;}
.btn-ghost{background:transparent;border:1.5px solid rgba(75,0,130,.2);color:var(--royal);border-radius:8px;padding:.35rem .85rem;font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;}
.btn-ghost:hover{background:rgba(75,0,130,.06);color:var(--royal);}
.btn-danger-sm{background:rgba(231,76,60,.08);border:1px solid rgba(231,76,60,.2);color:var(--danger);border-radius:8px;padding:.35rem .85rem;font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.3rem;}
.btn-danger-sm:hover{background:rgba(231,76,60,.15);}

/* GALLERY GRID */
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1.25rem;padding:1.5rem;}
.gallery-card{border-radius:14px;overflow:hidden;border:1px solid rgba(0,0,0,.07);background:var(--white);transition:box-shadow .2s,transform .2s;}
.gallery-card:hover{box-shadow:0 8px 24px rgba(75,0,130,.12);transform:translateY(-3px);}
.gallery-card img{width:100%;height:170px;object-fit:cover;display:block;}
.gallery-card-body{padding:.875rem 1rem;}
.gallery-card-title{font-family:'Sora',sans-serif;font-size:.85rem;font-weight:700;color:var(--royal);margin-bottom:.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.gallery-card-meta{font-size:.72rem;color:var(--muted);margin-bottom:.5rem;}
.gallery-card-actions{display:flex;gap:.4rem;flex-wrap:wrap;}

/* BADGES */
.badge-status{display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .6rem;border-radius:50px;font-size:.68rem;font-weight:700;}
.badge-active{background:rgba(46,204,113,.1);color:#1a9c54;}
.badge-inactive{background:rgba(108,117,125,.1);color:var(--muted);}
.cat-badge{display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.68rem;font-weight:600;background:rgba(75,0,130,.08);color:var(--royal);margin-bottom:.35rem;}

/* FILTER TABS */
.filter-tabs{display:flex;gap:.35rem;flex-wrap:wrap;margin-bottom:1rem;}
.filter-tab{padding:.4rem 1rem;border-radius:50px;font-size:.78rem;font-weight:600;border:1.5px solid rgba(75,0,130,.15);color:var(--muted);text-decoration:none;transition:all .2s;}
.filter-tab:hover,.filter-tab.active{background:var(--royal);color:#fff;border-color:var(--royal);}

/* EMPTY */
.empty-state{text-align:center;padding:3rem 1rem;color:var(--muted);}
.empty-state i{font-size:3rem;opacity:.3;margin-bottom:1rem;display:block;color:var(--royal);}

@media(max-width:767px){.sidebar{display:none;}.main-wrap{margin-left:0;}.page-body{padding:1rem;}.stat-grid{grid-template-columns:1fr 1fr;}}
</style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo-ring"><i class="fas fa-hands-helping" style="color:var(--gold);font-size:1.4rem;"></i></div>
    <h4>Mahanaim Admin</h4>
    <small>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></small>
  </div>
  <div class="nav-section">Main</div>
  <ul class="sidebar-nav">
    <li><a href="dashboard.php?tab=overview"><span class="nav-icon"><i class="fas fa-th-large"></i></span>Overview</a></li>
    <li><a href="dashboard.php?tab=events"><span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>Events</a></li>
    <li><a href="dashboard.php?tab=registrations"><span class="nav-icon"><i class="fas fa-users"></i></span>Registrations</a></li>
    <li><a href="dashboard.php?tab=payments"><span class="nav-icon"><i class="fas fa-credit-card"></i></span>Payments</a></li>
    <li><a href="dashboard.php?tab=create_event"><span class="nav-icon"><i class="fas fa-plus-circle"></i></span>Add Event</a></li>
    <li><a href="gallery_manage.php" class="active"><span class="nav-icon"><i class="fas fa-images"></i></span>Gallery</a></li>
  </ul>
  <div class="nav-section">Links</div>
  <ul class="sidebar-nav">
    <li><a href="../index.php" target="_blank"><span class="nav-icon"><i class="fas fa-globe"></i></span>View Website</a></li>
    <li><a href="../gallery.php" target="_blank"><span class="nav-icon"><i class="fas fa-eye"></i></span>View Gallery</a></li>
  </ul>
  <div class="sidebar-footer"><a href="settings.php"><i class="fas fa-gear"></i> Settings</a></div>
  <div class="sidebar-footer"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
</aside>

<div class="main-wrap">
  <div class="topbar">
    <div class="topbar-title">Gallery Manager</div>
    <div class="topbar-actions">
      <a href="../gallery.php" target="_blank" class="btn-ghost"><i class="fas fa-external-link-alt"></i> View Gallery</a>
      <div class="admin-chip"><div class="dot"></div><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></div>
    </div>
  </div>

  <div class="page-body">

    <?php if ($message): ?>
    <div class="alert-banner alert-<?php echo $messageType; ?>">
      <i class="fas fa-<?php echo $messageType==='success'?'check-circle':($messageType==='warning'?'exclamation-triangle':'times-circle'); ?>"></i>
      <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="stat-grid">
      <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-images"></i></div><div class="stat-value"><?php echo $totalImages; ?></div><div class="stat-label">Uploaded Images</div></div>
      <div class="stat-card"><div class="stat-icon green"><i class="fas fa-eye"></i></div><div class="stat-value"><?php echo $activeImages; ?></div><div class="stat-label">Visible on Site</div></div>
      <div class="stat-card"><div class="stat-icon gold"><i class="fas fa-eye-slash"></i></div><div class="stat-value"><?php echo $totalImages - $activeImages; ?></div><div class="stat-label">Hidden</div></div>
    </div>

    <!-- UPLOAD / EDIT -->
    <div class="section-card">
      <div class="section-card-header">
        <h5><i class="fas fa-<?php echo $editItem?'edit':'cloud-upload-alt'; ?>"></i>
          <?php echo $editItem ? 'Edit Image Details' : 'Upload New Image'; ?>
        </h5>
        <?php if ($editItem): ?>
          <a href="gallery_manage.php" class="btn-ghost"><i class="fas fa-times"></i> Cancel</a>
        <?php endif; ?>
      </div>
      <div class="section-card-body">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="<?php echo $editItem?'update':'upload'; ?>">
          <?php if ($editItem): ?>
            <input type="hidden" name="gallery_id" value="<?php echo $editItem['id']; ?>">
          <?php endif; ?>

          <div class="row g-3">

            <?php if (!$editItem): ?>
            <div class="col-12">
              <label class="form-label">Image File <span style="color:var(--danger)">*</span></label>
              <div class="drop-zone" id="dropZone" onclick="document.getElementById('imgFile').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p><strong>Click to browse</strong> or drag & drop here</p>
                <p style="font-size:.75rem;margin-top:.25rem;">JPG, PNG, WEBP, GIF &nbsp;·&nbsp; Max 5MB &nbsp;·&nbsp; Saved to assets/images/</p>
                <img id="imagePreview" src="" alt="Preview">
              </div>
              <input type="file" id="imgFile" name="image" accept="image/*" style="display:none;" required>
            </div>
            <?php else: ?>
            <div class="col-12">
              <label class="form-label">Current Image</label><br>
              <img src="../<?php echo htmlspecialchars($editItem['image_path']); ?>"
                   style="height:130px;border-radius:10px;object-fit:cover;border:1px solid rgba(0,0,0,.08);"
                   onerror="this.style.display='none'">
              <p style="font-size:.72rem;color:var(--muted);margin-top:.4rem;">To replace the image, delete this entry and re-upload.</p>
            </div>
            <?php endif; ?>

            <div class="col-md-6">
              <label class="form-label">Title / Caption <span style="color:var(--danger)">*</span></label>
              <input type="text" name="title" class="form-control" required
                     placeholder="e.g. Trade Fair Opening Ceremony"
                     value="<?php echo $editItem ? htmlspecialchars($editItem['title']) : ''; ?>">
            </div>

            <div class="col-md-3">
              <label class="form-label">Category <span style="color:var(--danger)">*</span></label>
              <select name="category" class="form-select" required>
                <?php foreach ($categories as $val => $label): ?>
                <option value="<?php echo $val; ?>" <?php echo ($editItem && $editItem['category']===$val)?'selected':''; ?>>
                  <?php echo $label; ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label">Order</label>
              <input type="number" name="display_order" class="form-control" min="0" placeholder="0"
                     value="<?php echo $editItem ? $editItem['display_order'] : '0'; ?>">
              <span style="font-size:.7rem;color:var(--muted);">Lower = first</span>
            </div>

            <div class="col-md-1">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="active"   <?php echo (!$editItem || $editItem['status']==='active')  ?'selected':''; ?>>On</option>
                <option value="inactive" <?php echo ($editItem && $editItem['status']==='inactive')  ?'selected':''; ?>>Off</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Description (optional)</label>
              <textarea name="description" class="form-control" rows="2"
                        placeholder="Brief note about this photo..."><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
            </div>

            <div class="col-12">
              <button type="submit" class="btn-royal">
                <i class="fas fa-<?php echo $editItem?'save':'upload'; ?>"></i>
                <?php echo $editItem ? 'Save Changes' : 'Upload Image'; ?>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- FILTER TABS -->
    <div class="filter-tabs">
      <a href="gallery_manage.php" class="filter-tab <?php echo !$filterCat?'active':''; ?>">All (<?php echo $totalImages; ?>)</a>
      <?php foreach ($categories as $val => $label):
        $cnt = $pdo->prepare("SELECT COUNT(*) FROM gallery WHERE category=?");
        $cnt->execute([$val]); $c = $cnt->fetchColumn();
        if ($c == 0) continue;
      ?>
      <a href="?cat=<?php echo $val; ?>" class="filter-tab <?php echo $filterCat===$val?'active':''; ?>">
        <?php echo $label; ?> (<?php echo $c; ?>)
      </a>
      <?php endforeach; ?>
    </div>

    <!-- GALLERY GRID -->
    <div class="section-card">
      <div class="section-card-header">
        <h5><i class="fas fa-images"></i> Uploaded Images (<?php echo count($images); ?>)</h5>
        <span style="font-size:.75rem;color:var(--muted);">These appear after the original photos on the public gallery</span>
      </div>

      <?php if (empty($images)): ?>
      <div class="empty-state">
        <i class="fas fa-images"></i>
        <p style="font-weight:600;margin-bottom:.35rem;">No uploads yet</p>
        <p style="font-size:.82rem;">Upload images above — they'll appear on the public gallery page instantly after the original photos.</p>
      </div>
      <?php else: ?>
      <div class="gallery-grid">
        <?php foreach ($images as $img): ?>
        <div class="gallery-card">
          <img src="../<?php echo htmlspecialchars($img['image_path']); ?>"
               alt="<?php echo htmlspecialchars($img['title']); ?>"
               onerror="this.src='https://via.placeholder.com/220x170?text=Missing'">
          <div class="gallery-card-body">
            <div class="cat-badge"><?php echo $categories[$img['category']] ?? $img['category']; ?></div>
            <div class="gallery-card-title"><?php echo htmlspecialchars($img['title']); ?></div>
            <div class="gallery-card-meta">
              Order: <?php echo $img['display_order']; ?> &nbsp;·&nbsp;
              <?php echo date('M d, Y', strtotime($img['upload_date'])); ?>
            </div>
            <div style="margin-bottom:.5rem;">
              <span class="badge-status badge-<?php echo $img['status']; ?>">
                <i class="fas fa-circle" style="font-size:.4rem;"></i>
                <?php echo $img['status']==='active' ? 'Visible' : 'Hidden'; ?>
              </span>
            </div>
            <div class="gallery-card-actions">
              <a href="?edit=<?php echo $img['id']; ?>" class="btn-ghost"><i class="fas fa-edit"></i></a>

              <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="gallery_id" value="<?php echo $img['id']; ?>">
                <input type="hidden" name="new_status" value="<?php echo $img['status']==='active'?'inactive':'active'; ?>">
                <button type="submit" class="btn-gold-sm" title="<?php echo $img['status']==='active'?'Hide':'Show'; ?>">
                  <i class="fas fa-<?php echo $img['status']==='active'?'eye-slash':'eye'; ?>"></i>
                </button>
              </form>

              <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this image permanently?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="gallery_id" value="<?php echo $img['id']; ?>">
                <button type="submit" class="btn-danger-sm"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('imgFile');
const preview  = document.getElementById('imagePreview');

if (dropZone && fileInput) {
  ['dragenter','dragover'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('dragover'); }));
  ['dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('dragover'); }));
  dropZone.addEventListener('drop', e => { fileInput.files = e.dataTransfer.files; showPreview(fileInput.files[0]); });
  fileInput.addEventListener('change', () => showPreview(fileInput.files[0]));
}

function showPreview(file) {
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
  reader.readAsDataURL(file);
}

setTimeout(() => {
  const a = document.querySelector('.alert-banner');
  if (a) { a.style.transition = 'opacity .4s'; a.style.opacity = '0'; setTimeout(() => a.remove(), 420); }
}, 5000);
</script>
</body>
</html>
