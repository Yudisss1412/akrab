<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AuthHelper;

class RoleDashboardController extends Controller
{
    public function showAdminDashboard()
    {
        // Middleware admin.role akan memastikan hanya admin yang bisa mengakses
        return view('admin.dashboard_admin');
    }

    public function showSellerDashboard()
    {
        // Middleware seller.role akan memastikan hanya penjual yang bisa mengakses
        return view('penjual.dashboard');
    }

    public function showCustomerDashboard()
    {
        // Redirect to login if customer dashboard view is not available
        return redirect('/login');
    }

    public function showRoleBasedDashboard()
    {
        // Menampilkan dashboard berdasarkan role user saat ini
        $role = AuthHelper::getUserRole();
        
        switch($role) {
            case 'admin':
                return view('admin.dashboard');
            case 'seller':
                return view('penjual.dashboard');
            case 'customer':
                return redirect('/login');
            default:
                return redirect()->route('cust.welcome');
        }
    }
}
