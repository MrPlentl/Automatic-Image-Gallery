<?php
require_once __DIR__ . '/utils/scan.php';

$imagesDir = __DIR__ . '/galleries';
$galleries = scanAllGalleries($imagesDir);
$pageTitle = 'Home';
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once './sections/head.php'; ?>
<body class="gallery-body">

    <?php require_once './sections/nav.php'; ?>
    <?php require_once './sections/header.php'; ?>

    <!-- ══════════════════════════════════════
         GALLERY TILES
    ══════════════════════════════════════ -->
    <main class="home-main">

        <?php if (empty($galleries)): ?>
            <div class="empty-state">
                <p class="empty-text">No galleries found. Add folders inside the <code>/galleries</code> directory.</p>
            </div>
        <?php else: ?>
            <div class="gallery-tiles" id="gallery-tiles">
                <?php foreach ($galleries as $gallery): ?>
                    <a
                        href="gallery.php?g=<?= urlencode($gallery['name']) ?>"
                        class="gallery-tile"
                        aria-label="Open <?= htmlspecialchars(str_replace('-', ' ', $gallery['name'])) ?> gallery"
                    >
                        <?php if ($gallery['randomImage']): ?>
                            <img
                                src="<?= htmlspecialchars($gallery['randomImage']) ?>"
                                alt="<?= htmlspecialchars(str_replace('-', ' ', $gallery['name'])) ?>"
                                class="gallery-tile__img"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="gallery-tile__placeholder"></div>
                        <?php endif; ?>

                        <div class="gallery-tile__overlay">
                            <div class="gallery-tile__info">
                                <h2 class="gallery-tile__name"><?= htmlspecialchars(str_replace('-', ' ', $gallery['name'])) ?></h2>
                                <span class="gallery-tile__count">
                                    <?= $gallery['imageCount'] ?> image<?= $gallery['imageCount'] !== 1 ? 's' : '' ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <?php require_once './sections/footer.php'; ?>

</body>
</html>
