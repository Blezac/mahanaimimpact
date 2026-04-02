<?php
// === gallery.php ===
$pageTitle = "Gallery";
require_once 'includes/header.php';
require_once 'config/db.php';

// Fetch all active DB-uploaded images
$dbImages = $pdo->query("SELECT * FROM gallery WHERE status='active' ORDER BY display_order ASC, upload_date DESC")->fetchAll();

// Group DB images by category
$dbGrouped = [];
foreach ($dbImages as $img) {
    $dbGrouped[$img['category']][] = $img;
}

$categories = [
    'trade_fair' => ['label' => 'University Trade Fairs',         'desc' => 'Empowering young entrepreneurs with marketplace opportunities'],
    'outreach'   => ['label' => 'Orphanage & Elderly Outreaches', 'desc' => 'Humanitarian support reaching vulnerable communities'],
    'mentorship' => ['label' => 'Mentorship Programs',            'desc' => 'Guiding the next generation of leaders'],
    'academy'    => ['label' => 'Academy & Training',             'desc' => 'Building skills and knowledge for tomorrow'],
    'community'  => ['label' => 'Community Impact',               'desc' => 'Transforming lives across communities'],
];

// Hardcoded originals — these always show first, client uploads appear below them
$hardcoded = [
    'trade_fair' => [
        ['src' => 'assets/images/1.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/2.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/3.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/4.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/5.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/6.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/7.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/8.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/9.jpeg',  'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/10.jpeg', 'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/11.jpeg', 'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/12.jpeg', 'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/13.jpeg', 'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/14.jpeg', 'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/15.jpeg', 'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/16.jpeg', 'title' => 'Trade Fair', 'desc' => ''],
        ['src' => 'assets/images/17.jpeg', 'title' => 'Trade Fair', 'desc' => ''],
    ],
    'outreach' => [
        ['src' => 'assets/images/18.jpeg', 'title' => 'Orphanage Outreach', 'desc' => ''],
        ['src' => 'assets/images/19.jpeg', 'title' => 'Orphanage Outreach', 'desc' => ''],
        ['src' => 'assets/images/20.jpeg', 'title' => 'Orphanage Outreach', 'desc' => ''],
        ['src' => 'assets/images/21.jpeg', 'title' => 'Orphanage Outreach', 'desc' => ''],
        ['src' => 'assets/images/22.jpeg', 'title' => 'Orphanage Outreach', 'desc' => ''],
        ['src' => 'assets/images/23.jpeg', 'title' => 'Orphanage Outreach', 'desc' => ''],
        ['src' => 'assets/images/24.jpeg', 'title' => 'Orphanage Outreach', 'desc' => ''],
    ],
    'mentorship' => [
        ['src' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&h=400&fit=crop', 'title' => 'One-on-One Sessions', 'desc' => 'Personalized mentorship and guidance'],
        ['src' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',    'title' => 'Group Mentorship',    'desc' => 'Collaborative learning experiences'],
        ['src' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&h=400&fit=crop', 'title' => 'Career Development',  'desc' => 'Professional growth and guidance'],
    ],
];
?>

<!-- Hero -->
<section style="background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%); color: white; padding: 5rem 0;">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Impact <span style="color: #D4AF37;">Gallery</span></h1>
        <p class="lead" style="font-size: 1.4rem;">Moments of transformation: Trade fairs, humanitarian outreaches, and community impact</p>
    </div>
</section>

<!-- Gallery Body -->
<section style="padding: 4rem 0;">
    <div class="container">

        <?php
        // All category keys that have any content
        $allCatKeys   = array_unique(array_merge(array_keys($hardcoded), array_keys($dbGrouped)));
        $orderedCats  = array_filter(array_keys($categories), fn($k) => in_array($k, $allCatKeys));
        ?>

        <?php foreach ($orderedCats as $catKey):
            $hcImages = $hardcoded[$catKey]  ?? [];
            $upImages = $dbGrouped[$catKey]  ?? [];
            if (empty($hcImages) && empty($upImages)) continue;

            // Merge: hardcoded first, then DB uploads
            $allInCat = [];
            foreach ($hcImages as $h) {
                $allInCat[] = ['src' => $h['src'], 'title' => $h['title'], 'desc' => $h['desc']];
            }
            foreach ($upImages as $u) {
                $allInCat[] = ['src' => $u['image_path'], 'title' => $u['title'], 'desc' => $u['description'] ?? ''];
            }

            $catInfo = $categories[$catKey];
        ?>
        <div class="gallery-section mb-5" id="cat-<?php echo $catKey; ?>">
            <div style="margin-bottom: 2rem;">
                <h2 style="font-family:'Sora',sans-serif;font-size:1.8rem;font-weight:800;color:#4B0082;margin-bottom:.5rem;">
                    <?php echo htmlspecialchars($catInfo['label']); ?>
                </h2>
                <p style="color:#6c757d;font-size:1rem;margin:0;"><?php echo htmlspecialchars($catInfo['desc']); ?></p>
                <div style="width:60px;height:3px;background:#D4AF37;border-radius:2px;margin-top:.75rem;"></div>
            </div>

            <div class="row g-4">
                <?php foreach ($allInCat as $idx => $img): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="gallery-item"
                         data-group="<?php echo $catKey; ?>"
                         data-index="<?php echo $idx; ?>"
                         data-src="<?php echo htmlspecialchars($img['src']); ?>"
                         data-title="<?php echo htmlspecialchars($img['title']); ?>"
                         data-desc="<?php echo htmlspecialchars($img['desc']); ?>">
                        <div class="gallery-thumb">
                            <img src="<?php echo htmlspecialchars($img['src']); ?>"
                                 alt="<?php echo htmlspecialchars($img['title']); ?>"
                                 loading="lazy"
                                 onerror="this.closest('.col-md-4').style.display='none'">
                            <div class="gallery-overlay">
                                <div class="overlay-inner">
                                    <i class="fas fa-expand"></i>
                                    <span><?php echo htmlspecialchars($img['title']); ?></span>
                                    <?php if ($img['desc']): ?>
                                    <small><?php echo htmlspecialchars($img['desc']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($upImages)): ?>
            <p style="text-align:right;font-size:.72rem;color:rgba(75,0,130,.35);margin-top:.5rem;">
                <?php echo count($hcImages); ?> original &nbsp;+&nbsp; <?php echo count($upImages); ?> newly added
            </p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <!-- Pure DB categories (academy, community, etc. with no hardcoded images) -->
        <?php foreach ($categories as $catKey => $catInfo):
            if (in_array($catKey, array_keys($hardcoded))) continue;
            if (empty($dbGrouped[$catKey])) continue;
            $upImages  = $dbGrouped[$catKey];
            $allInCat  = array_map(fn($u) => ['src' => $u['image_path'], 'title' => $u['title'], 'desc' => $u['description'] ?? ''], $upImages);
        ?>
        <div class="gallery-section mb-5" id="cat-<?php echo $catKey; ?>">
            <div style="margin-bottom: 2rem;">
                <h2 style="font-family:'Sora',sans-serif;font-size:1.8rem;font-weight:800;color:#4B0082;margin-bottom:.5rem;">
                    <?php echo htmlspecialchars($catInfo['label']); ?>
                </h2>
                <p style="color:#6c757d;font-size:1rem;margin:0;"><?php echo htmlspecialchars($catInfo['desc']); ?></p>
                <div style="width:60px;height:3px;background:#D4AF37;border-radius:2px;margin-top:.75rem;"></div>
            </div>
            <div class="row g-4">
                <?php foreach ($allInCat as $idx => $img): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="gallery-item"
                         data-group="<?php echo $catKey; ?>"
                         data-index="<?php echo $idx; ?>"
                         data-src="<?php echo htmlspecialchars($img['src']); ?>"
                         data-title="<?php echo htmlspecialchars($img['title']); ?>"
                         data-desc="<?php echo htmlspecialchars($img['desc']); ?>">
                        <div class="gallery-thumb">
                            <img src="<?php echo htmlspecialchars($img['src']); ?>"
                                 alt="<?php echo htmlspecialchars($img['title']); ?>"
                                 loading="lazy"
                                 onerror="this.closest('.col-md-4').style.display='none'">
                            <div class="gallery-overlay">
                                <div class="overlay-inner">
                                    <i class="fas fa-expand"></i>
                                    <span><?php echo htmlspecialchars($img['title']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>

<!-- LIGHTBOX -->
<div id="lightbox">
    <button id="lbClose"><i class="fas fa-times"></i></button>
    <button id="lbPrev"><i class="fas fa-chevron-left"></i></button>
    <button id="lbNext"><i class="fas fa-chevron-right"></i></button>
    <img id="lbImg" src="" alt="">
    <div id="lbCaption">
        <div id="lbTitle"></div>
        <div id="lbDesc"></div>
        <div id="lbCounter"></div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@700;800&display=swap');

.gallery-thumb {
    position: relative; overflow: hidden;
    border-radius: 12px; cursor: pointer;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    height: 300px;
}
.gallery-thumb img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .4s ease; display: block;
}
.gallery-thumb:hover img { transform: scale(1.08); }

.gallery-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(75,0,130,.88), rgba(212,175,55,.6));
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .3s ease;
}
.gallery-thumb:hover .gallery-overlay { opacity: 1; }

.overlay-inner {
    text-align: center; color: #fff; padding: 1rem;
    display: flex; flex-direction: column; align-items: center; gap: .35rem;
}
.overlay-inner i    { font-size: 1.5rem; }
.overlay-inner span { font-weight: 700; font-size: .9rem; }
.overlay-inner small{ font-size: .75rem; opacity: .85; }

/* Lightbox */
#lightbox {
    display: none; position: fixed; inset: 0;
    background: rgba(10,0,20,.95); z-index: 9999;
    align-items: center; justify-content: center; flex-direction: column;
}
#lightbox.open { display: flex; }

