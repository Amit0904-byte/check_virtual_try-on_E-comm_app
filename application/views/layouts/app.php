<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= html_escape($title ?? 'Maison AI') ?> · Maison AI</title>
    <?= csrf_meta() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= asset_url('css/app.css') ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg atelier-nav sticky-top"><div class="container-fluid px-lg-5">
    <a class="navbar-brand" href="<?= site_url('try-on') ?>">MAISON <span>AI</span></a>
    <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="mainNav"><div class="navbar-nav mx-auto gap-lg-4">
        <a class="nav-link" href="<?= site_url('products') ?>">Collection</a><a class="nav-link" href="<?= site_url('try-on') ?>">Virtual fitting room</a>
    </div><div class="navbar-nav align-items-lg-center gap-2">
        <?php if (is_admin()): ?><a class="nav-link" href="<?= site_url('admin') ?>">Dashboard</a><?php endif ?>
        <?php if (is_logged_in()): ?><span class="nav-greeting">Hello, <?= html_escape(explode(' ', current_user()['name'])[0]) ?></span><a class="btn btn-ink btn-sm" href="<?= site_url('logout') ?>">Sign out</a>
        <?php else: ?><a class="nav-link" href="<?= site_url('login') ?>">Sign in</a><a class="btn btn-ink btn-sm" href="<?= site_url('register') ?>">Join Maison</a><?php endif ?>
    </div></div>
</div></nav>
<?php if ($this->session->flashdata('success')): ?><div class="container mt-3"><div class="alert atelier-alert alert-success"><?= html_escape($this->session->flashdata('success')) ?></div></div><?php endif ?>
<?php if ($this->session->flashdata('error')): ?><div class="container mt-3"><div class="alert atelier-alert alert-danger"><?= html_escape($this->session->flashdata('error')) ?></div></div><?php endif ?>
<main><?php $this->load->view($content_view); ?></main>
<footer class="footer mt-5"><div class="container d-flex flex-wrap justify-content-between gap-3"><span>MAISON AI — Fashion, fitted by intelligence.</span><span>Private by design · Camera frames stay in your browser</span></div></footer>
<div class="toast-container position-fixed bottom-0 end-0 p-3"><div id="appToast" class="toast" role="alert"><div class="toast-body"></div></div></div>
<script>window.APP={baseUrl:<?= json_encode(base_url()) ?>,siteUrl:<?= json_encode(site_url()) ?>,loggedIn:<?= is_logged_in()?'true':'false' ?>};</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
