<?php

if (!function_exists('terbilang')) {
    function terbilang(float $angka): string
    {
        $angka = (int) abs($angka);
        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan',
            'Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas',
            'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];

        if ($angka < 20) return $bilangan[$angka];
        if ($angka < 100) {
            $r = $angka % 10;
            return $bilangan[(int)($angka / 10)] . ' Puluh' . ($r ? ' ' . $bilangan[$r] : '');
        }
        if ($angka < 200) return 'Seratus' . ($angka - 100 ? ' ' . terbilang($angka - 100) : '');
        if ($angka < 1000) {
            $r = $angka % 100;
            return $bilangan[(int)($angka / 100)] . ' Ratus' . ($r ? ' ' . terbilang($r) : '');
        }
        if ($angka < 2000) return 'Seribu' . ($angka - 1000 ? ' ' . terbilang($angka - 1000) : '');
        if ($angka < 1000000) {
            $r = $angka % 1000;
            return terbilang((int)($angka / 1000)) . ' Ribu' . ($r ? ' ' . terbilang($r) : '');
        }
        if ($angka < 1000000000) {
            $r = $angka % 1000000;
            return terbilang((int)($angka / 1000000)) . ' Juta' . ($r ? ' ' . terbilang($r) : '');
        }
        $r = $angka % 1000000000;
        return terbilang((int)($angka / 1000000000)) . ' Miliar' . ($r ? ' ' . terbilang($r) : '');
    }
}
