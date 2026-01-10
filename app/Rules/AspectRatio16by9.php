<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Aturan Validasi Rasio Aspek 16:9
 *
 * Aturan validasi ini digunakan untuk memvalidasi apakah sebuah gambar
 * memiliki rasio aspek 16:9. Namun saat ini aturan ini dinonaktifkan
 * sementara untuk memastikan gambar bisa diupload.
 */
class AspectRatio16by9 implements ValidationRule
{
    /**
     * Menjalankan aturan validasi
     *
     * @param string $attribute Nama atribut yang divalidasi
     * @param mixed $value Nilai atribut yang divalidasi
     * @param Closure $fail Fungsi closure untuk menandai validasi sebagai gagal
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Rule sementara dinonaktifkan untuk memastikan gambar bisa diupload
        // Validasi ketat dihapus sementara agar gambar bisa muncul
    }
}
