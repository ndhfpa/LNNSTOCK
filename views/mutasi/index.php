<!-- bagian nurul khoyimah start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Mutasi Stok Antar-Depo</h1>
        <p class="page-subtitle">Pemindahan stok terintegrasi PT Riyana Cipta Pangan Indonesia: buffer rotasi, batch kedaluwarsa, dan tracking logistik multi-depo</p>
    </div>
    <div>
        <?php if (Permission::can('mutasi', 'create')): ?>
            <a href="<?= base_url('?page=mutasi&action=create') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>+ Buat Surat Perintah Mutasi (SPM)</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- 3 Kartu KPI Mutasi Antar-Depo -->
<div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #DCEBFA; color: #075B75;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Menunggu Persetujuan</div>
            <div class="kpi-value"><?= (int)$kpi['pending_count'] ?> <span style="font-size:14px; font-weight:500;">Draft</span></div>
            <div class="kpi-subtext">Menanti verifikasi gudang</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #DDF3E8; color: #0F6B4B;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Selesai Bulan Ini</div>
            <div class="kpi-value"><?= (int)$kpi['selesai_bulan_ini'] ?> <span style="font-size:14px; font-weight:500;">Transfer</span></div>
            <div class="kpi-subtext">Berhasil diterima di depo tujuan</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #FEF0D0; color: #B45309;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="3" width="15" height="13"></rect>
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Total Karton Dipindahkan</div>
            <div class="kpi-value"><?= format_number($kpi['karton_dipindahkan']) ?> <span style="font-size:14px; font-weight:500;">CTN</span></div>
            <div class="kpi-subtext">Volume mutasi bulan berjalan</div>
        </div>
    </div>
</div>

<!-- Tabs Status Riwayat Mutasi -->
<div class="status-tabs">
    <a href="#" class="status-tab-btn <?= ($statusFilter === 'Semua') ? 'active' : '' ?>" data-status="Semua">
        <span>Semua</span>
        <span class="status-tab-count"><?= (int)$kpi['total_semua'] ?></span>
    </a>
    <a href="#" class="status-tab-btn <?= ($statusFilter === 'Menunggu') ? 'active' : '' ?>" data-status="Menunggu">
        <span>Menunggu Approval</span>
        <span class="status-tab-count"><?= (int)$kpi['pending_count'] ?></span>
    </a>
    <a href="#" class="status-tab-btn <?= ($statusFilter === 'Selesai') ? 'active' : '' ?>" data-status="Selesai">
        <span>Selesai</span>
        <span class="status-tab-count"><?= (int)$kpi['total_selesai'] ?></span>
    </a>
    <a href="#" class="status-tab-btn <?= ($statusFilter === 'Dibatalkan') ? 'active' : '' ?>" data-status="Dibatalkan">
        <span>Ditolak / Batal</span>
        <span class="status-tab-count"><?= (int)$kpi['total_dibatalkan'] ?></span>
    </a>
</div>

<!-- Bar Pencarian Live Riwayat -->
<div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
    <div class="search-input-wrap">
        <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="searchMutasi" class="form-input" placeholder="Cari nomor surat mutasi, produk, rute depo asal / tujuan...">
    </div>
</div>