#lbClose {
    position: absolute; top: 1.25rem; right: 1.5rem;
    background: none; border: none; color: #fff;
    font-size: 1.75rem; cursor: pointer; opacity: .7;
    transition: opacity .2s;
}
#lbClose:hover { opacity: 1; }

#lbPrev, #lbNext {
    position: absolute; top: 50%; transform: translateY(-50%);
    background: rgba(255,255,255,.1); border: none; color: #fff;
    width: 48px; height: 48px; border-radius: 50%;
    cursor: pointer; font-size: 1.1rem; transition: background .2s;
}
#lbPrev { left: 1.25rem; }
#lbNext { right: 1.25rem; }
#lbPrev:hover, #lbNext:hover { background: rgba(255,255,255,.25); }

#lbImg {
    max-width: 90vw; max-height: 80vh;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
    object-fit: contain;
}
#lbCaption { margin-top: 1rem; text-align: center; color: #fff; max-width: 600px; padding: 0 1rem; }
#lbTitle   { font-weight: 700; font-size: 1rem; margin-bottom: .25rem; }
#lbDesc    { font-size: .82rem; opacity: .7; }
#lbCounter { font-size: .72rem; opacity: .5; margin-top: .5rem; }
</style>

<script>
const lightbox  = document.getElementById('lightbox');
const lbImg     = document.getElementById('lbImg');
const lbTitle   = document.getElementById('lbTitle');
const lbDesc    = document.getElementById('lbDesc');
const lbCounter = document.getElementById('lbCounter');

