<!-- bagian bersama start -->
<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($greeting) ?>, <?= e($currentUser['nama_lengkap'] ?? 'Agung Prayitno') ?>!</h1>
        <p class="page-subtitle">Ringkasan status operasional gudang PT Riyana Cipta Pangan Indonesia &bull; Role:
            <?= e($currentUser['role'] ?? 'Kepala Gudang') ?></p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <?php if (Permission::can('mutasi', 'create')): ?>
            <a href="<?= base_url('?page=mutasi&action=create') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>+ Buat Mutasi Baru</span>
            </a>
        <?php endif; ?>
        <?php if (Permission::can('kedaluwarsa', 'create')): ?>
            <a href="<?= base_url('?page=kedaluwarsa&action=create') ?>" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>+ Tambah Batch</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- 4 Kartu KPI Utama -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18M3 7v14M21 7v14M6 21V11a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10"></path>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Total Depo Gudang</div>
            <div class="kpi-value"><?= (int) $totalDepo ?> <span style="font-size:14px; font-weight:500;">Gudang</span>
            </div>
            <div class="kpi-subtext"><?= $okupansiGlobal ?>% kapasitas terisi
                (<?= format_number($totalStok) ?>/<?= format_number($totalKapasitas) ?> CTN)</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #DDF3E8; color: #0F6B4B;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path
                    d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                </path>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Total Varian FMCG</div>
            <div class="kpi-value"><?= (int) $totalSku ?> <span style="font-size:14px; font-weight:500;">Master
                    SKU</span></div>
            <div class="kpi-subtext">Happy Jelly, Happy Time, Cookies Time</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #FEF0D0; color: #B45309;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Total Persediaan</div>
            <div class="kpi-value"><?= format_number($totalStok) ?> <span
                    style="font-size:14px; font-weight:500;">CTN</span></div>
            <div class="kpi-subtext">Stok fisik aktif di seluruh depo</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background-color: #DCEBFA; color: #075B75;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="17 1 21 5 17 9"></polyline>
                <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                <polyline points="7 23 3 19 7 15"></polyline>
                <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Mutasi Bulan Ini</div>
            <div class="kpi-value"><?= format_number($kartonMutasi) ?> <span
                    style="font-size:14px; font-weight:500;">CTN</span></div>
            <div class="kpi-subtext"><?= (int) $mutasiBulanIni ?> surat jalan SPM diterbitkan</div>
        </div>
    </div>
</div>

