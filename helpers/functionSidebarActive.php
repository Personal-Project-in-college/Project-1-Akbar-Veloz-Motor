<?php

/**
 * File: functionSidebarActive.php
 * Berisi fungsi-fungsi helper untuk menentukan status aktif pada menu sidebar
 * berdasarkan URL yang sedang diakses.
 */

/**
 * Memeriksa apakah menu utama sidebar harus aktif.
 *
 * Ini adalah fungsi pembantu (wrapper) yang lebih sederhana untuk `isSubMenuActive`.
 * Fungsi ini memeriksa apakah sebuah segmen URL (misal: 'branches') ada di dalam path URL saat ini.
 *
 * @param string $segment Segmen URL yang ingin diperiksa (misal: 'branches', 'vehicles').
 * @return bool Mengembalikan `true` jika segmen ditemukan, `false` jika tidak.
 * @see isSubMenuActive()
 */
function isSidebarMenuActive(string $segment): bool
{
    // Memanggil fungsi isSubMenuActive dengan array yang berisi satu segmen.
    // Ini menghilangkan duplikasi kode.
    return isSubMenuActive([$segment]);
}

/**
 * Memeriksa apakah sebuah menu (yang mungkin memiliki banyak submenu) harus aktif.
 *
 * Fungsi ini akan memeriksa apakah URL saat ini mengandung SALAH SATU dari segmen
 * yang diberikan dalam array. Berguna untuk dropdown menu di mana beberapa halaman
 * submenu harus membuat menu utamanya tetap aktif.
 *
 * @param array $segments Array berisi segmen-segmen URL yang ingin diperiksa.
 * @return bool Mengembalikan `true` jika salah satu segmen ditemukan, `false` jika tidak ada yang cocok.
 */
function isSubMenuActive(array $segments): bool
{
    // Mengambil path URL saat ini, contoh: /admin/branches/create.php
    $currentPath = $_SERVER['REQUEST_URI'];

    // Lakukan iterasi pada setiap segmen yang diberikan.
    foreach ($segments as $segment) {
        // Cek apakah '/segmen/' ada di dalam path URL.
        // Penambahan slash di awal dan akhir memastikan kita tidak salah mencocokkan.
        // Contoh: segmen 'user' tidak akan cocok dengan '/users/'.
        if (strpos($currentPath, '/' . $segment . '/') !== false) {
            // Jika satu saja ditemukan, langsung kembalikan true dan hentikan fungsi.
            return true;
        }
    }

    // Jika setelah semua iterasi tidak ada segmen yang cocok, kembalikan false.
    return false;
}
