<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

// ========================================================================
// MODEL USER - MANAJEMEN USER & AUTENTIKASI
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini adalah CORE dari sistem autentikasi dan manajemen user
// - Setiap user punya role (admin, seller, buyer) yang menentukan akses
// - Mendukung fitur: Register, Login, Profile Management, Role-based Access
//
// ROLE-BASED ACCESS CONTROL (RBAC):
// 1. admin    - Akses penuh ke semua fitur (admin dashboard, semua data)
// 2. seller   - Akses ke fitur penjual (produk, order masuk, dashboard seller)
// 3. buyer    - Akses ke fitur pembeli (belanja, order, wishlist, review)
//
// FITUR AUTENTIKASI:
// - Register: Buat akun baru dengan role 'buyer' (default)
// - Login:   Authenticate dengan email & password (Laravel Sanctum/Session)
// - Logout:  Destroy session/token
// - Password Hash: bcrypt untuk keamanan password
//
// INFORMASI USER:
// - Data dasar: name, email, password
// - Data tambahan: phone, address, lat/lng (untuk maps)
// - Data bank: bank_name, account_number (untuk seller withdrawal)
// - Lokasi: province, city, district, ward, full_address
//
// KEAMANAN:
// - Password di-hash menggunakan bcrypt (Laravel default)
// - Hidden fields: password & remember_token tidak di-serialize
// - Email verification: opsional (bisa di-enable)
// - Remember me: persistent login dengan token
// ========================================================================

/**
 * Model User
 *
 * Model ini merepresentasikan entitas pengguna dalam sistem e-commerce AKRAB.
 * Setiap pengguna dapat memiliki peran (role) sebagai admin, penjual, atau pembeli.
 * Model ini menyimpan informasi dasar pengguna seperti nama, email, serta informasi
 * tambahan seperti alamat, nomor telepon, dan informasi bank untuk penjual.
 * 
 * @package App\Models
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',                          // Nama lengkap pengguna
        'email',                         // Alamat email pengguna
        'password',                      // Kata sandi pengguna (akan di-hash)
        'role_id',                       // ID peran pengguna (admin, seller, buyer)
        'status',                        // Status akun (aktif, ditangguhkan, dll)
        'phone',                         // Nomor telepon pengguna
        'address',                       // Alamat pengguna
        'lat',                           // Koordinat lintang (untuk lokasi)
        'lng',                           // Koordinat bujur (untuk lokasi)
        'shop_description',              // Deskripsi toko (untuk penjual)
        'bank_name',                     // Nama bank (untuk penjual)
        'bank_account_number',           // Nomor rekening bank (untuk penjual)
        'bank_account_name',             // Nama pemilik rekening (untuk penjual)
        'province',                      // Provinsi
        'city',                          // Kota
        'district',                      // Kecamatan
        'ward',                          // Kelurahan
        'full_address',                  // Alamat lengkap
        'bio',                           // Biografi singkat
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',                      // Kata sandi tidak ditampilkan saat serialisasi
        'remember_token',                // Token sesi tidak ditampilkan saat serialisasi
    ];

    /**
     * Konfigurasi casting atribut
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // Casting kolom verifikasi email ke datetime
            'password' => 'hashed',             // Kata sandi di-hash saat disimpan
        ];
    }

    /**
     * Relasi ke model Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Memeriksa apakah pengguna memiliki peran tertentu
     *
     * @param string|\Illuminate\Support\Collection $roleName Nama peran atau koleksi ID peran
     * @return bool True jika pengguna memiliki peran yang ditentukan
     */
    public function hasRole($roleName)
    {
        if (!$this->role) {
            return false;
        }

        if (is_string($roleName)) {
            return $this->role->name === $roleName;
        }

        return $roleName->contains($this->role->id);
    }

    /**
     * Memberikan peran kepada pengguna
     *
     * @param string|int|\App\Models\Role $role Peran yang akan diberikan (bisa berupa nama, ID, atau objek Role)
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        } elseif (is_numeric($role)) {
            $role = Role::findOrFail($role);
        }

        $this->update(['role_id' => $role->id]);
    }

    /**
     * Relasi ke pesanan yang dibuat oleh pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi ke ulasan yang dibuat oleh pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relasi ke wishlist milik pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Relasi ke pesan yang dikirim oleh pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * Relasi ke pesan yang diterima oleh pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedMessages()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    /**
     * Relasi ke notifikasi milik pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relasi ke permintaan penarikan dana milik penjual
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function withdrawalRequests()
    {
        return $this->hasManyThrough(WithdrawalRequest::class, Seller::class, 'user_id', 'seller_id');
    }

    /**
     * Relasi ke produk yang dijual oleh pengguna (melalui seller)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function products()
    {
        return $this->hasManyThrough(Product::class, Seller::class, 'user_id', 'seller_id');
    }

    /**
     * Mendapatkan nama peran pengguna secara aman
     *
     * @return string Nama peran atau 'No Role' jika tidak memiliki peran
     */
    public function getRoleName()
    {
        return $this->role ? $this->role->name : 'No Role';
    }

    /**
     * Relasi ke tiket yang dibuat oleh pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Relasi ke tiket terbuka milik pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openTickets()
    {
        return $this->hasMany(Ticket::class, 'user_id')->where('status', 'open');
    }

    /**
     * Relasi ke profil penjual milik pengguna
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function seller()
    {
        return $this->hasOne(Seller::class, 'user_id');
    }

    /**
     * Mendapatkan tiket terbaru milik pengguna
     *
     * @param int $limit Jumlah maksimum tiket yang akan diambil
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recentTickets($limit = 5)
    {
        return $this->hasMany(Ticket::class, 'user_id')->latest()->limit($limit);
    }
}
