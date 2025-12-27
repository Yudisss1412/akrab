<?php
// Script untuk memeriksa user yang sedang login

require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

// Kita tidak bisa langsung mengakses session di script CLI seperti ini
// Jadi kita akan coba dengan cara lain

// Ambil semua user yang ada
$users = User::all();
echo "Daftar semua user di database:\n";
foreach ($users as $user) {
    echo "- ID: " . $user->id . ", Name: " . $user->name . ", Email: " . $user->email . "\n";
}

// Kita juga bisa cek user dengan ID 13 secara spesifik
$user13 = User::find(13);
if ($user13) {
    echo "\nDetail user dengan ID 13:\n";
    echo "ID: " . $user13->id . "\n";
    echo "Name: " . $user13->name . "\n";
    echo "Email: " . $user13->email . "\n";
    echo "Role ID: " . $user13->role_id . "\n";
    echo "Created At: " . $user13->created_at . "\n";
    echo "Status: " . $user13->status . "\n";
} else {
    echo "\nUser dengan ID 13 tidak ditemukan\n";
}