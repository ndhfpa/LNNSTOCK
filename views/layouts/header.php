<!-- bagian bersama start -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - LNNSTOCK' : 'LNNSTOCK - Warehouse Management System' ?></title>
    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- CSS Utama & Modul Anggota -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/modul-depo.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/modul-produk.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/modul-mutasi.css') ?>">
</head>
<body>
    <div class="app-layout">
        <!-- Overlay untuk drawer mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
<!-- bagian bersama end -->

