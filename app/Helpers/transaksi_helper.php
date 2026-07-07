<?php

if (!function_exists('hitung_ppn')) {
    function hitung_ppn($total_harga)
    {
        return $total_harga * 0.12;
    }
}

if (!function_exists('hitung_biaya_admin')) {
    function hitung_biaya_admin($total_harga)
    {
        if ($total_harga <= 15000000) {
            return $total_harga * 0.005;
        } elseif ($total_harga <= 35000000) {
            return $total_harga * 0.007;
        } else {
            return $total_harga * 0.009;
        }
    }
}

if (!function_exists('hitung_diskon_kupon')) {
    function hitung_diskon_kupon($total_harga, $kupon_code)
    {
        $kupon_list = [
            'HEMAT20'  => 0.20,
            'HEMAT30'  => 0.30,
            'MEMBER25' => 0.25,
        ];

        if (!empty($kupon_code) && isset($kupon_list[strtoupper($kupon_code)])) {
            $diskon = $total_harga * $kupon_list[strtoupper($kupon_code)];
            return ['diskon' => $diskon, 'kode' => strtoupper($kupon_code)];
        }

        return ['diskon' => 0, 'kode' => null];
    }
}