<!-- Grid 2 Kolom: Peringatan Kedaluwarsa Dini & Stok di Bawah Batas -->
<div class="form-grid-2" style="margin-bottom: 24px;">
    <!-- Panel: Peringatan Kedaluwarsa Dini -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--primary);">Peringatan Kedaluwarsa Dini</h3>
                <p class="text-muted" style="font-size: 12px;">Prioritas pengeluaran berdasarkan FEFO</p>
            </div>
            <a href="<?= base_url('?page=kedaluwarsa') ?>" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>

        <?php if (empty($urgentBatches)): ?>
            <p class="text-muted text-center" style="padding: 20px;">Tidak ada batch yang mendekati tanggal kedaluwarsa.</p>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap: 12px;">
                <?php foreach ($urgentBatches as $ub): ?>
                    <?php
                    $sisa = (int) $ub['sisa_hari'];
                    $isLewat = $sisa < 0;
                    $badgeClass = ($sisa <= 30) ? 'badge-red' : 'badge-yellow';
                    $labelSisa = $isLewat ? 'Sudah Kedaluwarsa' : $sisa . ' hari lagi';
                    ?>
                    <div
                        style="display:flex; justify-content:space-between; align-items:center; padding: 12px 16px; background-color: #F8FAFC; border-radius: 12px; border: 1px solid var(--border-light);">
                        <div>
                            <strong><?= e($ub['nama_produk']) ?></strong>
                            <div style="font-size: 11.5px; color:#64748B;">
                                Batch: <code><?= e($ub['no_batch']) ?></code> &bull; Depo: <?= e($ub['kode_depo']) ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge <?= $badgeClass ?>"><?= $labelSisa ?></span>
                            <div style="font-size: 11.5px; font-weight: 600; margin-top: 2px;">
                                <?= format_number($ub['jumlah']) ?> CTN
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Panel: Stok di Bawah Batas Minimum -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #B91C1C;">Stok di Bawah Batas Buffer</h3>
                <p class="text-muted" style="font-size: 12px;">SKU membutuhkan pengadaan / mutasi cepat</p>
            </div>
            <a href="<?= base_url('?page=produk') ?>" class="btn btn-secondary btn-sm">Buka Katalog</a>
        </div>

        <?php if (empty($kritisProduk)): ?>
            <p class="text-muted text-center" style="padding: 20px;">Semua produk berada dalam batas buffer aman.</p>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap: 12px;">
                <?php foreach ($kritisProduk as $kp): ?>
                    <div
                        style="display:flex; justify-content:space-between; align-items:center; padding: 12px 16px; background-color: #FEF2F2; border-radius: 12px; border: 1px solid #FECACA;">
                        <div>
                            <strong style="color: #991B1B;"><?= e($kp['nama_produk']) ?></strong>
                            <div style="font-size: 11.5px; color:#B91C1C;">
                                SKU: <code><?= e($kp['kode_sku']) ?></code> &bull; Buffer Min:
                                <?= format_number($kp['buffer_minimum']) ?> CTN
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge badge-red">Kritis</span>
                            <div style="font-size: 12px; font-weight: 700; color: #DC2626; margin-top: 2px;">
                                Sisa: <?= format_number($kp['stok_karton']) ?> CTN
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Panel: Denah Rak & Okupansi Ringkas per Depo -->
<div class="card" style="margin-bottom: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--primary);">Denah Rak &amp; Okupansi Kapasitas
                Fasilitas</h3>
            <p class="text-muted" style="font-size: 12px;">Tingkat pengisian ruang simpan di setiap depo operasional</p>
        </div>
        <?php if (Permission::can('rak')): ?>
            <a href="<?= base_url('?page=rak') ?>" class="btn btn-secondary btn-sm">Buka Matriks Rak</a>
        <?php endif; ?>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
        <?php foreach ($depoSummaries as $ds): ?>
            <?php
            $kap = (int) $ds['kapasitas'];
            $isi = (int) $ds['terisi'];
            $pct = ($kap > 0) ? round(($isi / $kap) * 100) : 0;
            $pCol = ($pct > 90) ? 'progress-red' : (($pct >= 60) ? 'progress-yellow' : 'progress-green');
            ?>
            <div
                style="background-color: #F8FAFC; border: 1px solid var(--border-light); padding: 14px 18px; border-radius: 12px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <strong><?= e($ds['kode_depo']) ?></strong>
                    <span style="font-size: 12px; font-weight: 700;"><?= $pct ?>%</span>
                </div>
                <div
                    style="font-size: 12px; color: #64748B; margin: 2px 0 6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?= e($ds['nama_depo']) ?>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-fill <?= $pCol ?>" style="width: <?= min(100, $pct) ?>%;"></div>
                </div>
                <div style="font-size: 11px; color:#64748B; margin-top: 6px; text-align:right;">
                    <?= format_number($isi) ?> / <?= format_number($kap) ?> Karton
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Tabel: Transaksi Mutasi Terkini -->
<div class="card" style="margin-bottom: 0;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--primary);">Transaksi Mutasi Terkini</h3>
            <p class="text-muted" style="font-size: 12px;">5 aktivitas pemindahan stok antar-gudang terbaru</p>
        </div>
        <?php if (Permission::can('mutasi')): ?>
            <a href="<?= base_url('?page=mutasi') ?>" class="btn btn-secondary btn-sm">Lihat Semua Riwayat</a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>No. SPM</th>
                    <th>Tanggal</th>
                    <th>Produk &amp; Batch</th>
                    <th>Rute Pemindahan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentMutasi)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 24px;">Belum ada transaksi mutasi.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentMutasi as $rm): ?>
                        <tr>
                            <td><code class="code-mono"><?= e($rm['no_mutasi']) ?></code></td>
                            <td><?= format_date_id($rm['tanggal']) ?></td>
                            <td>
                                <strong><?= e($rm['nama_produk']) ?></strong><br>
                                <small class="text-muted">Batch: <?= e($rm['no_batch']) ?></small>
                            </td>
                            <td>
                                <div class="route-cell">
                                    <span><?= e($rm['depo_asal']) ?></span>
                                    <span class="route-arrow">&rarr;</span>
                                    <span><?= e($rm['depo_tujuan']) ?></span>
                                </div>
                            </td>
                            <td><strong><?= format_number($rm['jumlah']) ?></strong> CTN</td>
                            <td>
                                <?php if ($rm['status'] === 'Selesai'): ?>
                                    <span class="badge badge-green">Selesai</span>
                                <?php elseif ($rm['status'] === 'Menunggu'): ?>
                                    <span class="badge badge-blue">Menunggu</span>
                                <?php else: ?>
                                    <span class="badge badge-gray">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- bagian bersama end -->