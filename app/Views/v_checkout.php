<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <?= form_open('buy', 'class="row g-3"') ?>

        <?= form_hidden('username', session()->get('username')) ?>

        <div class="col-12">
            <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'     => 'nama',
                'id'       => 'nama',
                'class'    => 'form-control',
                'value'    => session()->get('username'),
                'readonly' => true]) ?>
        </div>
        <div class="col-12">
            <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'  => 'alamat',
                'id'    => 'alamat',
                'class' => 'form-control']) ?>
        </div> 
        <div class="col-12">
            <?= form_label('Kode Kupon (opsional)', 'kupon_code', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'  => 'kupon_code',
                'id'    => 'kupon_code',
                'class' => 'form-control',
                'placeholder' => 'Masukkan kode kupon (HEMAT20, HEMAT30, MEMBER25)'
            ]) ?>
            <small class="text-muted">Tersedia: HEMAT20, HEMAT30, MEMBER25</small>
        </div>
        <div class="col-12"> 
            <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
            <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?>
        </div>
        <div class="col-12"> 
            <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?> 
            <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?>
        </div>
        <div class="col-12">
            <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'     => 'ongkir',
                'id'       => 'ongkir',
                'class'    => 'form-control',
                'readonly' => true]) ?>
        </div>
        <div class="col-12">
            <?= form_submit(
                'submit',
                'Buat Pesanan',
                ['class' => 'btn btn-primary']) ?>
        </div>

        <?= form_close() ?> 
    </div>
    <div class="col-lg-6">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)) : ?>
                    <?php foreach ($items as $item) : ?>
                        <tr>
                            <td><?= $item['name'] ?></td>
                            <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                            <td><?= $item['qty'] ?></td>
                            <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal</td>
                    <td><?= number_to_currency($total, 'IDR') ?></td>
                </tr>
                <!-- BARIS BARU UNTUK RINCIAN -->
                <tr>
                    <td colspan="2"></td>
                    <td>Diskon Kupon</td>
                    <td><span id="diskon_display">IDR 0</span></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>PPN (12%)</td>
                    <td><span id="ppn_display">IDR 0</span></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Biaya Admin</td>
                    <td><span id="admin_display">IDR 0</span></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal (+PPN+Admin-Kupon)</td>
                    <td><span id="subtotal_after">IDR 0</span></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Grand Total (incl. Ongkir)</td>
                    <td><span id="grand_total_display">IDR 0</span></td>
                </tr>
            </tbody>
            </table>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    let ongkir = 0;
    let subtotal = <?= $total ?>; // subtotal produk dari controller

    // Daftar kupon (hardcode di JS, sesuai helper)
    const kuponList = {
        'HEMAT20': 0.20,
        'HEMAT30': 0.30,
        'MEMBER25': 0.25
    };

    // Fungsi utama untuk menghitung dan menampilkan
    function updatePerhitungan() {
        // 1. Ambil kode kupon (case-insensitive)
        let kode = $('#kupon_code').val().trim().toUpperCase();
        let diskon = 0;
        let diskonPersen = 0;
        if (kode && kuponList.hasOwnProperty(kode)) {
            diskonPersen = kuponList[kode];
            diskon = subtotal * diskonPersen;
        }

        // 2. PPN 12%
        let ppn = subtotal * 0.12;

        // 3. Biaya admin (berjenjang)
        let biayaAdmin = 0;
        if (subtotal <= 15000000) {
            biayaAdmin = subtotal * 0.005;
        } else if (subtotal <= 35000000) {
            biayaAdmin = subtotal * 0.007;
        } else {
            biayaAdmin = subtotal * 0.009;
        }

        // 4. Subtotal setelah diskon + ppn + admin
        let subtotalAfter = subtotal - diskon + ppn + biayaAdmin;

        // 5. Grand total = subtotalAfter + ongkir
        let grandTotal = subtotalAfter + ongkir;

        // 6. Update tampilan
        $('#diskon_display').text(
            diskon > 0 ? `-IDR ${diskon.toLocaleString('id-ID')} (${(diskonPersen*100)}%)` : 'IDR 0'
        );
        $('#ppn_display').text(`IDR ${ppn.toLocaleString('id-ID')}`);
        $('#admin_display').text(`IDR ${biayaAdmin.toLocaleString('id-ID')}`);
        $('#subtotal_after').text(`IDR ${subtotalAfter.toLocaleString('id-ID')}`);
        $('#grand_total_display').text(`IDR ${grandTotal.toLocaleString('id-ID')}`);

        // 7. Update input ongkir (agar terlihat nilainya)
        $('#ongkir').val(ongkir);

        // 8. (Opsional) set hidden total_harga = grandTotal jika perlu
        // $('#total_harga').val(grandTotal);

        // Debug (bisa dihapus setelah berhasil)
        console.log('Ongkir:', ongkir, 'Grand Total:', grandTotal);
    }

    // === Event untuk kupon ===
    $('#kupon_code').on('change keyup', function() {
        updatePerhitungan();
    });

    // === Event untuk layanan (dropdown ongkir) ===
    $('#layanan').on('change', function() {
        let val = $(this).val();
        if (val && !isNaN(val)) {
            ongkir = parseInt(val);
        } else {
            ongkir = 0;
        }
        updatePerhitungan();
    });

    // === Select2 untuk kelurahan (tetap seperti sebelumnya) ===
    $('#kelurahan').select2({
        placeholder: 'Cari daerah tujuan',
        minimumInputLength: 3,
        ajax: {
            url: '<?= site_url('ajax/destinations') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
    });

    // === Saat kelurahan berubah, load layanan ===
    $("#kelurahan").on('change', function () {
        let id_kelurahan = $(this).val();

        // Kosongkan dropdown layanan dan reset ongkir
        $("#layanan").empty();
        ongkir = 0;
        updatePerhitungan();

        $.ajax({
            url: "<?= site_url('ajax/costs') ?>",
            dataType: "json",
            data: { destination: id_kelurahan },
            success: function (data) {
                // Isi dropdown layanan
                data.forEach(function (item) {
                    $("#layanan").append(
                        $('<option>', {
                            value: item.cost,
                            text: `${item.description} (${item.service}) : estimasi ${item.etd}`
                        })
                    );
                });
                // Jika ada data, pilih yang pertama agar ongkir langsung terisi
                if (data.length > 0) {
                    let firstVal = data[0].cost;
                    $("#layanan").val(firstVal);
                    ongkir = firstVal;
                    updatePerhitungan();
                }
            },
            error: function() {
                console.log('Gagal mengambil data ongkir');
            }
        });
    });

    // === Jalankan update pertama kali ===
    updatePerhitungan();
});
</script>
<?= $this->endSection() ?>