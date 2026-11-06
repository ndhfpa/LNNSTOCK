<!-- bagian nadhifatul hilmy ns start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Matriks Rak Multi-Tier</h1>
        <p class="page-subtitle">Visualisasi tata letak, kapasitas, dan okupansi rak gudang secara real-time</p>
    </div>
    <div>
        <?php if (Permission::can('rak', 'create')): ?>
            <a href="<?= base_url('?page=rak&action=create&depo=' . $depoId) ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>+ Tambah Rak</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Bar Kontrol Denah Rak -->
<div class="rack-control-bar">
    <!-- Pilihan Depo Aktif -->
    <div class="depo-selector-wrap">
        <label for="depoSelector" style="font-weight:600; font-size:13px; color:#475569;">Depo Terpilih:</label>
        <select id="depoSelector" class="form-select" style="min-width: 250px; font-weight:600;" onchange="location = this.value;">
            <?php foreach ($depos as $d): ?>
                <option value="<?= base_url('?page=rak&depo=' . $d['id']) ?>" <?= ($d['id'] == $depoId) ? 'selected' : '' ?>>
                    <?= e($d['kode_depo']) ?> - <?= e($d['nama_depo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Filter Tab Zona (Semua / Zona A / B / C) -->
    <div class="zone-tabs">
        <button type="button" class="zone-tab-btn active" data-zone="Semua">Semua Zona</button>
        <button type="button" class="zone-tab-btn" data-zone="A">Zona A</button>
        <button type="button" class="zone-tab-btn" data-zone="B">Zona B</button>
        <button type="button" class="zone-tab-btn" data-zone="C">Zona C</button>
    </div>

    <!-- Legenda Okupansi -->
    <div class="occupancy-legend">
        <div class="legend-item"><span class="legend-dot dot-green"></span> <span>&lt;60% Tersedia</span></div>
        <div class="legend-item"><span class="legend-dot dot-yellow"></span> <span>60-90% Optimal</span></div>
        <div class="legend-item"><span class="legend-dot dot-red"></span> <span>&gt;90% Penuh</span></div>
        <div class="legend-item"><span class="legend-dot dot-gray"></span> <span>Maintenance</span></div>
    </div>
</div>

<!-- Grid Matriks Rak -->
<?php if (empty($racks)): ?>
    <div class="card text-center" style="padding: 48px 20px;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" style="margin: 0 auto 12px;">
            <path d="M4 4h16v16H4zM4 10h16M4 16h16M10 4v16M16 4v16"></path>
        </svg>
        <h3 style="font-size: 16px; color: #475569; margin-bottom: 6px;">Belum Ada Rak di Depo Ini</h3>
        <p class="text-muted" style="margin-bottom: 16px;">Silakan buat rak baru untuk mulai mengatur tata letak karton persediaan.</p>
        <a href="<?= base_url('?page=rak&action=create&depo=' . $depoId) ?>" class="btn btn-primary btn-sm" style="margin: 0 auto;">
            + Tambah Rak Baru
        </a>
    </div>
<?php else: ?>
    <div class="rack-matrix-grid">
        <?php foreach ($racks as $r): ?>
            <?php
                $statusColorClass = 'status-' . $r['status_warna'];
                $batchesJson = htmlspecialchars(json_encode($r['batches']), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="rack-card <?= $statusColorClass ?>"
                 data-id="<?= $r['id'] ?>"
                 data-kode="<?= e($r['kode_rak']) ?>"
                 data-zona="<?= e($r['zona']) ?>"
                 data-kapasitas="<?= (int)$r['kapasitas'] ?>"
                 data-terisi="<?= (int)$r['terisi'] ?>"
                 data-persen="<?= (int)$r['persen_okupansi'] ?>"
                 data-status="<?= e($r['status_rak']) ?>"
                 data-batches='<?= $batchesJson ?>'
                 title="Klik untuk melihat rincian batch rak <?= e($r['kode_rak']) ?>">
                
                <div>
                    <div class="rack-card-header">
                        <span class="rack-zone-badge">Zona <?= e($r['zona']) ?></span>
                        <span class="badge badge-<?= $r['status_warna'] ?>"><?= e($r['status_label']) ?></span>
                    </div>

                    <div class="rack-name">Rak <?= e($r['kode_rak']) ?></div>

                    <div class="rack-card-body" style="margin-top: 10px;">
                        <?php if (!empty($r['produk_dominan'])): ?>
                            <div class="rack-product-meta" title="<?= e($r['produk_dominan']) ?>">
                                <?= e($r['produk_dominan']) ?>
                            </div>
                            <div class="rack-batch-meta">
                                Batch: <code><?= e($r['batch_dominan']) ?></code>
                            </div>
                        <?php else: ?>
                            <div class="rack-product-meta text-muted" style="font-style: italic;">
                                Rak Kosong
                            </div>
                            <div class="rack-batch-meta">-</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="rack-card-footer">
                    <div class="rack-capacity-info">
                        <span><?= format_number($r['terisi']) ?> / <?= format_number($r['kapasitas']) ?> CTN</span>
                        <span><?= $r['persen_okupansi'] ?>%</span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-fill progress-<?= $r['status_warna'] ?>" style="width: <?= min(100, $r['persen_okupansi']) ?>%;"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Strip Ringkasan Bawah Sesuai Prototype -->
<div class="rack-summary-strip">
    <div class="summary-metric">
        <span class="summary-metric-title">Total Karton Tersimpan</span>
        <span class="summary-metric-value"><?= format_number($totalTerisiDepo) ?> / <?= format_number($totalKapasitasDepo) ?> <span style="font-size:13px; font-weight:500;">CTN</span></span>
    </div>

    <div class="summary-metric">
        <span class="summary-metric-title">Rak Penuh (&gt;90%)</span>
        <span class="summary-metric-value" style="color: #DC2626;"><?= $rakPenuhCount ?> Rak</span>
    </div>

    <div class="summary-metric">
        <span class="summary-metric-title">Rak Tersedia (&lt;60%)</span>
        <span class="summary-metric-value" style="color: #10B981;"><?= $rakTersediaCount ?> Rak</span>
    </div>

    <div class="unassigned-chip">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <span>Belum ditempatkan di rak: <strong><?= format_number($unassignedStock) ?></strong> karton</span>
    </div>
</div>

<!-- Modal Detail Layout Rak -->
<div class="modal-backdrop" id="modalDetailRak">
    <div class="modal-dialog" style="max-width: 640px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Detail Layout <span id="detailRakKode">Rak</span></h3>
                <span class="badge badge-blue" id="detailRakZona" style="margin-top:4px;">Zona</span>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeDetailRakModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding: 20px 24px;">
            <div style="display: flex; justify-content: space-between; background-color: #F8FAFC; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; border: 1px solid var(--border-light);">
                <div>
                    <span class="text-muted" style="font-size:12px;">Kapasitas Rak:</span><br>
                    <strong id="detailRakKapasitas" style="font-size:15px;">-</strong>
                </div>
                <div>
                    <span class="text-muted" style="font-size:12px;">Terisi Saat Ini:</span><br>
                    <strong id="detailRakTerisi" style="font-size:15px;">-</strong>
                </div>
                <div>
                    <span class="text-muted" style="font-size:12px;">Persentase:</span><br>
                    <strong id="detailRakPersen" style="font-size:15px; color:var(--primary);">-</strong>
                </div>
            </div>

            <h4 style="font-size: 13.5px; font-weight: 700; margin-bottom: 10px; color: #334155;">Daftar Batch Tersimpan di Rak:</h4>
            <div class="table-responsive" style="max-height: 240px; overflow-y: auto;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Produk FMCG</th>
                            <th>No. Batch</th>
                            <th>Jumlah</th>
                            <th>Masa Simpan</th>
                        </tr>
                    </thead>
                    <tbody id="detailRakBatchList">
                        <!-- Diisi secara dinamis oleh depo.js -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer" style="justify-content: space-between;">
            <button type="button" class="btn btn-secondary" onclick="closeDetailRakModal()">Tutup</button>
            <div style="display: flex; gap: 8px;">
                <?php if (Permission::can('rak', 'edit')): ?>
                    <a href="#" id="detailRakBtnEdit" data-base="<?= base_url('?page=rak&action=edit&id=') ?>" class="btn btn-secondary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                        </svg>
                        <span>Edit Rak</span>
                    </a>
                <?php endif; ?>
                <?php if (Permission::can('rak', 'delete')): ?>
                    <button type="button" id="detailRakBtnDelete" data-base="<?= base_url('?page=rak&action=delete&id=') ?>" class="btn btn-danger">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        <span>Hapus</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- bagian nadhifatul hilmy ns end -->

