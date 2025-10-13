<?php
// app/Helpers/AuthHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Seller;

class AuthHelper
{
    /**
     * Mendapatkan role dari user saat ini
     */
    public static function getUserRole()
    {
        if (!Auth::check()) {
            return 'guest';
        }
        
        $user = Auth::user();
        
        // Cek apakah user adalah admin
        if (self::isAdmin($user)) {
            return 'admin';
        }
        
        // Cek apakah user adalah penjual
        $seller = Seller::where('user_id', $user->id)->first();
        if ($seller) {
            return 'seller';
        }
        
        // Default sebagai customer
        return 'customer';
    }
    
    /**
     * Cek apakah user adalah admin
     */
    public static function isAdmin($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        
        if (!$user) {
            return false;
        }
        
        // Logika untuk menentukan apakah user adalah admin
        return $user->email === 'admin@akrab.test' || 
               str_contains($user->email, 'admin') || 
               $user->id === 1;
    }
    
    /**
     * Cek apakah user adalah penjual
     */
    public static function isSeller($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        
        if (!$user) {
            return false;
        }
        
        return Seller::where('user_id', $user->id)->exists();
    }
    
    /**
     * Cek apakah user adalah customer
     */
    public static function isCustomer($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        
        if (!$user) {
            return false;
        }
        
        // Customer adalah user yang terautentikasi tetapi bukan penjual dan bukan admin
        return !self::isSeller($user) && !self::isAdmin($user);
    }
}