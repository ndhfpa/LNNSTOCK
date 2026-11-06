<!-- bagian bersama start -->
    </main>
</div>

<!-- Modal Konfirmasi Hapus Universal -->
<div class="modal-backdrop" id="modalConfirmDelete">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">Konfirmasi Penghapusan</h3>
            <button type="button" class="btn-modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <form id="formConfirmDelete" method="POST" action="">
            <?= csrf_field() ?>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data <strong id="deleteItemName" class="text-danger">-</strong>?</p>
                <p class="modal-subtext">Tindakan ini tidak dapat dibatalkan. Pastikan data tidak sedang berelasi dengan transaksi atau batch lain.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus Permanen</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Global & Modul -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?php if ($currentPage === 'depo' || $currentPage === 'rak'): ?>
    <script src="<?= base_url('assets/js/depo.js') ?>"></script>
<?php elseif ($currentPage === 'produk'): ?>
    <script src="<?= base_url('assets/js/produk.js') ?>"></script>
<?php elseif ($currentPage === 'mutasi'): ?>
    <script src="<?= base_url('assets/js/mutasi.js') ?>"></script>
<?php elseif ($currentPage === 'kedaluwarsa'): ?>
    <script src="<?= base_url('assets/js/kedaluwarsa.js') ?>"></script>
<?php endif; ?>
</body>
</html>
<!-- bagian bersama end -->

