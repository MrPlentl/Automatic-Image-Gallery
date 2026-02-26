<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'About';
require_once './sections/head.php';
?>
<body class="gallery-body about-page">

    <!-- NAVIGATION -->
    <nav class="site-nav" id="site-nav">
        <div class="nav-inner">
            <a href="/" class="nav-logo">GALLERY</a>
            <div class="nav-links">
                <a href="/about" class="nav-link nav-link--active">About</a>
            </div>
        </div>
    </nav>

    <!-- ABOUT CONTENT -->
    <main class="about-main">
        <div class="about-container">
            <div class="about-header">
                <h1 class="about-title">About the Gallery</h1>
                <div class="about-divider"></div>
            </div>

            <div class="about-body">
                <p class="about-lead">
                    A personal archive of images, organized by topic and tag.
                    This gallery is built to make browsing and discovering photographs effortless.
                </p>

                <h2 class="about-heading">How It Works</h2>
                <p class="about-text">
                    Images are organized in a simple directory structure. Each top-level folder inside
                    <code class="about-code">/galleries</code> represents a <em>topic</em> — a broad category
                    such as a place, theme, or subject. Inside each topic, sub-folders represent <em>tags</em>
                    that further classify the images within.
                </p>
                <p class="about-text">
                    To set the hero images on the home page, place any image files inside the special
                    <code class="about-code">/galleries/__HERO__/</code> directory. A random image from this folder
                    will be chosen as the banner each time the home page is loaded.
                </p>
                <p class="about-text">
                    Full-resolution versions of each image are stored in a <code class="about-code">full/</code>
                    sub-directory within each tag folder. When you click an image in the gallery, the full
                    resolution version is displayed in the lightbox.
                </p>

                <h2 class="about-heading">Directory Structure</h2>
                <pre class="about-pre"><code>/galleries
  /__HERO__/
    hero-image.jpg
  /{topic}/
    /{tag}/
      image.jpg          ← thumbnail
      /full/
        image.jpg        ← full-size</code></pre>

                <h2 class="about-heading">Credits</h2>
                <p class="about-text">
                    This gallery was built and is maintained by <strong>Brandon Plentl aka ThaBamboozler</strong>.
                    Built with PHP, TypeScript, and Tailwind CSS v4.
                </p>
            </div>

            <div class="about-back">
                <a href="/" class="back-link">← Back to Gallery</a>
            </div>
        </div>
    </main>
    
    <?php require_once './sections/footer.php'; ?>

</body>
</html>
