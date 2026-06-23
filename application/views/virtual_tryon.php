<section class="tryon-hero"><div class="container-fluid px-lg-5"><div class="row align-items-end"><div class="col-lg-8"><span class="eyebrow">AI FITTING ROOM · BETA</span><h1>Your reflection,<br><em>reimagined.</em></h1></div><div class="col-lg-4"><p>Explore silhouettes in real time. Your camera feed is processed locally and never uploaded unless you choose to save a look.</p></div></div></div></section>
<section class="container-fluid px-lg-5 pb-5"><div class="tryon-shell">
    <aside class="product-rail"><div class="rail-title"><div><span class="eyebrow">CURATED FOR YOU</span><h2>Select a look</h2></div><a href="<?= site_url('products') ?>">View all</a></div>
        <div class="product-scroll" id="productList">
        <?php foreach ($products as $i => $product): ?><button class="dress-card <?= $i===0?'active':'' ?>" type="button" data-product='<?= html_escape(json_encode($product), TRUE) ?>'>
            <span class="dress-thumb"><img src="<?= product_image_url($product['product_image']) ?>" alt="<?= html_escape($product['name']) ?>"></span>
            <span class="dress-meta"><small><?= html_escape($product['category']) ?></small><strong><?= html_escape($product['name']) ?></strong><span><?= money($product['price']) ?></span></span><i class="bi bi-arrow-right"></i>
        </button><?php endforeach ?>
        <?php if (!$products): ?><div class="empty-state">No looks yet. An administrator can add the first product.</div><?php endif ?>
        </div>
        <div class="size-card"><i class="bi bi-stars"></i><div><strong>Find your Maison size</strong><small>Measurement-based fit guidance</small></div><button data-bs-toggle="modal" data-bs-target="#sizeModal">Calculate</button></div>
    </aside>
    <div class="studio-panel">
        <div class="studio-top"><div><span class="live-dot"></span><span id="cameraStatus">Camera is off</span></div><div class="studio-tools"><button id="toggleLandmarks" class="icon-btn active" title="Toggle landmarks"><i class="bi bi-person-bounding-box"></i></button><button id="fullscreenBtn" class="icon-btn" title="Fullscreen"><i class="bi bi-arrows-fullscreen"></i></button></div></div>
        <div class="camera-stage" id="cameraStage">
            <video id="cameraVideo" playsinline muted></video><canvas id="tryonCanvas"></canvas>
            <div class="camera-empty" id="cameraEmpty"><div class="camera-orbit"><i class="bi bi-camera-video"></i></div><h3>Step into the fitting room</h3><p>Stand 2–3 metres away with your shoulders and hips visible.</p><button class="btn btn-light btn-lg" id="startCamera"><i class="bi bi-camera-video me-2"></i>Start camera</button></div>
            <div class="camera-loading d-none" id="cameraLoading"><span class="loader"></span><p>Preparing your private fitting room…</p></div>
            <div class="stage-badge"><i class="bi bi-shield-check"></i> On-device pose detection</div>
        </div>
        <div class="studio-actions">
            <button class="btn btn-ink" id="captureBtn" disabled><i class="bi bi-camera me-2"></i>Capture</button>
            <button class="btn btn-outline-ink" id="saveBtn" disabled><i class="bi bi-cloud-arrow-up me-2"></i>Save look</button>
            <button class="btn btn-outline-ink" id="downloadBtn" disabled><i class="bi bi-download me-2"></i>Download</button>
            <button class="btn btn-quiet" id="shareBtn" disabled><i class="bi bi-share me-2"></i>Share</button>
            <button class="btn btn-quiet" id="changeDressBtn"><i class="bi bi-arrow-repeat me-2"></i>Change dress</button>
            <button class="btn btn-quiet" id="resetBtn"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset</button>
            <button class="btn btn-danger-soft ms-lg-auto" id="stopCamera" disabled><i class="bi bi-stop-circle me-2"></i>Stop</button>
        </div>
    </div>
    <aside class="look-panel"><span class="eyebrow">CURRENT LOOK</span><div class="look-image"><img id="selectedPreview" src="<?= $selected ? product_image_url($selected['product_image']) : asset_url('images/dress-placeholder.svg') ?>" alt="Selected dress"></div><small id="selectedCategory"><?= $selected ? html_escape($selected['category']) : 'COLLECTION' ?></small><h2 id="selectedName"><?= $selected ? html_escape($selected['name']) : 'Select a dress' ?></h2><div class="look-price" id="selectedPrice"><?= $selected ? money($selected['price']) : '' ?></div><p id="selectedDescription"><?= $selected ? html_escape($selected['description']) : 'Choose a look to begin.' ?></p><div class="fit-note"><i class="bi bi-info-circle"></i><span>The overlay follows shoulder and hip points. Turn slowly for the most natural result.</span></div></aside>
    </div></section>

<div class="modal fade" id="sizeModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form class="modal-content atelier-modal" id="sizeForm"><div class="modal-header"><div><span class="eyebrow">FIT INTELLIGENCE</span><h2 class="modal-title">Your size estimate</h2></div><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3"><div class="col-4"><label>Height (cm)</label><input class="form-control" name="height" type="number" min="120" max="220" required></div><div class="col-4"><label>Chest (cm)</label><input class="form-control" name="chest" type="number" min="60" max="150" required></div><div class="col-4"><label>Waist (cm)</label><input class="form-control" name="waist" type="number" min="45" max="150" required></div></div><div id="sizeResult" class="size-result d-none"></div></div><div class="modal-footer"><button class="btn btn-ink w-100">Recommend my size</button></div></form></div></div>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/pose/pose.js"></script><script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script><script src="<?= asset_url('js/virtual-tryon.js') ?>"></script>
