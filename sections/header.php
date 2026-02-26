<?php
$heroFiles = glob(__DIR__ . '/../galleries/__HERO__/*');
$heroFiles = $heroFiles ? array_filter($heroFiles, 'is_file') : [];

$heroImage = !empty($heroFiles)
    ? 'galleries/__HERO__/' . basename($heroFiles[array_rand($heroFiles)])
    : 'galleries/__HERO__/hero.jpg';

    echo '<!-- Debug: Hero files found: ' . $heroImage . ' -->';

if (isset($galleryName) && $galleryName !== '') {
    $galleryHero = 'galleries/' . $galleryName . '/hero.jpg';
    if (file_exists(__DIR__ . '/../' . $galleryHero)) {
        $heroImage = $galleryHero;
    }
}
?>
<!-- ══════════════════════════════════════
      HERO
══════════════════════════════════════ -->
<header class="hero-section">
    <img
        src="<?= htmlspecialchars($heroImage) ?>"
        alt="Gallery Hero"
        class="hero-image"
        onerror="this.style.display='none'; this.parentElement.classList.add('hero-fallback')"
    >
    <div class="hero-overlay">
        <div class="hero-content">
            <?php if (isset($galleryName) && $galleryName !== ''): ?>
                <div class="hero-breadcrumb">
                    <a href="index.php" class="hero-back-link">← All Galleries</a>
                </div>
                <h1 class="hero-title"><?= htmlspecialchars(str_replace('-', ' ', $galleryName)) ?></h1>
            <?php else: ?>
                <h1 class="hero-title">My Galleries</h1>
            <?php endif; ?>
            <p class="hero-subtitle">A curated collection</p>
        </div>
    </div>
</header>