<!-- Tabel Riwayat Mutasi -->
<div class="table-responsive">
    <table class="table-custom" id="tableMutasi">
        <thead>
            <tr>
                <th>No. Mutasi / SPM</th>
                <th>Tanggal</th>
                <th>Produk &amp; Batch FEFO</th>
                <th>Rute (Asal &rarr; Tujuan)</th>
                <th>Jumlah (CTN)</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($mutasis)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding: 32px;">Belum ada riwayat mutasi stok antar-depo.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($mutasis as $m): ?>
                    <tr data-nomutasi="<?= e($m['no_mutasi']) ?>"
                        data-produk="<?= e($m['nama_produk']) ?> <?= e($m['no_batch']) ?>"
                        data-rute="<?= e($m['nama_asal']) ?> <?= e($m['nama_tujuan']) ?>"
                        data-status="<?= e($m['status']) ?>">
                        <td>
                            <code class="code-mono" style="font-weight:700; color:var(--primary);"><?= e($m['no_mutasi']) ?></code>
                            <div style="font-size:11px; color:#64748B;">Oleh: <?= e($m['pembuat']) ?></div>
                        </td>
                        <td><?= format_date_id($m['tanggal']) ?></td>
                        <td>
                            <strong><?= e($m['nama_produk']) ?></strong><br>
                            <small class="text-muted">Batch: <code><?= e($m['no_batch']) ?></code> &bull; Exp: <?= format_date_id($m['tgl_expired']) ?></small>
                        </td>
                        <td>
                            <div class="route-cell">
                                <span><?= e($m['nama_asal']) ?></span>
                                <span class="route-arrow">&rarr;</span>
                                <span style="color:var(--primary); font-weight:700;"><?= e($m['nama_tujuan']) ?></span>
                            </div>
                        </td>
                        <td>
                            <strong style="font-size:15px;"><?= format_number($m['jumlah']) ?></strong>
                            <small class="text-muted">Karton</small>
                        </td>
                        <td>
                            <?php if ($m['status'] === 'Selesai'): ?>
                                <span class="badge badge-green">Diterima Gudang (Selesai)</span>
                            <?php elseif ($m['status'] === 'Menunggu'): ?>
                                <span class="badge badge-blue">Menunggu Approval</span>
                            <?php else: ?>
                                <span class="badge badge-gray">Ditolak / Batal</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <?php if ($m['status'] === 'Menunggu'): ?>
                                <?php if (Permission::can('mutasi', 'complete')): ?>
                                    <!-- Tombol Setujui / Selesaikan -->
                                    <form action="<?= base_url('?page=mutasi&action=complete') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin menyetujui mutasi ini? Stok fisik akan langsung dipindahkan secara otomatis.');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm" title="Setujui dan Selesaikan Mutasi">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            <span>Setujui</span>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if (Permission::can('mutasi', 'cancel')): ?>
                                    <!-- Tombol Batalkan -->
                                    <form action="<?= base_url('?page=mutasi&action=cancel') ?>" method="POST" style="display:inline;" onsubmit="return confirm('Batalkan permintaan mutasi ini?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Batalkan Mutasi">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                                <line x1="9" y1="9" x2="15" y2="15"></line>
                                            </svg>
                                            <span>Batal</span>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if (Permission::can('mutasi', 'delete')): ?>
                                    <!-- Tombol Hapus Draft -->
                                    <button type="button" class="btn btn-secondary btn-sm" style="color:#DC2626;" title="Hapus Mutasi"
                                            onclick="confirmDelete('<?= base_url('?page=mutasi&action=delete') ?>&id=<?= $m['id'] ?>', 'Draft Mutasi <?= e($m['no_mutasi']) ?>')">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                <?php endif; ?>

                                <?php if (!Permission::can('mutasi', 'complete') && !Permission::can('mutasi', 'cancel') && !Permission::can('mutasi', 'delete')): ?>
                                    <span class="text-muted" style="font-size:12px; font-style:italic;">Menunggu Approval</span>
                                <?php endif; ?>

                            <?php elseif ($m['status'] === 'Dibatalkan'): ?>
                                <?php if (Permission::can('mutasi', 'delete')): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" style="color:#DC2626;" title="Hapus Mutasi Dibatalkan"
                                            onclick="confirmDelete('<?= base_url('?page=mutasi&action=delete') ?>&id=<?= $m['id'] ?>', 'Riwayat Batal <?= e($m['no_mutasi']) ?>')">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:12px; font-style:italic;">Dibatalkan</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:12px; font-style:italic;">Terkunci (Arsip Audit)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- bagian nurul khoyimah end -->

