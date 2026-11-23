<?php
declare(strict_types=1);

/**
 * Mengamankan teks sebelum ditampilkan ke HTML.
 */
function bersihkan(string $teks): string
{
    return htmlspecialchars(trim($teks), ENT_QUOTES, 'UTF-8');
}

/**
 * Mengubah angka menjadi format Rupiah.
 */
function rupiah(float|int $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Menghitung total pembayaran berdasarkan layanan dan diskon.
 * Diskon 10% berlaku jika jumlah > 100 lembar.
 * Diskon member sebesar 5% ditambahkan ke diskon tersebut.
 */
function hitungTotalBayar(string $layanan, int $jumlah, bool $isMember = false): float
{
    switch ($layanan) {
        case 'Print Hitam':
            $tarif = 500;
            break;
        case 'Print Warna':
            $tarif = 1500;
            break;
        case 'Fotokopi':
            $tarif = 300;
            break;
        case 'Jilid Spiral':
            $tarif = 10000;
            break;
        default:
            $tarif = 0;
    }

    $subtotal = $tarif * $jumlah;
    $diskon = ($jumlah > 100) ? 0.10 : 0.0;

    if ($isMember) {
        $diskon += 0.05;
    }

    return $subtotal * (1 - $diskon);
}

/**
 * Menghitung ringkasan seluruh transaksi.
 */
function hitungRekap(array $daftarTransaksi): array
{
    $totalTransaksi = count($daftarTransaksi);
    $totalLembar = 0;
    $totalPendapatan = 0;

    foreach ($daftarTransaksi as $transaksi) {
        $totalLembar += (int) ($transaksi['jumlah'] ?? 0);
        $totalPendapatan += (float) ($transaksi['total'] ?? 0);
    }

    $rataRata = $totalTransaksi > 0
        ? $totalPendapatan / $totalTransaksi
        : 0;

    return [
        'total_transaksi' => $totalTransaksi,
        'total_lembar' => $totalLembar,
        'total_pendapatan' => $totalPendapatan,
        'rata_rata' => $rataRata,
    ];
}
