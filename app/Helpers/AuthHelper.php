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