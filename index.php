<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/functions.php';

$daftarLayanan = ['Print Hitam', 'Print Warna', 'Fotokopi', 'Jilid Spiral'];

/* Lima data transaksi awal disimpan di session agar transaksi baru tetap ada. */
if (!isset($_SESSION['transaksi'])) {
    $_SESSION['transaksi'] = [
        ['id' => 1, 'pelanggan' => 'Ani Saputri', 'layanan' => 'Print Hitam', 'jumlah' => 40, 'member' => false, 'total' => 20000],
        ['id' => 2, 'pelanggan' => 'Budi Aminah', 'layanan' => 'Print Warna', 'jumlah' => 25, 'member' => true, 'total' => 35625],
        ['id' => 3, 'pelanggan' => 'Dudi Santoso', 'layanan' => 'Fotokopi', 'jumlah' => 150, 'member' => false, 'total' => 40500],
        ['id' => 4, 'pelanggan' => 'Citra Lestari', 'layanan' => 'Jilid Spiral', 'jumlah' => 20, 'member' => true, 'total' => 190000],
        ['id' => 5, 'pelanggan' => 'Rina Wulandari', 'layanan' => 'Fotokopi', 'jumlah' => 200, 'member' => true, 'total' => 51300],
    ];
}

$errors = [];
$success = '';
$namaInput = '';
$layananInput = 'Print Hitam';
$jumlahInput = '1';
$memberInput = false;

/* Proses form dengan metode POST. */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaInput = trim((string) ($_POST['pelanggan'] ?? ''));
    $layananInput = (string) ($_POST['layanan'] ?? '');
    $jumlahInput = trim((string) ($_POST['jumlah'] ?? ''));
    $memberInput = isset($_POST['member']);

    if (mb_strlen($namaInput) < 3) {
        $errors[] = 'Nama pelanggan minimal 3 karakter.';
    }

    if (!in_array($layananInput, $daftarLayanan, true)) {
        $errors[] = 'Jenis layanan tidak valid.';
    }

    if ($jumlahInput === '' || filter_var($jumlahInput, FILTER_VALIDATE_INT) === false || (int) $jumlahInput < 1) {
        $errors[] = 'Jumlah lembar harus berupa bilangan bulat minimal 1.';
    }

    if (empty($errors)) {
        $jumlah = (int) $jumlahInput;
        $transaksiBaru = [
            'id' => count($_SESSION['transaksi']) + 1,
            'pelanggan' => $namaInput,
            'layanan' => $layananInput,
            'jumlah' => $jumlah,
            'member' => $memberInput,
            'total' => hitungTotalBayar($layananInput, $jumlah, $memberInput),
        ];

        $_SESSION['transaksi'][] = $transaksiBaru;
        $success = 'Transaksi berhasil ditambahkan.';
        $namaInput = '';
        $layananInput = 'Print Hitam';
        $jumlahInput = '1';
        $memberInput = false;
    }
}

$semuaTransaksi = $_SESSION['transaksi'];
$rekap = hitungRekap($semuaTransaksi);

/* Filter dan pencarian menggunakan GET. */
$filterLayanan = (string) ($_GET['layanan'] ?? '');
$kataKunci = trim((string) ($_GET['q'] ?? ''));

