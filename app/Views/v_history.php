<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
History Transaksi Pembelian <strong><?= $username ?></strong>
<hr>
<div class="table-responsive">
    <table class="table datatable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ID Pembelian</th>
                <th scope="col">Waktu Pembelian</th>
                <th scope="col">Total Bayar</th>
                <th scope="col">Alamat</th>
                <th scope="col">Status</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)) : ?>
                <?php foreach ($transactions as $index => $item) : ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= $item['id'] ?></td>
                        <td><?= $item['created_at'] ?></td>
                        <td>
                            <?php 
                            $grand_total = $item['grand_total'] ?? ($item['total_harga'] ?? 0 + $item['ongkir'] ?? 0);
                            echo number_to_currency($grand_total, 'IDR');
                            ?>
                        </td>
                        <td><?= $item['alamat'] ?></td>
                        <td>
                            <?= ($item['status'] == "1")
                                ? '<span class="badge bg-success">Sudah Selesai</span>'
                                : '<span class="badge bg-warning">Belum Selesai</span>' ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal-<?= $item['id'] ?>">
                                Detail
                            </button>
                        </td>
                    </tr> 
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($transactions)) : ?>
    <?php foreach ($transactions as $item) : ?>
        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal-<?= $item['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Transaksi #<?= $item['id'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"> 
                        <!-- Loop Produk -->
                        <?php if (!empty($products[$item['id']])) : ?>
                            <?php foreach ($products[$item['id']] as $index2 => $item2) : ?>
                                <div><?= $index2 + 1 . ")" ?></div>
                                <?php
                                $imagePath = FCPATH . 'img/' . $item2['foto'];
                                if (!empty($item2['foto']) && file_exists($imagePath)) :
                                ?>
                                    <div class="my-2">
                                        <img src="<?= base_url('img/' . $item2['foto']) ?>" width="100" class="img-thumbnail">
                                    </div>
                                <?php endif; ?>
                                <strong><?= $item2['nama'] ?></strong>
                                <?= number_to_currency($item2['harga'] ?? 0, 'IDR') ?>
                                <br>
                                <?= "(" . ($item2['jumlah'] ?? 0) . " pcs)" ?><br>
                                <?= number_to_currency($item2['subtotal_harga'] ?? 0, 'IDR') ?>
                                <hr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Rincian Biaya -->
                        <div class="row">
                            <div class="col-8">Subtotal Produk</div>
                            <div class="col-4 text-end"><?= number_to_currency($item['total_harga'] ?? 0, 'IDR') ?></div>
                        </div>
                        <?php if (!empty($item['kupon_code'])) : ?>
                        <div class="row">
                            <div class="col-8">Diskon Kupon (<?= $item['kupon_code'] ?>)</div>
                            <div class="col-4 text-end text-danger">- <?= number_to_currency($item['diskon_kupon'] ?? 0, 'IDR') ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-8">PPN (12%)</div>
                            <div class="col-4 text-end"><?= number_to_currency($item['ppn'] ?? 0, 'IDR') ?></div>
                        </div>
                        <div class="row">
                            <div class="col-8">Biaya Admin</div>
                            <div class="col-4 text-end"><?= number_to_currency($item['biaya_admin'] ?? 0, 'IDR') ?></div>
                        </div>
                        <div class="row">
                            <div class="col-8">Ongkir</div>
                            <div class="col-4 text-end"><?= number_to_currency($item['ongkir'] ?? 0, 'IDR') ?></div>
                        </div>
                        <hr>
                        <div class="row fw-bold">
                            <div class="col-8">Grand Total</div>
                            <div class="col-4 text-end">
                                <?php 
                                $grand_total = $item['grand_total'] ?? ($item['total_harga'] ?? 0 + $item['ongkir'] ?? 0);
                                echo number_to_currency($grand_total, 'IDR');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?= $this->endSection() ?>