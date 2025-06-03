<?php
function generateSlug($string) {
    // 🧬 Fungsi untuk bikin slug dari nama cabang (biar rapi & URL friendly)
    $slug = strtolower(trim($string)); // 🤏 ubah ke huruf kecil & hapus spasi depan-belakang
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // 🚫 hapus karakter aneh
    $slug = preg_replace('/[\s-]+/', '-', $slug); // 🔀 ganti spasi & strip jadi strip tunggal
    return $slug;
}