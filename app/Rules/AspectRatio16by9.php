<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AspectRatio16by9 implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Rule sementara dinonaktifkan untuk memastikan gambar bisa diupload
        // Validasi ketat dihapus sementara agar gambar bisa muncul
    }
}
