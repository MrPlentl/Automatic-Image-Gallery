<?php

/**
 * Scans the /galleries root and returns a list of galleries with a random
 * representative thumbnail and total image count for each.
 *
 * Gallery structure:
 *   /galleries/{GALLERY_NAME}/{topic}/{tag}/{filename}
 *   /galleries/{GALLERY_NAME}/{topic}/{tag}/full/{filename}
 */
function scanAllGalleries(string $imagesDir): array
{
    $galleries       = [];
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

    if (!is_dir($imagesDir)) {
        return $galleries;
    }

    $galleryPaths = glob($imagesDir . '/*', GLOB_ONLYDIR);
    if (!$galleryPaths) {
        return $galleries;
    }

    sort($galleryPaths);

    foreach ($galleryPaths as $galleryPath) {
        $galleryName = basename($galleryPath);

        // Ignore the __HERO__ directory
        if ($galleryName === '__HERO__') {
            continue;
        }

        // Collect all thumbnail images inside this gallery (exclude /full/ dirs)
        $allImages = [];
        collectImages($galleryPath, $imageExtensions, $imagesDir, $allImages);

        if (empty($allImages)) {
            // Still show the gallery tile even if empty
            $galleries[] = [
                'name'        => $galleryName,
                'randomImage' => null,
                'imageCount'  => 0,
            ];
            continue;
        }

        // Pick a random image for the tile
        $randomImage = $allImages[array_rand($allImages)];

        $galleries[] = [
            'name'        => $galleryName,
            'randomImage' => $randomImage,
            'imageCount'  => count($allImages),
        ];
    }

    return $galleries;
}

/**
 * Recursively collect all image file paths inside a gallery directory,
 * skipping any directory named "full".
 * Returns relative paths from $imagesDir root.
 */
function collectImages(string $dir, array $extensions, string $imagesDir, array &$results): void
{
    // Skip /full/ directories — those are full-res versions, not thumbnails
    if (strtolower(basename($dir)) === 'full') {
        return;
    }

    foreach ($extensions as $ext) {
        $files = glob($dir . '/*.' . $ext) ?: [];
        $files = array_merge($files, glob($dir . '/*.' . strtoupper($ext)) ?: []);
        foreach ($files as $file) {
            // Store as a path relative to the project root
            $results[] = ltrim(str_replace($imagesDir, 'galleries', $file), '/');
        }
    }

    // Recurse into subdirectories
    $subdirs = glob($dir . '/*', GLOB_ONLYDIR) ?: [];
    foreach ($subdirs as $subdir) {
        collectImages($subdir, $extensions, $imagesDir, $results);
    }
}

/**
 * Scans a single gallery directory and builds a topic/tag/image map.
 *
 * $galleryDir = /path/to/galleries/{GALLERY_NAME}
 * $galleryName is used to build relative image paths.
 */
function scanGalleryImages(string $galleryDir, string $galleryName): array
{
    $result = ['topics' => []];

    if (!is_dir($galleryDir)) {
        return $result;
    }

    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

    // Each direct child directory of the gallery is a Topic
    $topicPaths = glob($galleryDir . '/*', GLOB_ONLYDIR);
    if (!$topicPaths) {
        return $result;
    }

    sort($topicPaths);

    foreach ($topicPaths as $topicPath) {
        $topicName = basename($topicPath);
        $topicData = [
            'name' => $topicName,
            'tags' => [],
        ];

        $tagPaths = glob($topicPath . '/*', GLOB_ONLYDIR);
        if (!$tagPaths) {
            $result['topics'][] = $topicData;
            continue;
        }

        foreach ($tagPaths as $tagPath) {
            $tagName = basename($tagPath);

            if (strtolower($tagName) === 'full') {
                continue;
            }

            $tagImages = [];

            foreach ($imageExtensions as $ext) {
                $files = glob($tagPath . '/*.' . $ext) ?: [];
                $files = array_merge($files, glob($tagPath . '/*.' . strtoupper($ext)) ?: []);

                foreach ($files as $filePath) {
                    $filename      = basename($filePath);
                    $fullImagePath = $tagPath . DIRECTORY_SEPARATOR . 'full' . DIRECTORY_SEPARATOR . $filename;

                    $created = @filemtime($filePath) ?: null;

                    $relativeThumbnail = 'galleries/' . $galleryName . '/' . $topicName . '/' . $tagName . '/' . $filename;
                    
                    // Fallback to thumbnail if full version doesn't exist
                    $relativeFull = file_exists($fullImagePath)
                        ? 'galleries/' . $galleryName . '/' . $topicName . '/' . $tagName . '/full/' . $filename
                        : $relativeThumbnail;

                    $tagImages[] = [
                        'filename'  => $filename,
                        'thumbnail' => $relativeThumbnail,
                        'full'      => $relativeFull,
                        'topic'     => $topicName,
                        'tag'       => $tagName,
                        'created'   => $created,
                    ];
                }
            }

            usort($tagImages, function ($a, $b) {
                return ($b['created'] ?? 0) <=> ($a['created'] ?? 0);
            });

            $topicData['tags'][] = [
                'name'   => $tagName,
                'images' => $tagImages,
            ];
        }

        $result['topics'][] = $topicData;
    }

    return $result;
}
