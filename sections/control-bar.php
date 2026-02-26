<!-- ══════════════════════════════════════
      CONTROLS AREA
══════════════════════════════════════ -->
<div class="controls-bar" id="controls-bar">
    <div class="controls-inner">
        <!-- Topic Dropdown -->
        <div class="topic-select-wrap">
            <label class="select-label" for="topic-select">Collection</label>
            <div class="select-wrapper">
                <select id="topic-select" class="topic-select">
                    <option value="ALL">ALL</option>
                    <?php foreach ($galleryData['topics'] as $topic): ?>
                        <option value="<?= htmlspecialchars($topic['name']) ?>">
                            <?= htmlspecialchars(str_replace('-', ' ', $topic['name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="select-arrow">▾</span>
            </div>
        </div>

        <!-- Image count badge -->
        <div class="image-count" id="image-count">
            <span id="count-text">— images</span>
        </div>
    </div>

    <!-- Tag Filters -->
    <div class="tag-row" id="tag-row">
        <div class="tag-filters" id="tag-filters">
            <!-- Populated by JS -->
        </div>
    </div>
</div>