let currentGroup = [];
let currentIdx   = 0;

document.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('click', () => {
        const groupKey = item.dataset.group;
        currentGroup   = [...document.querySelectorAll(`.gallery-item[data-group="${groupKey}"]`)];
        currentIdx     = parseInt(item.dataset.index);
        openLightbox();
    });
});

function openLightbox() {
    const item = currentGroup[currentIdx];
    lbImg.src             = item.dataset.src;
    lbTitle.textContent   = item.dataset.title;
    lbDesc.textContent    = item.dataset.desc;
    lbCounter.textContent = `${currentIdx + 1} / ${currentGroup.length}`;
    lightbox.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    lightbox.classList.remove('open');
    document.body.style.overflow = '';
    lbImg.src = '';
}

document.getElementById('lbClose').addEventListener('click', closeLightbox);
lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });
document.getElementById('lbPrev').addEventListener('click', () => {
    currentIdx = (currentIdx - 1 + currentGroup.length) % currentGroup.length;
    openLightbox();
});
document.getElementById('lbNext').addEventListener('click', () => {
    currentIdx = (currentIdx + 1) % currentGroup.length;
    openLightbox();
});
document.addEventListener('keydown', e => {
    if (!lightbox.classList.contains('open')) return;
    if (e.key === 'Escape')      closeLightbox();
    if (e.key === 'ArrowLeft')  { currentIdx = (currentIdx - 1 + currentGroup.length) % currentGroup.length; openLightbox(); }
    if (e.key === 'ArrowRight') { currentIdx = (currentIdx + 1) % currentGroup.length; openLightbox(); }
});
</script>

<?php require_once 'includes/footer.php'; ?>
