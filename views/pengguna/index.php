<!-- bagian bersama start -->
<div class="page-header">
    <div>
        <h1 class="page-title">Kelola Pengguna Sistem</h1>
        <p class="page-subtitle">Pusat kendali akun pengguna dan penetapan hak akses operasional LNNSTOCK</p>
    </div>
    <div>
        <a href="<?= base_url('?page=pengguna&action=create') ?>" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>+ Tambah Pengguna</span>
        </a>
    </div>
</div>

<!-- Bar Pencarian Live Search -->
<div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
    <div class="search-input-wrap">
        <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="searchPengguna" class="form-input" placeholder="Cari nama pengguna atau username...">
    </div>
</div>

<!-- Tabel Pengguna -->
<div class="table-responsive">
    <table class="table-custom" id="tablePengguna">
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Hak Akses (Role)</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding: 32px;">Belum ada data pengguna terdaftar.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($users as $u): ?>
                    <?php
                        $roleBadge = 'badge-blue';
                        if ($u['role'] === 'Kepala Gudang') $roleBadge = 'badge-green';
                        elseif ($u['role'] === 'Petugas Gudang') $roleBadge = 'badge-yellow';
                        elseif ($u['role'] === 'Staff Distribusi') $roleBadge = 'badge-blue';
                        $isSelf = ((int)$u['id'] === (int)($_SESSION['user']['id'] ?? 0));
                    ?>
                    <tr data-nama="<?= e($u['nama_lengkap']) ?>" data-user="<?= e($u['username']) ?>">
                        <td><?= $no++ ?></td>
                        <td>
                            <code class="code-mono" style="font-weight:700; color:var(--primary);"><?= e($u['username']) ?></code>
                            <?php if ($isSelf): ?>
                                <span class="badge badge-gray" style="font-size:10px; margin-left:4px;">Akun Anda</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($u['nama_lengkap']) ?></strong></td>
                        <td>
                            <span class="badge <?= $roleBadge ?>"><?= e($u['role']) ?></span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="<?= base_url('?page=pengguna&action=edit&id=' . $u['id']) ?>" class="btn btn-secondary btn-sm" title="Edit Pengguna">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                </svg>
                                <span>Edit</span>
                            </a>
                            <?php if (!$isSelf): ?>
                                <button type="button" class="btn btn-secondary btn-sm" style="color:#DC2626;" title="Hapus Pengguna"
                                        onclick="confirmDelete('<?= base_url('?page=pengguna&action=delete') ?>&id=<?= $u['id'] ?>', 'Pengguna <?= e($u['nama_lengkap']) ?> (<?= e($u['username']) ?>)')">
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

<script>
// Live Search Tabel Pengguna
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchPengguna');
    const rows = document.querySelectorAll('#tablePengguna tbody tr');

    if (searchInput && rows.length > 0) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            rows.forEach(function (row) {
                const nama = (row.getAttribute('data-nama') || '').toLowerCase();
                const user = (row.getAttribute('data-user') || '').toLowerCase();
                if (query === '' || nama.includes(query) || user.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
<!-- bagian bersama end -->

