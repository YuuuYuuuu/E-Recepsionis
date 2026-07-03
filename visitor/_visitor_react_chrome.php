<?php
/**
 * Animated wallpaper + Virtual Resepsionis (same bundle as index landing).
 * Place before idle-redirect.js; Bootstrap bundle must load first.
 */
$landing_js = 'assets/landing/assets/visitor-landing.js';
$landing_asset_ver = max(
    (int) @filemtime(__DIR__ . '/' . $landing_js),
    (int) @filemtime(__DIR__ . '/assets/landing/assets/visitor-landing.css'),
    time()
);
?>
<div id="visitor-chrome-root"></div>
<script type="module" src="<?= htmlspecialchars($landing_js) ?>?v=<?= (int) $landing_asset_ver ?>"></script>
