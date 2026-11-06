<!-- bagian lutfi firmansyah start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Katalog Master Produk FMCG</h1>
        <p class="page-subtitle">Master data SKU produk PT Riyana Cipta Pangan Indonesia &amp; batas aman buffer persediaan</p>
    </div>
    <div>
        <?php if (Permission::can('produk', 'create')): ?>
            <a href="<?= base_url('?page=produk&action=create') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>+ Tambah Produk Baru</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Container Alert Banner Stok Kritis (Dikelola oleh produk.js) -->
<div id="bannerKritisContainer"></div>

<!-- 4 Kartu KPI Stok Produk -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Total Master SKU</div>
            <div class="kpi-value"><?= (int)$kpi['total_sku'] ?></div>
            <div class="kpi-subtext">Varian produk FMCG aktif</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #DDF3E8; color: #0F6B4B;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Stok Aman</div>
            <div class="kpi-value"><?= (int)$kpi['aman'] ?> <span style="font-size:14px; font-weight:500;">SKU</span></div>
            <div class="kpi-subtext">&gt;120% Buffer Minimum</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #FEF0D0; color: #B45309;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Stok Menipis</div>
            <div class="kpi-value"><?= (int)$kpi['menipis'] ?> <span style="font-size:14px; font-weight:500;">SKU</span></div>
            <div class="kpi-subtext">Mendekati batas buffer</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #FDE2E2; color: #B91C1C;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Stok Kritis</div>
            <div class="kpi-value" style="color: #DC2626;"><?= (int)$kpi['kritis'] ?> <span style="font-size:14px; font-weight:500;">SKU</span></div>
            <div class="kpi-subtext">&lt; Buffer Minimum</div>
        </div>
    </div>
</div>

<!-- Bar Filter & Live Search -->
<div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
    <div class="filter-bar" style="margin-bottom: 0;">
        <div class="search-input-wrap">
            <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="searchProduk" class="form-input" placeholder="Cari kode SKU atau nama produk FMCG...">
        </div>

        <select id="filterKategori" class="form-select" style="width: auto; min-width: 170px;">
            <option value="">Semua Kategori</option>
            <option value="Jelly Cups">Jelly Cups</option>
            <option value="Wafer Roll">Wafer Roll</option>
            <option value="Biscuits & Cookies">Biscuits &amp; Cookies</option>
        </select>

        <select id="filterStatus" class="form-select" style="width: auto; min-width: 150px;">
            <option value="">Semua Status Stok</option>
            <option value="Aman">Stok Aman</option>
            <option value="Menipis">Stok Menipis</option>
            <option value="Kritis">Stok Kritis</option>
        </select>

        <button type="button" id="btnResetFilter" class="btn btn-secondary">Reset</button>
    </div>
</div>

<div style="margin-bottom: 12px; font-size: 13px; color: #64748B;">
    Menampilkan <strong id="displayCountProduk"><?= count($produks) ?></strong> dari <strong><?= count($produks) ?></strong> produk
</div>

<!-- Tabel Katalog Produk -->
<div class="table-responsive">
    <table class="table-custom" id="tableProduk">
        <thead>
            <tr>
                <th>Produk FMCG</th>
                <th>Kode SKU</th>
                <th>Kategori</th>
                <th>Konversi Kemasan</th>
                <th>Stok Fisik Tersedia</th>
                <th>Buffer Minimum</th>
                <th>Status Stok</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produks)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding: 32px;">Belum ada master produk terdaftar.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($produks as $p): ?>
                    <tr data-sku="<?= e($p['kode_sku']) ?>"
                        data-nama="<?= e($p['nama_produk']) ?>"
                        data-kategori="<?= e($p['kategori']) ?>"
                        data-status="<?= e($p['status_stok']) ?>">
                        <td>
                            <div class="product-cell">
                                <?php if (!empty($p['gambar']) && file_exists(__DIR__ . '/../../public/uploads/produk/' . $p['gambar'])): ?>
                                    <img src="<?= base_url('uploads/produk/' . $p['gambar']) ?>" alt="<?= e($p['nama_produk']) ?>" class="product-thumb">
                                <?php else: ?>
                                    <div class="product-thumb-placeholder">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div class="product-name-meta">
                                    <span class="product-name-title"><?= e($p['nama_produk']) ?></span>
                                    <small class="text-muted">Masa Simpan: <?= (int)$p['masa_simpan_bulan'] ?> Bulan</small>
                                </div>
                            </div>
                        </td>
                        <td><code class="code-mono"><?= e($p['kode_sku']) ?></code></td>
                        <td><span class="category-chip"><?= e($p['kategori']) ?></span></td>
                        <td><?= (int)$p['isi_per_karton'] ?> Pcs / Karton</td>
                        <td>
                            <div class="stock-cell">
                                <span class="stock-carton <?= ($p['status_stok'] === 'Kritis') ? 'text-kritis' : '' ?>">
                                    <?= format_number($p['stok_karton']) ?> Karton
                                </span>
                                <span class="stock-pcs"><?= format_number($p['stok_pcs']) ?> Pcs</span>
                            </div>
                        </td>
                        <td><strong><?= format_number($p['buffer_minimum']) ?></strong> Karton</td>
                        <td>
                            <span class="badge badge-<?= $p['badge_color'] ?>"><?= e($p['status_stok']) ?></span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <?php if (Permission::can('produk', 'edit')): ?>
                                <a href="<?= base_url('?page=produk&action=edit&id=' . $p['id']) ?>" class="btn btn-secondary btn-sm" title="Edit Produk">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                            <?php endif; ?>
                            <?php if (Permission::can('produk', 'delete')): ?>
                                <button type="button" class="btn btn-secondary btn-sm" style="color:#DC2626;" title="Hapus Produk"
                                        onclick="confirmDelete('<?= base_url('?page=produk&action=delete') ?>&id=<?= $p['id'] ?>', 'Produk <?= e($p['nama_produk']) ?>')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            <?php endif; ?>
                            <?php if (!Permission::can('produk', 'edit') && !Permission::can('produk', 'delete')): ?>
                                <span class="text-muted" style="font-size:12px; font-style:italic;">Lihat Saja</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- bagian lutfi firmansyah end -->

