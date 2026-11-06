<!-- bagian nurul khoyimah start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Monitoring Kedaluwarsa FMCG</h1>
        <p class="page-subtitle">Sistem kendali masa simpan FEFO (First Expired First Out) &amp; FIFO produk makanan/minuman</p>
    </div>
    <div>
        <?php if (Permission::can('kedaluwarsa', 'create')): ?>
            <a href="<?= base_url('?page=kedaluwarsa&action=create') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>+ Tambah Batch</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Prinsip Penyaluran: FEFO & FIFO Banner -->
<div class="principle-banner">
    <div>
        <div class="principle-title">Prinsip Mutlak Penyaluran FMCG: FEFO &amp; FIFO Terintegrasi</div>
        <div class="principle-desc">
            Sistem memprioritaskan pengeluaran stok dengan tanggal kedaluwarsa paling awal (FEFO) guna menghindari penyusutan dan kerugian akibat barang basi. Tanggal masuk (FIFO) menjadi parameter sekunder untuk menjaga kesegaran perputaran di rak.
        </div>
    </div>
    <div style="font-size: 28px; opacity: 0.8; padding-left: 20px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
    </div>
</div>

<!-- Container Alert Banner FEFO (Dikelola oleh kedaluwarsa.js) -->
<div id="alertKedaluwarsaContainer"></div>

<!-- Banner Batch Paling Urgent dengan Tombol Alokasikan ke Mutasi -->
<?php if (!empty($urgentBatch)): ?>
    <div class="urgent-batch-banner">
        <div class="urgent-meta">
            <div class="urgent-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div>
                <div class="urgent-text-title">
                    Tindakan Cepat Diperlukan: Batch Urgent Mendekati Kedaluwarsa
                </div>
                <div class="urgent-text-desc">
                    <strong><?= e($urgentBatch['nama_produk']) ?></strong> (Batch: <code><?= e($urgentBatch['no_batch']) ?></code>) di <strong><?= e($urgentBatch['nama_depo']) ?></strong>
                    tersisa <strong><?= (int)$urgentBatch['sisa_hari'] < 0 ? 'Sudah Kedaluwarsa' : (int)$urgentBatch['sisa_hari'] . ' hari lagi' ?></strong> (Total: <?= format_number($urgentBatch['jumlah']) ?> CTN).
                </div>
            </div>
        </div>
        <div>
            <?php if (Permission::can('mutasi', 'create')): ?>
                <a href="<?= base_url('?page=mutasi&action=create&batch_id=' . $urgentBatch['id']) ?>" class="btn btn-danger btn-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m16 3 4 4-4 4M20 7H4M8 21l-4-4 4-4M4 17h16"></path>
                    </svg>
                    <span>Alokasikan ke Mutasi</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- 3 Kartu Status Kedaluwarsa -->