$transaksiTampil = array_filter($semuaTransaksi, function (array $transaksi) use ($filterLayanan, $kataKunci): bool {
    $cocokLayanan = $filterLayanan === ''
        || $transaksi['layanan'] === $filterLayanan;

    $cocokNama = $kataKunci === ''
        || stripos($transaksi['pelanggan'], $kataKunci) !== false;

    return $cocokLayanan && $cocokNama;
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Percetakan Kampus | UTS PHP Native</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <header class="topbar">
        <div>
            <p class="eyebrow">FSTI · UNIVERSITAS WIDYA GAMA MALANG</p>
            <h1>Kasir Percetakan Kampus</h1>
            <p class="subtitle">Aplikasi transaksi dan rekapitulasi layanan percetakan</p>
        </div>
        <div class="server-label"><span class="status-dot"></span> PHP Native · Server-side</div>
    </header>

    <main>
        <section class="stats-grid" aria-label="Statistik transaksi">
            <article class="stat-card">
                <span class="stat-label">Total Transaksi</span>
                <strong><?= bersihkan((string) $rekap['total_transaksi']) ?></strong>
                <small>Seluruh transaksi</small>
            </article>
            <article class="stat-card">
                <span class="stat-label">Total Lembar</span>
                <strong><?= bersihkan((string) $rekap['total_lembar']) ?></strong>
                <small>Akumulasi jumlah</small>
            </article>
            <article class="stat-card">
                <span class="stat-label">Total Pendapatan</span>
                <strong class="stat-money"><?= bersihkan(rupiah($rekap['total_pendapatan'])) ?></strong>
                <small>Seluruh pembayaran</small>
            </article>
            <article class="stat-card">
                <span class="stat-label">Rata-rata / Order</span>
                <strong class="stat-money"><?= bersihkan(rupiah($rekap['rata_rata'])) ?></strong>
                <small>Per transaksi</small>
            </article>
        </section>

        <section class="content-grid">
            <article class="panel form-panel">
                <div class="panel-heading">
                    <div>
                        <span class="section-kicker">INPUT DATA</span>
                        <h2>Form Transaksi</h2>
                    </div>
                    <span class="method-tag">METHOD: POST</span>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <strong>Periksa kembali data berikut:</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= bersihkan($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success !== ''): ?>
                    <div class="alert alert-success"><?= bersihkan($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="field">
                        <label for="pelanggan">Nama Pelanggan <span>*</span></label>
                        <input type="text" id="pelanggan" name="pelanggan"
                               value="<?= bersihkan($namaInput) ?>"
                               placeholder="Masukkan nama pelanggan" required>
                    </div>

                    <div class="field">
                        <label for="layanan">Jenis Layanan <span>*</span></label>
                        <select id="layanan" name="layanan" required>
                            <?php foreach ($daftarLayanan as $layanan): ?>
                                <option value="<?= bersihkan($layanan) ?>"
                                    <?= $layananInput === $layanan ? 'selected' : '' ?>>
                                    <?= bersihkan($layanan) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="jumlah">Jumlah Lembar <span>*</span></label>
                        <input type="number" id="jumlah" name="jumlah" min="1"
                               value="<?= bersihkan($jumlahInput) ?>" required>
                    </div>

                    <label class="checkbox-row">
                        <input type="checkbox" name="member" value="1" <?= $memberInput ? 'checked' : '' ?>>
                        <span>
                            <strong>Status Member</strong>
                            <small>Tambahan diskon member 5%</small>
                        </span>
                    </label>

                    <button class="submit-button" type="submit">＋ Proses &amp; Tambah Transaksi (POST)</button>
                </form>

                <div class="price-note">
                    <strong>Daftar Tarif</strong>
                    <p>Print Hitam Rp500 · Print Warna Rp1.500</p>
                    <p>Fotokopi Rp300 · Jilid Spiral Rp10.000</p>
                    <p>Diskon 10% untuk jumlah &gt; 100, ditambah 5% untuk member.</p>
                </div>
            </article>

            <article class="panel table-panel">
                <div class="panel-heading table-heading">
                    <div>
                        <span class="section-kicker">DATA TRANSAKSI</span>
                        <h2>Filter &amp; Rekapitulasi</h2>
                    </div>
                    <span class="count-tag"><?= count($transaksiTampil) ?> data tampil</span>
                </div>

                <form method="GET" action="" class="filter-form">
                    <div class="field">
                        <label for="filter-layanan">Layanan</label>
                        <select id="filter-layanan" name="layanan">
                            <option value="">Semua Layanan</option>
                            <?php foreach ($daftarLayanan as $layanan): ?>
                                <option value="<?= bersihkan($layanan) ?>"
                                    <?= $filterLayanan === $layanan ? 'selected' : '' ?>>
                                    <?= bersihkan($layanan) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field search-field">
                        <label for="q">Cari Nama Pelanggan</label>
                        <input type="search" id="q" name="q"
                               value="<?= bersihkan($kataKunci) ?>"
                               placeholder="Nama pelanggan...">
                    </div>
                    <button type="submit" class="filter-button">Filter GET</button>
                    <a class="reset-link" href="index.php">Reset</a>
                </form>

                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Jumlah</th>
                            <th>Member</th>
                            <th class="text-right">Total Bayar</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($transaksiTampil) > 0): ?>
                            <?php $nomor = 1; ?>
                            <?php foreach ($transaksiTampil as $transaksi): ?>
                                <tr>
                                    <td><?= $nomor++ ?></td>
                                    <td class="customer-name"><?= bersihkan($transaksi['pelanggan']) ?></td>
                                    <td><?= bersihkan($transaksi['layanan']) ?></td>
                                    <td><?= bersihkan((string) $transaksi['jumlah']) ?></td>
                                    <td>
                                        <?php if ($transaksi['member']): ?>
                                            <span class="member yes">Ya (5%)</span>
                                        <?php else: ?>
                                            <span class="member no">Tidak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right amount"><?= bersihkan(rupiah($transaksi['total'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-state">Tidak ada transaksi yang sesuai dengan filter.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="table-footnote">Menampilkan data berdasarkan filter yang dipilih. Statistik di atas menghitung seluruh transaksi.</p>
            </article>
        </section>
    </main>

    <footer>
        <span>UTS Sistem Aplikasi Web · PHP Native Pertemuan 1–7</span>
        <span>HTML5 + CSS3 · 0% JavaScript</span>
    </footer>
</div>
</body>
</html>
