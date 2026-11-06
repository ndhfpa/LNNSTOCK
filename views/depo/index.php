<!-- bagian nadhifatul hilmy ns start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Master Depo Gudang</h1>
        <p class="page-subtitle">Pusat kelola fasilitas penyimpanan, pabrikasi, dan titik distribusi FMCG</p>
    </div>
    <div>
        <?php if (Permission::can('depo', 'create')): ?>
            <a href="<?= base_url('?page=depo&action=create') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>+ Tambah Depo</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- 3 Kartu KPI Depo -->
<div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="kpi-card">
        <div class="kpi-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18M3 7v14M21 7v14M6 21V11a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10"></path>
            </svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-label">Total Depo</div>
            <div class="kpi-value"><?= (int)$kpi['total_depo'] ?></div>
            <div class="kpi-subtext">Seluruh fasilitas operasional</div>
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
            <div class="kpi-label">Depo Aktif</div>
            <div class="kpi-value"><?= (int)$kpi['depo_aktif'] ?></div>
            <div class="kpi-subtext">Siap melayani arus barang</div>
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
            <div class="kpi-label">Total Kapasitas Rak</div>
            <div class="kpi-value"><?= format_number($kpi['total_kapasitas']) ?> <span style="font-size:14px; font-weight:500;">CTN</span></div>
            <div class="kpi-subtext">Daya tampung seluruh depo</div>
        </div>
    </div>
</div>

<!-- Filter & Live Search Bar -->
<div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
    <div class="search-input-wrap">
        <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="searchDepo" class="form-input" placeholder="Cari kode depo, nama gudang, atau lokasi...">
    </div>
</div>

<!-- Tabel Master Depo -->
<div class="table-responsive">
    <table class="table-custom" id="tableDepo">
        <thead>
            <tr>
                <th>Kode Depo</th>
                <th>Nama Fasilitas Gudang</th>
                <th>Jenis Depo</th>
                <th>Lokasi Operasional</th>
                <th style="min-width: 190px;">Kapasitas & Okupansi</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($depos)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding: 32px;">Belum ada data depo terdaftar.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($depos as $d): ?>
                    <?php
                        $kapasitas = (int)$d['total_kapasitas'];
                        $terisi = (int)$d['total_terisi'];
                        $persen = ($kapasitas > 0) ? round(($terisi / $kapasitas) * 100) : 0;
                        $progColor = ($persen > 90) ? 'progress-red' : (($persen >= 60) ? 'progress-yellow' : 'progress-green');
                    ?>
                    <tr>
                        <td><code class="code-mono"><?= e($d['kode_depo']) ?></code></td>
                        <td>
                            <strong><?= e($d['nama_depo']) ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-blue"><?= e($d['jenis_depo']) ?></span>
                        </td>
                        <td><?= e($d['lokasi']) ?></td>
                        <td>
                            <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:600;">
                                <span><?= format_number($terisi) ?> / <?= format_number($kapasitas) ?> CTN</span>
                                <span><?= $persen ?>%</span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-fill <?= $progColor ?>" style="width: <?= min(100, $persen) ?>%;"></div>
                            </div>
                        </td>
                        <td>
                            <?php if ($d['status'] === 'Aktif'): ?>
                                <span class="badge badge-green">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-gray"><?= e($d['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <?php if (Permission::can('rak')): ?>
                                <a href="<?= base_url('?page=rak&depo=' . $d['id']) ?>" class="btn btn-secondary btn-sm" title="Lihat Denah Rak">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16v16H4zM4 10h16M4 16h16M10 4v16M16 4v16"></path>
                                    </svg>
                                    <span>Denah Rak</span>
                                </a>
                            <?php endif; ?>
                            <?php if (Permission::can('depo', 'edit')): ?>
                                <a href="<?= base_url('?page=depo&action=edit&id=' . $d['id']) ?>" class="btn btn-secondary btn-sm" title="Edit Depo">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                            <?php endif; ?>
                            <?php if (Permission::can('depo', 'delete')): ?>
                                <button type="button" class="btn btn-secondary btn-sm" style="color:#DC2626;" title="Hapus Depo"
                                        onclick="confirmDelete('<?= base_url('?page=depo&action=delete') ?>&id=<?= $d['id'] ?>', 'Depo <?= e($d['nama_depo']) ?>')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- bagian nadhifatul hilmy ns end -->

