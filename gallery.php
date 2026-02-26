<?php
require_once __DIR__ . '/utils/scan.php';

// Validate and sanitise the gallery name from the query string
$galleryName = isset($_GET['g']) ? trim($_GET['g']) : '';

// Basic security: strip any path traversal attempts
$galleryName = basename($galleryName);

$imagesDir  = __DIR__ . '/galleries';
$galleryDir = $imagesDir . '/' . $galleryName;

// If the gallery doesn't exist, redirect home
if ($galleryName === '' || !is_dir($galleryDir)) {
    header('Location: index.php');
    exit;
}

$galleryData = scanGalleryImages($galleryDir, $galleryName);
$galleryJson = json_encode(
    $galleryData,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);

$pageTitle = htmlspecialchars($galleryName);
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once './sections/head.php'; ?>
<body class="gallery-body">

    <?php require_once './sections/nav.php'; ?>

    <!-- Gallery-specific hero: reuse header section but pass gallery name -->
    <?php require_once './sections/header.php'; ?>

    <?php require_once './sections/control-bar.php'; ?>
    <?php require_once './sections/gallery-main.php'; ?>
    <?php require_once './sections/lightbox-modal.php'; ?>
    <?php require_once './sections/footer.php'; ?>

    <!-- Inject gallery data for TypeScript -->
    <script>
        window.__GALLERY_DATA__ = <?= $galleryJson ?>;
    </script>
    <script src="dist/main.js"></script>
</body>
</html>
