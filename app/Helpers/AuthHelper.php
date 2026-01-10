<?php
// app/Helpers/AuthHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Seller;

/**
 * Helper Otentikasi
 *
 * Kelas helper ini menyediakan fungsi-fungsi bantuan untuk otentikasi
 * dan manajemen peran pengguna dalam sistem e-commerce AKRAB.
 */
class AuthHelper
{
    /**
     * Mendapatkan role dari user saat ini
     *
     * @return string Peran pengguna saat ini (admin, seller, customer, atau guest)
     */
    public static function getUserRole()
    {
        if (!Auth::check()) {
            return 'guest';
        }

        $user = Auth::user();

        // Gunakan sistem role yang konsisten dengan middleware
        $roleName = $user->getRoleName();

        switch($roleName) {
            case 'admin':
                return 'admin';
            case 'seller':
                return 'seller';
            case 'buyer': // dalam sistem ini role customer disebut buyer
                return 'customer';
            default:
                return 'guest';
        }
    }

    /**
     * Cek apakah user adalah admin
     *
     * @param mixed $user Objek pengguna (jika tidak disediakan, akan menggunakan pengguna saat ini)
     * @return bool True jika pengguna adalah admin, false jika tidak
     */
    public static function isAdmin($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Gunakan sistem role yang konsisten
        return $user->role && $user->role->name === 'admin';
    }

    /**
     * Cek apakah user adalah penjual
     *
     * @param mixed $user Objek pengguna (jika tidak disediakan, akan menggunakan pengguna saat ini)
     * @return bool True jika pengguna adalah penjual, false jika tidak
     */
    public static function isSeller($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Gunakan sistem role yang konsisten
        return $user->role && $user->role->name === 'seller';
    }

    /**
     * Cek apakah user adalah customer
     *
     * @param mixed $user Objek pengguna (jika tidak disediakan, akan menggunakan pengguna saat ini)
     * @return bool True jika pengguna adalah customer, false jika tidak
     */
    public static function isCustomer($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Customer adalah user dengan role buyer
        return $user->role && $user->role->name === 'buyer';
    }
}