<div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #DDF3E8; color: #0F6B4B;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Aman (&gt; 90 Hari)</div>
            <div class="kpi-value"><?= (int)$kpi['batch_aman'] ?> <span style="font-size:14px; font-weight:500;">Batch</span></div>
            <div class="kpi-subtext"><?= format_number($kpi['stok_aman']) ?> CTN (<?= $kpi['pct_aman'] ?>% persediaan)</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #FEF0D0; color: #B45309;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Waspada (31 - 90 Hari)</div>
            <div class="kpi-value"><?= (int)$kpi['batch_waspada'] ?> <span style="font-size:14px; font-weight:500;">Batch</span></div>
            <div class="kpi-subtext"><?= format_number($kpi['stok_waspada']) ?> CTN (<?= $kpi['pct_waspada'] ?>% persediaan)</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #FDE2E2; color: #B91C1C;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Kritis (&le; 30 Hari / Lewat)</div>
            <div class="kpi-value" style="color: #DC2626;"><?= (int)$kpi['batch_kritis'] ?> <span style="font-size:14px; font-weight:500;">Batch</span></div>
            <div class="kpi-subtext"><?= format_number($kpi['stok_kritis']) ?> CTN (<?= $kpi['pct_kritis'] ?>% persediaan)</div>
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
            <input type="text" id="searchBatch" class="form-input" placeholder="Cari nomor batch atau nama produk...">
        </div>

        <select id="filterDepo" class="form-select" style="width: auto; min-width: 180px;">
            <option value="">Semua Lokasi Depo</option>
            <?php foreach ($depos as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($depoFilter == $d['id']) ? 'selected' : '' ?>>
                    <?= e($d['kode_depo']) ?> - <?= e($d['nama_depo']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="filterStatusBatch" class="form-select" style="width: auto; min-width: 170px;">
            <option value="">Semua Status FEFO</option>
            <option value="Aman" <?= ($statusFilter === 'Aman') ? 'selected' : '' ?>>Aman (&gt; 90 Hari)</option>
            <option value="Waspada" <?= ($statusFilter === 'Waspada') ? 'selected' : '' ?>>Waspada (31 - 90 Hari)</option>
            <option value="Kritis" <?= ($statusFilter === 'Kritis') ? 'selected' : '' ?>>Kritis (&le; 30 Hari)</option>
        </select>
    </div>
</div>

<!-- Tabel Monitoring Kedaluwarsa (FEFO) -->
<div class="table-responsive">
    <table class="table-custom" id="tableBatch">
        <thead>
            <tr>
                <th>No. Batch</th>
                <th>Produk FMCG</th>
                <th>Lokasi &amp; Rak</th>
                <th>Tgl Masuk (FIFO)</th>
                <th>Kedaluwarsa (FEFO)</th>
                <th>Sisa Masa Simpan</th>
                <th>Stok Fisik</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($batches)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding: 32px;">Tidak ada data batch stok yang cocok dengan kriteria filter.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($batches as $b): ?>
                    <tr data-batch="<?= e($b['no_batch']) ?>"
                        data-produk="<?= e($b['nama_produk']) ?>"
                        data-depo="<?= $b['id_depo'] ?>"
                        data-status="<?= e($b['status_expired']) ?>"
                        data-expired="<?= $b['tgl_expired'] ?>"
                        data-produk-name="<?= e($b['nama_produk']) ?>">
                        <td><code class="code-mono"><?= e($b['no_batch']) ?></code></td>
                        <td>
                            <strong><?= e($b['nama_produk']) ?></strong><br>
                            <small class="text-muted"><?= e($b['kode_sku']) ?></small>
                        </td>
                        <td>
                            <strong><?= e($b['kode_depo']) ?></strong><br>
                            <small class="text-muted">
                                <?= !empty($b['kode_rak']) ? 'Rak ' . e($b['kode_rak']) . ' (Zona ' . e($b['zona']) . ')' : '<span style="color:#B45309;">Belum di rak</span>' ?>
                            </small>
                        </td>
                        <td><?= format_date_id($b['tgl_masuk']) ?></td>
                        <td><strong><?= format_date_id($b['tgl_expired']) ?></strong></td>
                        <td>
                            <?php if ($b['sisa_hari'] < 0): ?>
                                <span class="badge badge-red">Sudah Kedaluwarsa</span>
                            <?php else: ?>
                                <span class="badge badge-<?= $b['badge_color'] ?>"><?= (int)$b['sisa_hari'] ?> Hari Lagi</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= format_number($b['jumlah']) ?></strong> Karton</td>
                        <td style="text-align: right; white-space: nowrap;">
                            <?php if (Permission::can('kedaluwarsa', 'edit')): ?>
                                <a href="<?= base_url('?page=kedaluwarsa&action=edit&id=' . $b['id']) ?>" class="btn btn-secondary btn-sm" title="Edit Batch">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                            <?php endif; ?>
                            <?php if (Permission::can('kedaluwarsa', 'delete')): ?>
                                <button type="button" class="btn btn-secondary btn-sm" style="color:#DC2626;" title="Hapus Batch"
                                        onclick="confirmDelete('<?= base_url('?page=kedaluwarsa&action=delete') ?>&id=<?= $b['id'] ?>', 'Batch <?= e($b['no_batch']) ?>')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            <?php endif; ?>
                            <?php if (!Permission::can('kedaluwarsa', 'edit') && !Permission::can('kedaluwarsa', 'delete')): ?>
                                <span class="text-muted" style="font-size:12px; font-style:italic;">Lihat Saja</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- bagian nurul khoyimah end -->

