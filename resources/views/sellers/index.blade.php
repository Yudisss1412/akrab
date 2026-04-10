@extends('layouts.admin')

@section('title', 'Manajemen Penjual - AKRAB')

@include('components.admin_penjual.header')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    /* Enhanced styles for seller management page */
    :root {
      --primary: #006E5C;
      --primary-light: #a8d5c9;
      --secondary: #6c757d;
      --success: #28a745;
      --danger: #dc3545;
      --warning: #ffc107;
      --info: #17a2b8;
      --light: #f8f9fa;
      --dark: #343a40;
      --white: #ffffff;
      --gray: #6c757d;
      --border: #dee2e6;
      --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      --border-radius: 0.375rem;
      --transition: all 0.3s ease;
    }

    body {
      background-color: #f5f7fa;
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }

    .main-content {
      padding-top: 1rem;
      padding-bottom: 1rem;
      min-height: auto !important;
      height: auto !important;
      flex: none !important;
    }

    .admin-page-content {
      padding: 1rem !important;
      min-height: auto !important;
      height: auto !important;
      flex: none !important;
    }

    .content-wrapper {
      min-height: auto !important;
      height: auto !important;
    }

    .table-container {
      min-height: auto !important;
      height: auto !important;
      margin-bottom: 1rem;
    }

    .table-content {
      min-height: auto !important;
      height: auto !important;
    }

    .table-responsive {
      min-height: auto !important;
      height: auto !important;
    }

    .pagination-wrapper,
    .mt-3.d-flex {
      margin-top: 1rem !important;
      padding-top: 0 !important;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
      padding: 0.25rem 0;
    }

    .page-title {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .page-title i {
      color: var(--primary-light);
    }

    .filter-panel {
      background: var(--white);
      border-radius: var(--border-radius);
      border: none;
      box-shadow: var(--box-shadow);
      margin-bottom: 1.5rem;
      overflow: hidden;
      transition: var(--transition);
    }

    .filter-panel:hover {
      box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    .custom-notification {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      opacity: 0;
      transform: translateX(100%);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      max-width: 350px;
    }

    .custom-notification.show {
      opacity: 1;
      transform: translateX(0);
    }

    .custom-notification .alert {
      margin-bottom: 0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border: none;
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .custom-notification .alert .alert-content {
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 1;
    }

    .custom-notification .alert .alert-icon {
      font-size: 1.2rem;
    }

    .custom-notification .alert .alert-message {
      flex: 1;
    }

    .confirm-danger {
      background-color: #dc3545 !important;
      border-color: #dc3545 !important;
      color: white !important;
      transition: all 0.2s ease;
    }

    .confirm-danger:hover {
      background-color: #c82333 !important;
      border-color: #bd2130 !important;
      transform: translateY(-2px);
    }

    .filter-header {
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .filter-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .filter-content {
      padding: 1.5rem;
    }

    .form-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
      border: 1px solid var(--border);
      border-radius: var(--border-radius);
      padding: 0.65rem 0.75rem;
      transition: var(--transition);
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(0, 110, 92, 0.25);
      outline: 0;
    }

    .btn {
      border-radius: var(--border-radius);
      padding: 0.5rem 1rem;
      font-weight: 600;
      transition: var(--transition);
      border: none;
    }

    .btn-primary {
      background: var(--primary);
      border: 1px solid var(--primary);
    }

    .btn-primary:hover {
      background: #005a4a;
      border-color: #005a4a;
      transform: translateY(-2px);
    }

    .btn-secondary {
      background: var(--secondary);
      border: 1px solid var(--secondary);
    }

    .btn-secondary:hover {
      background: #5a6268;
      border-color: #545b62;
      transform: translateY(-2px);
    }

    .table-container {
      background: var(--white);
      border-radius: var(--border-radius);
      border: none;
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: var(--transition);
    }

    .table-container:hover {
      box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    .table-header {
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .table-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .table-content {
      padding: 1.5rem;
    }

    .table {
      margin-bottom: 0;
    }

    .table th {
      background-color: #f8f9fa;
      color: #495057;
      font-weight: 600;
      padding: 0.9rem 0.75rem;
      border-top: none;
    }

    .table td {
      padding: 0.9rem 0.75rem;
      vertical-align: middle;
    }

    .table-hover tbody tr:hover {
      background-color: rgba(0, 110, 92, 0.05);
    }

    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.02);
    }

    .badge {
      padding: 0.4em 0.7em;
      font-size: 0.8em;
      font-weight: 500;
      border-radius: 50rem;
    }

    .bulk-actions-toolbar {
      background: #e9f7ef;
      border-radius: var(--border-radius);
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-left: 4px solid var(--primary);
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .btn-group .btn {
      margin-right: 0.25rem;
    }

    .btn-group .btn:last-child {
      margin-right: 0;
    }

    .pagination {
      margin-bottom: 0;
    }

    .pagination .page-link {
      color: var(--primary);
      border-radius: var(--border-radius);
      margin: 0 0.1rem;
    }

    .pagination .page-item.active .page-link {
      background-color: var(--primary);
      border-color: var(--primary);
    }

    .card {
      box-shadow: var(--box-shadow);
      border: none;
      border-radius: var(--border-radius);
      transition: var(--transition);
    }

    .table-responsive {
      border-radius: var(--border-radius);
    }

    /* Responsive table adjustments */
    .table th, .table td {
      white-space: nowrap;
      vertical-align: top;
    }

    /* Adjust for smaller screens */
    @media (max-width: 768px) {
      .table th, .table td {
        padding: 0.5rem 0.25rem;
        font-size: 0.9rem;
      }

      .btn-group-vertical .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        margin-bottom: 0.1rem;
      }
    }

    /* Ensure actions column doesn't get too wide */
    .table td:last-child {
      min-width: 100px;
    }

    /* Improved table styling for better UX */
    .table th {
      background-color: #f8f9fa;
      color: #495057;
      font-weight: 600;
      border-top: none;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
    }

    .table td {
      vertical-align: top;
      padding: 0.75rem 0.5rem;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
      .table {
        font-size: 0.85rem;
      }

      .table th, .table td {
        padding: 0.5rem 0.25rem;
      }

      /* Stack table on very small screens */
      .table-responsive {
        overflow-x: auto;
      }

      /* Make links more touch-friendly */
      .btn-group .btn {
        padding: 0.3rem 0.4rem;
        font-size: 0.7rem;
        margin-bottom: 0.1rem;
        width: 100%;
      }
    }

    /* Make table rows more distinct */
    .table-hover tbody tr:hover {
      background-color: rgba(0, 110, 92, 0.05);
    }

    /* Better display for mobile */
    @media (max-width: 576px) {
      .table th, .table td {
        padding: 0.4rem 0.2rem;
        font-size: 0.8rem;
      }

      .table th:nth-child(1),
      .table td:nth-child(1) {
        min-width: 120px;
      }

      .btn-group .btn {
        padding: 0.25rem 0.35rem;
        font-size: 0.65rem;
        margin-bottom: 0.05rem;
        width: 100%;
      }
    }

    /* Styling for text elements */
    .font-weight-bold.text-primary:hover {
      text-decoration: underline;
    }

    /* Badge improvements */
    .badge {
      font-size: 0.75em;
    }

    /* Better spacing in table cells */
    td > div > * {
      margin-bottom: 0.25rem;
    }

    td > div > :last-child {
      margin-bottom: 0;
    }

        /* Consistent button sizing for all action buttons */
    .table .btn-group.d-block .btn,
    .table form button.btn,
    .table a.btn {
      min-width: 85px !important;
      height: 36px !important;
      padding: 0.2rem 0.3rem !important;
      text-align: center !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 0.25rem !important;
      white-space: nowrap !important;
      font-size: 0.8rem !important;
    }

    /* Better alignment for icon and text */
    .table .btn i {
      margin-right: 0.1rem;
    }

    /* Force all action buttons to be perfect squares with icon only */
    .table .square-btn,
    .table .btn.square-btn,
    .table form button.square-btn,
    .table a.square-btn {
      width: 32px !important;
      height: 32px !important;
      padding: 6px !important;
      text-align: center !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      white-space: nowrap !important;
      font-size: 12px !important;
      line-height: 1 !important;
      box-sizing: border-box !important;
      min-width: 32px !important;
      min-height: 32px !important;
      max-width: 32px !important;
      max-height: 32px !important;
      border-radius: 4px !important;
      margin: 0 !important;
      flex: 0 0 32px !important;
    }

    /* Override any conflicting Bootstrap styles with highest specificity */
    .table .btn.square-btn,
    .table button.square-btn,
    .table a.square-btn {
      width: 32px !important;
      height: 32px !important;
      padding: 6px !important;
      min-width: 32px !important;
      min-height: 32px !important;
      max-width: 32px !important;
      max-height: 32px !important;
    }

    /* Ensure form buttons specifically match other buttons */
    .table form button.square-btn {
      width: 32px !important;
      height: 32px !important;
      padding: 6px !important;
      min-width: 32px !important;
      min-height: 32px !important;
      max-width: 32px !important;
      max-height: 32px !important;
    }

    /* Icon sizing for icon-only buttons - force perfect centering */
    .table .btn i {
      font-size: 14px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      min-width: 16px !important;
      width: 16px !important;
      height: 16px !important;
      margin: 0 !important;
      padding: 0 !important;
      line-height: 1 !important;
    }

    /* Extra specificity for font awesome icons */
    .table .btn .fas,
    .table .btn .fa {
      font-size: 14px !important;
      width: 16px !important;
      height: 16px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }

    /* Fix container styling that might affect button sizing */
    .table .btn-group.d-block {
      display: block !important;
      width: auto !important;
      margin-bottom: 8px !important;
    }

    /* Add extra spacing between delete button and status buttons */
    .table .btn-group.d-block:first-child {
      margin-bottom: 10px !important;
    }

    .table .btn-group.d-block:last-child {
      margin-bottom: 0 !important;
    }

    /* Add spacing between individual buttons */
    .table .btn.square-btn {
      margin-bottom: 4px !important;
      transition: all 0.2s ease !important;
      transform: scale(1) !important;
    }

    .table .btn.square-btn:last-child {
      margin-bottom: 0 !important;
    }

    /* Consistent spacing between all buttons using modern gap property - highest specificity */
    .table td .btn-group.d-block,
    td .table .btn-group.d-block,
    .table .seller-table td .btn-group.d-block {
      display: flex !important;
      flex-direction: column !important;
      gap: 6px !important;
      padding: 2px 0 !important;
    }

    /* Remove individual button margins since we're using gap - highest specificity */
    .table td .btn.square-btn,
    td .table .btn.square-btn,
    .table .seller-table td .btn.square-btn {
      margin-bottom: 0 !important;
      margin: 0 !important;
    }

    /* Ensure all buttons have consistent styling - highest specificity */
    .table td .btn.square-btn,
    td .table .btn.square-btn,
    .table .seller-table td .btn.square-btn {
      width: 32px !important;
      height: 32px !important;
      padding: 6px !important;
      min-width: 32px !important;
      min-height: 32px !important;
      max-width: 32px !important;
      max-height: 32px !important;
    }

    /* Add hover animations */
    .table .btn.square-btn:hover {
      transform: scale(1.1) !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
      z-index: 10 !important;
      position: relative !important;
    }

    /* Specific hover effects for different button types */
    .table .btn-info.square-btn:hover {
      background-color: #005a4a !important;
      border-color: #005a4a !important;
    }

    .table .btn-primary.square-btn:hover {
      background-color: #005a9c !important;
      border-color: #005a9c !important;
    }

    .table .btn-danger.square-btn:hover {
      background-color: #bd2130 !important;
      border-color: #bd2130 !important;
    }

    .table .btn-success.square-btn:hover {
      background-color: #1e7e34 !important;
      border-color: #1e7e34 !important;
    }

    .table .btn-warning.square-btn:hover {
      background-color: #d39e00 !important;
      border-color: #d39e00 !important;
    }

    /* Specific icon styling within buttons */
    .table .btn i {
      font-size: 0.9em !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      min-width: 14px !important;
    }

    /* Ensure icon color matches text color for all buttons */
    .table .btn-info i {
      color: #28a745 !important; /* Green icon for info buttons (Lihat) */
    }

    .table .btn-primary {
      color: #fff !important; /* White text for primary buttons (Edit) */
    }

    .table .btn-primary i {
      color: #fff !important; /* White icon for primary buttons (Edit) */
    }

    .table .btn-danger {
      background-color: #dc3545 !important; /* Solid red background for danger buttons (Hapus) */
      border-color: #dc3545 !important; /* Solid red border for danger buttons (Hapus) */
      color: #fff !important; /* White text for danger buttons (Hapus) */
    }

    .table .btn-danger i {
      color: #fff !important; /* White icon for danger buttons (Hapus) */
    }

    .table .btn-success {
      background-color: #28a745 !important; /* Solid green background for success buttons (Aktifkan) */
      border-color: #28a745 !important; /* Solid green border for success buttons (Aktifkan) */
      color: #fff !important; /* White text for success buttons (Aktifkan) */
    }

    .table .btn-success i {
      color: #fff !important; /* White icon for success buttons (Aktifkan) */
    }

    /* Special case for warning button with dark text */
    .table .btn-warning.text-dark {
      background-color: #ffc107 !important; /* Solid yellow background for warning buttons (Tangguhkan) */
      border-color: #ffc107 !important; /* Solid yellow border for warning buttons (Tangguhkan) */
      color: #212529 !important; /* Dark text for warning buttons (Tangguhkan) */
    }

    .table .btn-warning.text-dark i {
      color: #212529 !important; /* Dark icon for warning buttons (Tangguhkan) */
    }

    /* Button styling for better visual consistency */
    .table .btn-sm {
      padding: 0.25rem 0.5rem !important;
      font-size: 0.85rem !important;
      line-height: 1.5 !important;
    }

    /* Fixed button height for consistency with higher specificity */
    .table .fixed-btn-height {
      min-width: 85px !important;
      height: 36px !important;
      padding: 0.25rem 0.5rem !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      text-align: center !important;
      white-space: nowrap !important;
    }

    /* Additional specificity for form buttons to ensure consistency */
    .table form .btn {
      min-width: 85px !important;
      height: 36px !important;
      padding: 0.25rem 0.5rem !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      text-align: center !important;
      white-space: nowrap !important;
    }

    /* Mobile adjustments for consistent button sizes */
    @media (max-width: 768px) {
      .table .fixed-btn-height,
      .table form .btn {
        min-width: 80px !important;
        height: 32px !important;
        padding: 0.2rem 0.4rem !important;
        font-size: 0.75rem !important;
      }
    }

    @media (max-width: 576px) {
      .table .fixed-btn-height,
      .table form .btn {
        min-width: 70px !important;
        height: 30px !important;
        padding: 0.15rem 0.3rem !important;
        font-size: 0.7rem !important;
      }
    }

    /* Additional specificity for form buttons */
    .table form .btn {
      min-width: 85px !important;
      height: 36px !important;
      padding: 0.25rem 0.5rem !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      text-align: center !important;
      white-space: nowrap !important;
    }

    /* Mobile adjustments for consistent button sizes */
    @media (max-width: 768px) {
      .table .fixed-btn-height,
      .table form .btn {
        min-width: 80px !important;
        height: 32px !important;
        padding: 0.2rem 0.4rem !important;
        font-size: 0.75rem !important;
      }
    }

    @media (max-width: 576px) {
      .table .fixed-btn-height,
      .table form .btn {
        min-width: 70px !important;
        height: 30px !important;
        padding: 0.15rem 0.3rem !important;
        font-size: 0.7rem !important;
      }
    }

    /* Enhance readability and accessibility */
    .table th {
      font-weight: 600;
      color: #343a40;
      border-bottom: 2px solid #dee2e6;
    }

    .table td {
      border-top: 1px solid #dee2e6;
      vertical-align: top;
    }

    /* Improve contrast for better accessibility */
    .table a {
      color: #006E5C;
      text-decoration: none;
    }

    .table a:hover {
      color: #005a4a;
      text-decoration: underline;
    }

    /* Better alignment for badges and status indicators */
    .table .badge {
      margin: 0.1rem 0;
    }

    /* Consistent spacing in data cells */
    .table td div {
      margin-bottom: 0.25rem;
    }

    .table td div:last-ch ild {
      margin-bottom: 0;
    }

    /* Enhance hover state for better UX */
    .table-hover tbody tr:hover {
      background-color: rgba(0, 110, 92, 0.08) !important;
    }

    /* Responsive text sizing */
    @media (max-width: 768px) {
      .table th, .table td {
        font-size: 0.85rem;
        padding: 0.5rem 0.25rem;
      }
    }

    @media (max-width: 576px) {
      .table th, .table td {
        font-size: 0.8rem;
        padding: 0.4rem 0.2rem;
      }
    }

    /* =========================================
       SMALL MOBILE FIXES (360px & 320px)
       ========================================= */
    @media (max-width: 360px) {
      .shopee-card {
        padding: 0.5rem !important;
      }

      .shopee-card-header {
        padding: 0.6rem !important;
      }

      .shopee-card-body {
        padding: 0.75rem 0.5rem !important;
      }

      .shopee-info-row {
        gap: 0.25rem !important;
      }

      .shopee-info-label {
        min-width: 60px !important;
      }

      .shopee-stats-bar {
        gap: 0.4rem !important;
        padding: 0.5rem !important;
      }

      .shopee-stat-num {
        font-size: 0.85rem !important;
      }

      .shopee-stat-label {
        font-size: 0.65rem !important;
      }

      .shopee-card-footer {
        padding: 0.6rem 0.5rem !important;
        gap: 0.4rem !important;
      }

      .shopee-action-btn {
        padding: 0.4rem 0.3rem !important;
        font-size: 0.7rem !important;
        gap: 0.2rem !important;
        min-width: 60px !important;
      }

      .status-pill {
        font-size: 0.65rem !important;
        padding: 0.2rem 0.5rem !important;
      }
    }

    @media (max-width: 320px) {
      .shopee-card {
        padding: 0.4rem !important;
      }

      .shopee-card-header {
        padding: 0.5rem !important;
        flex-wrap: wrap;
      }

      .shopee-store-name {
        font-size: 0.9rem !important;
        max-width: 60% !important;
      }

      .shopee-card-body {
        padding: 0.6rem 0.4rem !important;
      }

      .shopee-info-label {
        min-width: 55px !important;
        font-size: 0.75rem !important;
      }

      .shopee-info-value {
        font-size: 0.8rem !important;
      }

      .shopee-stats-bar {
        flex-wrap: wrap !important;
        gap: 0.3rem !important;
        padding: 0.4rem !important;
      }

      .shopee-stat-item {
        flex: 1 1 30% !important;
        min-width: 80px !important;
      }

      .shopee-stat-num {
        font-size: 0.8rem !important;
      }

      .shopee-stat-label {
        font-size: 0.6rem !important;
      }

      .shopee-card-footer {
        padding: 0.5rem 0.4rem !important;
        gap: 0.3rem !important;
        flex-wrap: wrap !important;
      }

      .shopee-action-btn {
        flex: 1 1 calc(50% - 0.3rem) !important;
        padding: 0.35rem 0.2rem !important;
        font-size: 0.65rem !important;
        min-width: 0 !important;
      }

      .status-pill {
        font-size: 0.6rem !important;
        padding: 0.15rem 0.4rem !important;
      }
    }
    .shopee-card {
        background-color: #ffffff !important;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        margin-bottom: 0.75rem;
        overflow: hidden;
        border: 1px solid #f0f0f0;
    }

    .shopee-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
    }

    .shopee-store-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
        min-width: 0;
    }

    .shopee-store-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: #222222 !important;
        text-decoration: none;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
        max-width: 70%;
    }

    .shopee-store-name:hover {
        color: #006E5C !important;
    }

    .shopee-card-body {
        padding: 1rem;
    }

    .shopee-info-row {
        display: flex;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        gap: 0.5rem;
    }

    .shopee-info-row:last-child {
        margin-bottom: 0;
    }

    .shopee-info-label {
        color: #757575 !important;
        min-width: 75px;
        font-weight: 500;
        flex-shrink: 0;
    }

    .shopee-info-value {
        color: #222222 !important;
        font-weight: 600;
        flex: 1;
        text-align: right;
        word-break: break-word;
        overflow-wrap: break-word;
    }

    .shopee-stats-bar {
        display: flex;
        gap: 1rem;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f0f0f0;
        font-size: 0.8rem;
    }

    .shopee-stat-item {
        flex: 1;
        text-align: center;
    }

    .shopee-stat-num {
        font-weight: 700;
        color: #006E5C !important;
        display: block;
    }

    .shopee-stat-label {
        color: #757575 !important;
        font-size: 0.75rem;
    }

    .shopee-card-footer {
        display: flex;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-top: 1px solid #f0f0f0;
        background: #fafafa;
        flex-wrap: wrap;
    }

    .shopee-action-btn {
        flex: 1;
        min-width: 70px;
        padding: 0.5rem;
        font-size: 0.8rem;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        background: #ffffff;
        color: #222222 !important;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        text-decoration: none;
        white-space: nowrap;
    }

    .shopee-action-btn:hover {
        background: #f8f9fa;
        border-color: #006E5C;
        color: #006E5C !important;
    }

    .shopee-action-btn.btn-primary {
        background: #006E5C;
        color: #ffffff !important;
        border-color: #006E5C;
    }

    .shopee-action-btn.btn-primary:hover {
        background: #005a4a;
    }

    .shopee-action-btn.btn-danger {
        color: #dc3545 !important;
        border-color: #dc3545;
    }

    .shopee-action-btn.btn-danger:hover {
        background: #dc3545;
        color: #ffffff !important;
    }

    /* Status Badges */
    .status-pill {
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-pill-active {
        background: #e6fffa;
        color: #006E5C !important;
    }

    .status-pill-suspended {
        background: #fee2e2;
        color: #dc3545 !important;
    }

    .status-pill-pending {
        background: #fef3c7;
        color: #92400e !important;
    }

    .status-pill-new {
        background: #e0f2fe;
        color: #0369a1 !important;
    }

    /* =========================================
       MOBILE RESPONSIVE - Simple & Effective
       ========================================= */
    
    @media (max-width: 768px) {
      /* Reduce padding */
      .main-content {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
      }

      /* Stack page header */
      .page-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .page-title {
        font-size: 1.4rem;
      }

      /* Stack tabs vertically */
      .nav-tabs {
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
      }

      .nav-tabs .nav-link {
        width: 100%;
        text-align: center;
        padding: 0.75rem;
      }

      /* Full-width buttons */
      .btn {
        width: 100%;
        margin-bottom: 0.5rem;
      }

      .d-flex.justify-content-between {
        flex-direction: column;
        gap: 0.75rem;
      }

      /* Stack bulk actions */
      .bulk-actions-toolbar .row {
        flex-direction: column;
      }

      .bulk-actions-toolbar .col-md-2,
      .bulk-actions-toolbar .col-md-3,
      .bulk-actions-toolbar .col-md-5 {
        width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
        margin-bottom: 0.75rem;
      }

      .bulk-actions-toolbar .text-end {
        text-align: left !important;
      }

      /* Stack filters */
      .filter-content .row {
        flex-direction: column;
      }

      .filter-content .col-md-5,
      .filter-content .col-md-2 {
        width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
        margin-bottom: 0.75rem;
      }

      .filter-content .form-control,
      .filter-content .form-select {
        width: 100%;
      }

      /* Table horizontal scroll */
      .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .table {
        min-width: 650px;
        font-size: 0.85rem;
      }

      .table th {
        font-size: 0.75rem;
        white-space: nowrap;
      }

      .table td {
        white-space: nowrap;
      }

      /* Keep action buttons small in table */
      .table .btn {
        width: auto !important;
        padding: 0.35rem 0.5rem;
        font-size: 0.75rem;
        margin-bottom: 0;
      }

      /* Pagination wrap */
      .pagination {
        flex-wrap: wrap;
      }
    }

    @media (max-width: 576px) {
      .page-title {
        font-size: 1.2rem;
      }

      .table {
        min-width: 600px;
        font-size: 0.8rem;
      }

      .table th {
        font-size: 0.7rem;
      }

      .table .btn {
        padding: 0.3rem 0.4rem;
        font-size: 0.7rem;
      }
    }

    @media (max-width: 400px) {
      .page-title {
        font-size: 1.1rem;
      }

      .table {
        min-width: 550px;
        font-size: 0.75rem;
      }

      .table th {
        font-size: 0.65rem;
      }

      .table .btn {
        padding: 0.25rem 0.35rem;
        font-size: 0.65rem;
      }
    }
    /* =========================================
       SMALL MOBILE FIXES (360px & 320px)
       ========================================= */
    @media (max-width: 360px) {
      .shopee-card { padding: 0.5rem !important; }
      .shopee-card-header { padding: 0.6rem !important; }
      .shopee-card-body { padding: 0.75rem 0.5rem !important; }
      .shopee-info-row { gap: 0.25rem !important; }
      .shopee-info-label { min-width: 60px !important; }
      .shopee-stats-bar { gap: 0.4rem !important; padding: 0.5rem !important; }
      .shopee-stat-num { font-size: 0.85rem !important; }
      .shopee-stat-label { font-size: 0.65rem !important; }
      .shopee-card-footer { padding: 0.6rem 0.5rem !important; gap: 0.4rem !important; }
      .shopee-action-btn { padding: 0.4rem 0.3rem !important; font-size: 0.7rem !important; gap: 0.2rem !important; min-width: 60px !important; }
      .status-pill { font-size: 0.65rem !important; padding: 0.2rem 0.5rem !important; }
    }

    @media (max-width: 320px) {
      .shopee-card { padding: 0.4rem !important; }
      .shopee-card-header { padding: 0.5rem !important; flex-wrap: wrap; }
      .shopee-store-name { font-size: 0.9rem !important; max-width: 60% !important; }
      .shopee-card-body { padding: 0.6rem 0.4rem !important; }
      .shopee-info-label { min-width: 55px !important; font-size: 0.75rem !important; }
      .shopee-info-value { font-size: 0.8rem !important; }
      .shopee-stats-bar { flex-wrap: wrap !important; gap: 0.3rem !important; padding: 0.4rem !important; }
      .shopee-stat-item { flex: 1 1 30% !important; min-width: 80px !important; }
      .shopee-stat-num { font-size: 0.8rem !important; }
      .shopee-stat-label { font-size: 0.6rem !important; }
      .shopee-card-footer { padding: 0.5rem 0.4rem !important; gap: 0.3rem !important; flex-wrap: wrap !important; }
      .shopee-action-btn { flex: 1 1 calc(50% - 0.3rem) !important; padding: 0.35rem 0.2rem !important; font-size: 0.65rem !important; min-width: 0 !important; }
      .status-pill { font-size: 0.6rem !important; padding: 0.15rem 0.4rem !important; }
    }
    /* =========================================
       FINAL FIX: FORCE HIDE GHOSTS & FLEXBOX
       ========================================= */
    .content-wrapper, .admin-page-content, .main-content, .main-layout {
        flex: none !important; 
        height: auto !important;
        min-height: 0 !important;
        margin-bottom: 1rem;
    }

    /* USIR HANTU TAB YANG LAGI NGGAK AKTIF */
    .tab-content > .tab-pane:not(.active),
    .tab-pane.fade:not(.show) {
        display: none !important;
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content main-content">
        <div class="page-header">
          <h2 class="page-title">Manajemen Pengguna</h2>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link {{ (isset($tab) && $tab === 'buyers') ? '' : 'active' }}" id="sellers-tab" data-bs-toggle="tab" data-bs-target="#sellers-tab-pane" type="button" role="tab">Penjual</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link {{ (isset($tab) && $tab === 'buyers') ? 'active' : '' }}" id="buyers-tab" data-bs-toggle="tab" data-bs-target="#buyers-tab-pane" type="button" role="tab">Pembeli</button>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="userTabsContent">
          <!-- Sellers Tab -->
          <div class="tab-pane fade {{ (isset($tab) && $tab === 'buyers') ? '' : 'show active' }}" id="sellers-tab-pane" role="tabpanel">
            <div class="mt-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('sellers.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Tambah Penjual Baru
                </a>
              </div>

              <!-- Bulk Actions Toolbar -->
              <div class="bulk-actions-toolbar mb-3">
                <form id="bulkActionForm" method="POST" action="{{ route('sellers.bulk_action') }}">
                  @csrf
                  <div class="row align-items-center">
                    <div class="col-md-2">
                      <input type="checkbox" id="selectAll" class="form-check-input">
                      <label for="selectAll" class="form-check-label ms-1">Pilih Semua</label>
                    </div>
                    <div class="col-md-3">
                      <select name="action" class="form-select" required>
                        <option value="">Pilih Aksi</option>
                        <option value="suspend">Tangguhkan</option>
                        <option value="activate">Aktifkan</option>
                        <option value="delete">Hapus</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin melakukan aksi ini pada penjual terpilih?')">Terapkan</button>
                    </div>
                    <div class="col-md-5 text-end">
                      <a href="{{ route('sellers.export_sellers') }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Ekspor Data
                      </a>
                    </div>
                  </div>
                  </div>
                  <input type="hidden" name="seller_ids" id="sellerIdsInput">
                </form>
              </div>

              <!-- Filter Panel - Horizontal Layout -->
              <div class="filter-panel">
                <div class="filter-header">
                  <h3 class="filter-title"><i class="fas fa-filter"></i> Filter dan Pencarian</h3>
                </div>
                <div class="filter-content">
                  <form id="filterForm" method="GET" action="{{ route('sellers.index') }}?tab=sellers">
                    <div class="row g-2">
                      <div class="col-md-5 mb-2">
                        <label for="search" class="form-label">Cari Penjual</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nama Toko, Nama Pemilik, atau Email">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                          <option value="">Semua</option>
                          @foreach($statusOptions as $value => $label)
                              <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                  {{ $label }}
                              </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="join_date_from" class="form-label">Bergabung Dari</label>
                        <input type="date" class="form-control" id="join_date_from" name="join_date_from" value="{{ request('join_date_from') }}">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="join_date_to" class="form-label">Bergabung Sampai</label>
                        <input type="date" class="form-control" id="join_date_to" name="join_date_to" value="{{ request('join_date_to') }}">
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- ==========================================
                   MOBILE: SHOPEE-STYLE CARDS (< 992px)
                   ========================================== -->
              <div class="d-lg-none">
                <h5 class="mb-3" style="font-weight: 600; color: #374151;">Daftar Penjual</h5>
                
                @forelse($sellers ?? collect() as $seller)
                <div class="shopee-card">
                    <!-- Header: Checkbox + Store Name + Status -->
                    <div class="shopee-card-header">
                        <div class="shopee-store-info">
                            <input type="checkbox" class="form-check-input seller-checkbox" value="{{ $seller->id }}" name="seller_ids[]" style="width: 1.1rem; height: 1.1rem; flex-shrink: 0;">
                            <a href="{{ route('sellers.show', $seller) }}" class="shopee-store-name">
                                {{ $seller->store_name }}
                            </a>
                        </div>
                        
                        @php
                            $pillClass = 'status-pill-pending';
                            $statusText = 'N/A';
                            switch($seller->status) {
                              case 'aktif': $pillClass = 'status-pill-active'; $statusText = 'Aktif'; break;
                              case 'ditangguhkan': $pillClass = 'status-pill-suspended'; $statusText = 'Ditangguhkan'; break;
                              case 'menunggu_verifikasi': $pillClass = 'status-pill-pending'; $statusText = 'Pending'; break;
                              case 'baru': $pillClass = 'status-pill-new'; $statusText = 'Baru'; break;
                            }
                        @endphp
                        <span class="status-pill {{ $pillClass }}">{{ $statusText }}</span>
                    </div>

                    <!-- Body: Owner Info -->
                    <div class="shopee-card-body">
                        <div class="shopee-info-row">
                            <span class="shopee-info-label">Pemilik</span>
                            <span class="shopee-info-value">{{ $seller->owner_name }}</span>
                        </div>
                        <div class="shopee-info-row">
                            <span class="shopee-info-label">Email</span>
                            <span class="shopee-info-value">{{ $seller->email }}</span>
                        </div>
                        <div class="shopee-info-row">
                            <span class="shopee-info-label">Bergabung</span>
                            <span class="shopee-info-value">{{ $seller->join_date ? $seller->join_date->format('d M Y') : '-' }}</span>
                        </div>

                        <!-- Stats Bar -->
                        <div class="shopee-stats-bar">
                            <div class="shopee-stat-item">
                                <span class="shopee-stat-num">{{ $seller->active_products_count }}</span>
                                <span class="shopee-stat-label">Produk</span>
                            </div>
                            <div class="shopee-stat-item">
                                <span class="shopee-stat-num">Rp{{ number_format($seller->total_sales, 2, ',', '.') }}</span>
                                <span class="shopee-stat-label">GMV</span>
                            </div>
                            <div class="shopee-stat-item">
                                <span class="shopee-stat-num">
                                    <i class="fas fa-star" style="color: #f59e0b;"></i> {{ number_format($seller->rating, 1) }}
                                </span>
                                <span class="shopee-stat-label">Rating</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer: Action Buttons -->
                    <div class="shopee-card-footer">
                        <a href="{{ route('sellers.show', $seller) }}" class="shopee-action-btn" title="Lihat">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                        <a href="{{ route('sellers.edit', $seller) }}" class="shopee-action-btn btn-primary" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        
                        @if($seller->status !== 'ditangguhkan')
                          <form action="{{ route('sellers.suspend', $seller) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="shopee-action-btn" style="color: #92400e !important; border-color: #f59e0b;" title="Tangguhkan">
                              <i class="fas fa-pause"></i> Tangguh
                            </button>
                          </form>
                        @else
                          <form action="{{ route('sellers.activate', $seller) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="shopee-action-btn" style="color: #006E5C !important; border-color: #006E5C;" title="Aktifkan">
                              <i class="fas fa-play"></i> Aktifkan
                            </button>
                          </form>
                        @endif

                        <form action="{{ route('sellers.destroy', $seller) }}" method="POST" class="flex-fill delete-seller-form">
                          @csrf @method('DELETE')
                          <button type="submit" class="shopee-action-btn btn-danger" title="Hapus">
                            <i class="fas fa-trash"></i> Hapus
                          </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-5" style="color: #6b7280;">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Tidak ada data penjual ditemukan.</p>
                </div>
                @endforelse
              </div>

              <!-- ==========================================
                   DESKTOP: TABLE LAYOUT (>= 992px)
                   ========================================== -->
              <div class="d-none d-lg-block table-container mt-2">
                <div class="table-header">
                  <h3 class="table-title">Daftar Penjual</h3>
                </div>
                <div class="table-content">
                  <div class="table-responsive">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" id="selectAllHeader" class="form-check-input">
                        </th>
                        <th scope="col">Nama Toko</th>
                        <th scope="col">Info Pemilik</th>
                        <th scope="col">Statistik</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($sellers ?? collect() as $seller)
                      <tr id="seller-row-{{ $seller->id }}">
                        <td>
                          <input type="checkbox" class="form-check-input seller-checkbox" value="{{ $seller->id }}" name="seller_ids[]">
                        </td>
                        <td>
                          <a href="{{ route('sellers.show', $seller) }}" class="font-weight-bold text-primary d-block">
                            {{ $seller->store_name }}
                          </a>
                          <small class="text-muted d-block">ID: {{ $seller->id }}</small>
                        </td>
                        <td>
                          <div><strong>{{ $seller->owner_name }}</strong></div>
                          <small class="text-muted">{{ $seller->email }}</small><br>
                          <small class="text-muted">Bergabung: {{ $seller->join_date ? $seller->join_date->format('d M Y') : '-' }}</small>
                        </td>
                        <td>
                          <div>Produk: <strong>{{ $seller->active_products_count }}</strong></div>
                          <div>GMV: <strong>Rp {{ number_format($seller->total_sales, 2, ',', '.') }}</strong></div>
                          <div>Rating:
                            <span class="badge bg-warning text-dark">
                              <i class="fas fa-star"></i> {{ number_format($seller->rating, 1) }}
                            </span>
                          </div>
                        </td>
                        <td>
                          @php
                            $statusClass = '';
                            $statusText = '';
                            switch($seller->status) {
                              case 'aktif':
                                $statusClass = 'bg-success';
                                $statusText = 'Aktif';
                                break;
                              case 'ditangguhkan':
                                $statusClass = 'bg-danger';
                                $statusText = 'Ditangguhkan';
                                break;
                              case 'menunggu_verifikasi':
                                $statusClass = 'bg-warning text-dark';
                                $statusText = 'Menunggu Verifikasi';
                                break;
                              case 'baru':
                                $statusClass = 'bg-info';
                                $statusText = 'Baru';
                                break;
                              default:
                                $statusClass = 'bg-secondary';
                                $statusText = 'Tidak Dikenal';
                            }
                          @endphp
                          <span class="badge rounded-pill {{ $statusClass }} d-block">{{ $statusText }}</span>
                        </td>
                        <td>
                          <div class="btn-group d-block" role="group">
                            <a href="{{ route('sellers.show', $seller) }}" class="btn btn-info d-block text-center square-btn" title="Lihat Detail">
                              <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('sellers.edit', $seller) }}" class="btn btn-primary d-block text-center square-btn" title="Edit">
                              <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('sellers.destroy', $seller) }}" method="POST" class="d-inline delete-seller-form">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger d-block text-center square-btn" title="Hapus">
                                <i class="fas fa-trash"></i>
                              </button>
                            </form>
                            @if($seller->status !== 'ditangguhkan')
                              <form action="{{ route('sellers.suspend', $seller) }}" method="POST" class="d-inline suspend-seller-form">
                                @csrf
                                <button type="submit" class="btn btn-warning text-dark d-block text-center square-btn" title="Tangguhkan">
                                  <i class="fas fa-pause"></i>
                                </button>
                              </form>
                            @else
                              <form action="{{ route('sellers.activate', $seller) }}" method="POST" class="d-inline activate-seller-form">
                                @csrf
                                <button type="submit" class="btn btn-success d-block text-center square-btn" title="Aktifkan">
                                  <i class="fas fa-play"></i>
                                </button>
                              </form>
                            @endif
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data penjual ditemukan.</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>

                <!-- Pagination -->
                <div class="mt-3 d-flex justify-content-between align-items-center">
                  @if($sellers)
                  <div>
                    Menampilkan {{ $sellers->firstItem() }} sampai {{ $sellers->lastItem() }}
                    dari {{ $sellers->total() }} penjual
                  </div>
                  <nav aria-label="Halaman penjual">
                    <ul class="pagination mb-0">
                      @if ($sellers->onFirstPage())
                        <li class="page-item disabled">
                          <span class="page-link">&laquo;&laquo; Sebelumnya</span>
                        </li>
                      @else
                        <li class="page-item">
                          <a class="page-link" href="{{ $sellers->previousPageUrl() }}&tab=sellers" aria-label="Previous">
                            <span aria-hidden="true">&laquo;&laquo; Sebelumnya</span>
                          </a>
                        </li>
                      @endif

                      @for ($i = max(1, $sellers->currentPage() - 2); $i <= min($sellers->lastPage(), $sellers->currentPage() + 2); $i++)
                        @if ($i == $sellers->currentPage())
                          <li class="page-item active"><span class="page-link" style="color: white;">{{ $i }}</span></li>
                        @else
                          <li class="page-item"><a class="page-link" href="{{ $sellers->url($i) }}&tab=sellers">{{ $i }}</a></li>
                        @endif
                      @endfor

                      @if ($sellers->hasMorePages())
                        <li class="page-item">
                          <a class="page-link" href="{{ $sellers->nextPageUrl() }}&tab=sellers" aria-label="Next">
                            <span aria-hidden="true">Berikutnya &raquo;&raquo;</span>
                          </a>
                        </li>
                      @else
                        <li class="page-item disabled">
                          <span class="page-link">Berikutnya &raquo;&raquo;</span>
                        </li>
                      @endif
                    </ul>
                  </nav>
                  @else
                  <div>Tidak ada data penjual</div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- Buyers Tab -->
          <div class="tab-pane fade {{ (isset($tab) && $tab === 'buyers') ? 'show active' : '' }}" id="buyers-tab-pane" role="tabpanel">
            <div class="mt-3">
              <!-- Bulk Actions Toolbar for Buyers -->
              <div class="bulk-actions-toolbar mb-3">
                <form id="bulkActionFormBuyer" method="POST" action="{{ route('sellers.bulk_action') }}">
                  @csrf
                  <input type="hidden" name="user_type" value="buyer">
                  <div class="row align-items-center">
                    <div class="col-md-2">
                      <input type="checkbox" id="selectAllBuyers" class="form-check-input">
                      <label for="selectAllBuyers" class="form-check-label ms-1">Pilih Semua</label>
                    </div>
                    <div class="col-md-3">
                      <select name="action" class="form-select" required>
                        <option value="">Pilih Aksi</option>
                        <option value="suspend">Tangguhkan</option>
                        <option value="activate">Aktifkan</option>
                        <option value="delete">Hapus</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin melakukan aksi ini pada pembeli terpilih?')">Terapkan</button>
                    </div>
                    <div class="col-md-5 text-end">
                      <a href="{{ route('sellers.export_buyers') }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Ekspor Data
                      </a>
                    </div>
                  </div>
                  <input type="hidden" name="user_ids" id="userIdsInput">
                </form>
              </div>

              <!-- Filter Panel for Buyers -->
              <div class="filter-panel">
                <div class="filter-header">
                  <h3 class="filter-title"><i class="fas fa-filter"></i> Filter dan Pencarian</h3>
                </div>
                <div class="filter-content">
                  <form id="filterFormBuyers" method="GET" action="{{ route('sellers.index') }}?tab=buyers">
                    <div class="row g-2">
                      <div class="col-md-5 mb-2">
                        <label for="buyer_search" class="form-label">Cari Pembeli</label>
                        <input type="text" class="form-control" id="buyer_search" name="search" value="{{ request('search') }}" placeholder="Nama Pembeli, Email">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="buyer_status" class="form-label">Status</label>
                        <select class="form-select" id="buyer_status" name="status">
                          <option value="">Semua</option>
                          <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                          <option value="ditangguhkan" {{ request('status') === 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="join_date_from_buyer" class="form-label">Bergabung Dari</label>
                        <input type="date" class="form-control" id="join_date_from_buyer" name="join_date_from" value="{{ request('join_date_from') }}">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="join_date_to_buyer" class="form-label">Bergabung Sampai</label>
                        <input type="date" class="form-control" id="join_date_to_buyer" name="join_date_to" value="{{ request('join_date_to') }}">
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- ==========================================
                   MOBILE: SHOPEE-STYLE CARDS (< 992px)
                   ========================================== -->
              <div class="d-lg-none">
                <h5 class="mb-3" style="font-weight: 600; color: #374151;">Daftar Pembeli</h5>
                
                @forelse($buyers ?? collect() as $buyer)
                <div class="shopee-card">
                    <!-- Header: Checkbox + Name + Status -->
                    <div class="shopee-card-header">
                        <div class="shopee-store-info">
                            <input type="checkbox" class="form-check-input buyer-checkbox" value="{{ $buyer->id }}" name="user_ids[]" style="width: 1.1rem; height: 1.1rem; flex-shrink: 0;">
                            <a href="{{ route('sellers.edit_user', $buyer) }}" class="shopee-store-name">
                                {{ $buyer->name }}
                            </a>
                        </div>
                        
                        @php
                            $pillClass = $buyer->status === 'suspended' ? 'status-pill-suspended' : 'status-pill-active';
                            $statusText = $buyer->status === 'suspended' ? 'Ditangguhkan' : 'Aktif';
                        @endphp
                        <span class="status-pill {{ $pillClass }}">{{ $statusText }}</span>
                    </div>

                    <!-- Body: User Info -->
                    <div class="shopee-card-body">
                        <div class="shopee-info-row">
                            <span class="shopee-info-label">Email</span>
                            <span class="shopee-info-value">{{ $buyer->email }}</span>
                        </div>
                        <div class="shopee-info-row">
                            <span class="shopee-info-label">Bergabung</span>
                            <span class="shopee-info-value">{{ $buyer->created_at ? $buyer->created_at->format('d M Y') : '-' }}</span>
                        </div>

                        <!-- Stats Bar -->
                        <div class="shopee-stats-bar">
                            <div class="shopee-stat-item">
                                <span class="shopee-stat-num">
                                  @php
                                    $totalOrders = isset($buyer->orders) ? $buyer->orders->count() : $buyer->orders()->count();
                                  @endphp
                                  {{ $totalOrders }}
                                </span>
                                <span class="shopee-stat-label">Transaksi</span>
                            </div>
                            <div class="shopee-stat-item">
                                <span class="shopee-stat-num">
                                  @php
                                    $totalSpending = isset($buyer->orders) ? $buyer->orders->sum('total_amount') : $buyer->orders()->sum('total_amount');
                                  @endphp
                                  Rp{{ number_format($totalSpending, 0, ',', '.') }}
                                </span>
                                <span class="shopee-stat-label">Spending</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer: Action Buttons -->
                    <div class="shopee-card-footer">
                        <a href="{{ route('sellers.user_history', $buyer) }}" class="shopee-action-btn" title="Riwayat">
                            <i class="fas fa-history"></i> Riwayat
                        </a>
                        <a href="{{ route('sellers.edit_user', $buyer) }}" class="shopee-action-btn btn-primary" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        
                        @if($buyer->status === 'suspended')
                          <form action="{{ route('sellers.activate_user', $buyer) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="shopee-action-btn" style="color: #006E5C !important; border-color: #006E5C;" title="Aktifkan">
                              <i class="fas fa-play"></i> Aktifkan
                            </button>
                          </form>
                        @else
                          <form action="{{ route('sellers.suspend_user', $buyer) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="shopee-action-btn" style="color: #92400e !important; border-color: #f59e0b;" title="Tangguhkan">
                              <i class="fas fa-pause"></i> Tangguh
                            </button>
                          </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-5" style="color: #6b7280;">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>Tidak ada data pembeli ditemukan.</p>
                </div>
                @endforelse
              </div>

              <!-- ==========================================
                   DESKTOP: TABLE LAYOUT (>= 992px)
                   ========================================== -->
              <div class="d-none d-lg-block table-container mt-2">
                <div class="table-header">
                  <h3 class="table-title">Daftar Pembeli</h3>
                </div>
                <div class="table-content">
                  <div class="table-responsive">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" id="selectAllBuyersHeader" class="form-check-input">
                        </th>
                        <th scope="col">Info Pengguna</th>
                        <th scope="col">Statistik</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($buyers ?? collect() as $buyer)
                      <tr id="user-row-{{ $buyer->id }}">
                        <td>
                          <input type="checkbox" class="form-check-input buyer-checkbox" value="{{ $buyer->id }}" name="user_ids[]">
                        </td>
                        <td>
                          <div><strong>{{ $buyer->name }}</strong></div>
                          <small class="text-muted">{{ $buyer->email }}</small><br>
                          <small class="text-muted">Bergabung: {{ $buyer->created_at ? $buyer->created_at->format('d M Y') : '-' }}</small>
                        </td>
                        <td>
                          @php
                            // Hitung total pembelian dari order pengguna
                            $totalOrders = isset($buyer->orders) ? $buyer->orders->count() : $buyer->orders()->count();
                            $totalSpending = isset($buyer->orders) ? $buyer->orders->sum('total_amount') : $buyer->orders()->sum('total_amount');
                          @endphp
                          <div>Transaksi: <strong>{{ $totalOrders }}</strong></div>
                          <div>Spending: <strong>Rp {{ number_format($totalSpending, 0, ',', '.') }}</strong></div>
                        </td>
                        <td>
                          @php
                            $statusClass = $buyer->status === 'suspended' ? 'bg-danger' : 'bg-success';
                            $statusText = $buyer->status === 'suspended' ? 'Ditangguhkan' : 'Aktif';
                          @endphp
                          <span class="badge rounded-pill {{ $statusClass }} d-block">{{ $statusText }}</span>
                        </td>
                        <td>
                          <div class="btn-group d-block" role="group">
                            <a href="{{ route('sellers.user_history', $buyer) }}" class="btn btn-info d-block text-center square-btn" title="Lihat Riwayat Transaksi">
                              <i class="fas fa-history"></i>
                            </a>
                            <a href="{{ route('sellers.edit_user', $buyer) }}" class="btn btn-primary d-block text-center square-btn" title="Edit Profil">
                              <i class="fas fa-edit"></i>
                            </a>
                            @if($buyer->status === 'suspended')
                              <form action="{{ route('sellers.activate_user', $buyer) }}" method="POST" class="d-inline activate-user-form">
                                @csrf
                                <button type="submit" class="btn btn-success d-block text-center square-btn" title="Aktifkan Kembali Akun">
                                  <i class="fas fa-play"></i>
                                </button>
                              </form>
                            @else
                              <form action="{{ route('sellers.suspend_user', $buyer) }}" method="POST" class="d-inline suspend-user-form">
                                @csrf
                                <button type="submit" class="btn btn-warning text-dark d-block text-center square-btn" title="Tangguhkan Akun">
                                  <i class="fas fa-pause"></i>
                                </button>
                              </form>
                            @endif
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="5" class="text-center py-4">Tidak ada data pembeli ditemukan.</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                  </div>
              </div>

                  <!-- Pagination for buyers -->
                  <div class="mt-3 d-flex justify-content-between align-items-center">
                    @if($buyers)
                    <div>
                      Menampilkan {{ $buyers->firstItem() }} sampai {{ $buyers->lastItem() }}
                      dari {{ $buyers->total() }} pembeli
                    </div>
                    <nav aria-label="Halaman pembeli">
                      <ul class="pagination mb-0">
                        @if ($buyers->onFirstPage())
                          <li class="page-item disabled">
                            <span class="page-link">&laquo;&laquo; Sebelumnya</span>
                          </li>
                        @else
                          <li class="page-item">
                            <a class="page-link" href="{{ $buyers->previousPageUrl() }}&tab=buyers" aria-label="Previous">
                              <span aria-hidden="true">&laquo;&laquo; Sebelumnya</span>
                            </a>
                          </li>
                        @endif

                        @for ($i = max(1, $buyers->currentPage() - 2); $i <= min($buyers->lastPage(), $buyers->currentPage() + 2); $i++)
                          @if ($i == $buyers->currentPage())
                            <li class="page-item active"><span class="page-link" style="color: white;">{{ $i }}</span></li>
                          @else
                            <li class="page-item"><a class="page-link" href="{{ $buyers->url($i) }}&tab=buyers">{{ $i }}</a></li>
                          @endif
                        @endfor

                        @if ($buyers->hasMorePages())
                          <li class="page-item">
                            <a class="page-link" href="{{ $buyers->nextPageUrl() }}&tab=buyers" aria-label="Next">
                              <span aria-hidden="true">Berikutnya &raquo;&raquo;</span>
                            </a>
                          </li>
                        @else
                          <li class="page-item disabled">
                            <span class="page-link">Berikutnya &raquo;&raquo;</span>
                          </li>
                        @endif
                      </ul>
                    </nav>
                    @else
                    <div>Tidak ada data pembeli</div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <!-- JavaScript for Select All functionality, Bulk Actions, and Auto-filtering -->
  <script>
  function submitForm() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Build URL with parameters
    let url = new URL(window.location.href);
    url.search = '';

    for (const [key, value] of formData.entries()) {
      if (value) {
        url.searchParams.append(key, value);
      }
    }

    // Ensure tab parameter is preserved
    url.searchParams.set('tab', 'sellers');

    // Navigate to the URL with parameters
    window.location.href = url.toString();
  }

  function clearFilters() {
    // Reset all filter form fields
    document.getElementById('search').value = '';
    document.getElementById('status').value = '';
    document.getElementById('join_date_from').value = '';
    document.getElementById('join_date_to').value = '';

    // Submit the form to refresh results
    submitForm();
  }

  function submitBuyerForm() {
    const form = document.getElementById('filterFormBuyers');
    const formData = new FormData(form);

    // Build URL with parameters
    let url = new URL(window.location.href);
    url.search = '';

    for (const [key, value] of formData.entries()) {
      if (value) {
        url.searchParams.append(key, value);
      }
    }

    // Ensure tab parameter is preserved
    url.searchParams.set('tab', 'buyers');

    // Navigate to the URL with parameters
    window.location.href = url.toString();
  }

  function clearBuyerFilters() {
    // Reset all filter form fields
    document.getElementById('buyer_search').value = '';
    document.getElementById('buyer_status').value = '';
    document.getElementById('join_date_from_buyer').value = '';
    document.getElementById('join_date_to_buyer').value = '';

    // Submit the form to refresh results
    submitBuyerForm();
  }

  // Auto-filter functionality for sellers
  function updateSellerFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Build URL with parameters
    let url = new URL(window.location.href);
    url.search = '';

    for (const [key, value] of formData.entries()) {
      if (value) {
        url.searchParams.append(key, value);
      }
    }

    // Ensure tab parameter is preserved
    url.searchParams.set('tab', 'sellers');

    // Navigate to the URL with parameters
    window.location.href = url.toString();
  }

  // Auto-filter functionality for buyers
  function updateBuyerFilters() {
    const form = document.getElementById('filterFormBuyers');
    const formData = new FormData(form);

    // Build URL with parameters
    let url = new URL(window.location.href);
    url.search = '';

    for (const [key, value] of formData.entries()) {
      if (value) {
        url.searchParams.append(key, value);
      }
    }

    // Ensure tab parameter is preserved
    url.searchParams.set('tab', 'buyers');

    // Navigate to the URL with parameters
    window.location.href = url.toString();
  }

  // Add event listeners to filter elements for automatic filtering
  document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality for sellers
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const sellerCheckboxes = document.querySelectorAll('.seller-checkbox');

  // Handle tab switching to update URL and ensure correct tab is active
  document.addEventListener('DOMContentLoaded', function() {
      // Add click event listeners to the tab buttons to update URL
      const buyersTab = document.getElementById('buyers-tab');
      const sellersTab = document.getElementById('sellers-tab');

      if (buyersTab) {
          buyersTab.addEventListener('click', function(e) {
              // Update URL to include tab=buyers parameter
              const newUrl = new URL(window.location);
              newUrl.searchParams.set('tab', 'buyers');
              window.history.pushState({}, '', newUrl);
          });
      }

      if (sellersTab) {
          sellersTab.addEventListener('click', function(e) {
              // Update URL to include tab=sellers parameter (or remove tab param for default)
              const newUrl = new URL(window.location);
              newUrl.searchParams.set('tab', 'sellers');
              window.history.pushState({}, '', newUrl);
          });
      }

      // Handle browser back/forward buttons
      window.addEventListener('popstate', function(e) {
          const urlParams = new URLSearchParams(window.location.search);
          const tab = urlParams.get('tab');

          if (tab === 'buyers') {
              const buyersTabEl = document.getElementById('buyers-tab');
              if (buyersTabEl) {
                  bootstrap.Tab.getInstance(buyersTabEl)?.show() || new bootstrap.Tab(buyersTabEl).show();
              }
          } else {
              const sellersTabEl = document.getElementById('sellers-tab');
              if (sellersTabEl) {
                  bootstrap.Tab.getInstance(sellersTabEl)?.show() || new bootstrap.Tab(sellersTabEl).show();
              }
          }
      });
  });

    // Add event listeners to seller filter elements for automatic filtering
    const sellerSearch = document.getElementById('search');
    const sellerStatus = document.getElementById('status');
    const sellerJoinDateFrom = document.getElementById('join_date_from');
    const sellerJoinDateTo = document.getElementById('join_date_to');

    if (sellerSearch) {
      sellerSearch.addEventListener('input', function() {
        // Use a debounce to avoid too many requests while typing
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
          updateSellerFilters();
        }, 300);
      });
    }

    if (sellerStatus) {
      sellerStatus.addEventListener('change', updateSellerFilters);
    }

    if (sellerJoinDateFrom) {
      sellerJoinDateFrom.addEventListener('change', updateSellerFilters);
    }

    if (sellerJoinDateTo) {
      sellerJoinDateTo.addEventListener('change', updateSellerFilters);
    }

    // Add event listeners to buyer filter elements for automatic filtering
    const buyerSearch = document.getElementById('buyer_search');
    const buyerStatus = document.getElementById('buyer_status');
    const buyerJoinDateFrom = document.getElementById('join_date_from_buyer');
    const buyerJoinDateTo = document.getElementById('join_date_to_buyer');

    if (buyerSearch) {
      buyerSearch.addEventListener('input', function() {
        // Use a debounce to avoid too many requests while typing
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
          updateBuyerFilters();
        }, 300);
      });
    }

    if (buyerStatus) {
      buyerStatus.addEventListener('change', updateBuyerFilters);
    }

    if (buyerJoinDateFrom) {
      buyerJoinDateFrom.addEventListener('change', updateBuyerFilters);
    }

    if (buyerJoinDateTo) {
      buyerJoinDateTo.addEventListener('change', updateBuyerFilters);
    }

    // Select all functionality for sellers
    selectAll && selectAll.addEventListener('change', function() {
      sellerCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });

    selectAllHeader && selectAllHeader.addEventListener('change', function() {
      sellerCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      selectAll.checked = this.checked;
    });

    // Select all functionality for buyers
    const selectAllBuyers = document.getElementById('selectAllBuyers');
    const selectAllBuyersHeader = document.getElementById('selectAllBuyersHeader');
    const buyerCheckboxes = document.querySelectorAll('.buyer-checkbox');

    selectAllBuyers && selectAllBuyers.addEventListener('change', function() {
      buyerCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });

    selectAllBuyersHeader && selectAllBuyersHeader.addEventListener('change', function() {
      buyerCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      selectAllBuyers.checked = this.checked;
    });

    // Custom notification function
    function showNotification(message, type = 'info') {
      // Remove any existing notifications
      const existingNotification = document.querySelector('.custom-notification');
      if (existingNotification) {
        existingNotification.remove();
      }

      // Determine icon based on type
      let icon = 'ℹ️';
      if (type === 'success') {
        icon = '✅';
      } else if (type === 'error') {
        icon = '❌';
      } else if (type === 'warning') {
        icon = '⚠️';
      } else if (type === 'info') {
        icon = 'ℹ️';
      }

      // Create notification container
      const notification = document.createElement('div');
      notification.className = 'custom-notification';
      notification.innerHTML =
        '<div class="alert alert-' + (type === 'error' ? 'danger' : type) + ' alert-dismissible fade show" role="alert">' +
          '<div class="alert-content">' +
            '<span class="alert-icon">' + icon + '</span>' +
            '<span class="alert-message">' + message + '</span>' +
          '</div>' +
          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';

      // Add to document
      document.body.appendChild(notification);

      // Trigger animation
      setTimeout(() => {
        notification.classList.add('show');
      }, 10);

      // Auto remove after 5 seconds
      setTimeout(() => {
        const alertElement = notification.querySelector('.alert');
        if (alertElement) {
          alertElement.classList.remove('show');
          setTimeout(() => {
            if (notification.parentNode) {
              notification.parentNode.removeChild(notification);
            }
          }, 150);
        }
      }, 5000);
    }

    // Custom confirm function
    function showConfirm(message, onConfirm) {
      // Remove any existing confirm modals
      const existingModal = document.querySelector('.custom-confirm-modal');
      if (existingModal) {
        existingModal.remove();
      }

      // Create modal backdrop
      const backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade';
      backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1050;';

      // Create modal container
      const modal = document.createElement('div');
      modal.className = 'custom-confirm-modal';
      modal.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1051;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        padding: 1.5rem;
        min-width: 300px;
        max-width: 400px;
      `;

      modal.innerHTML = `
        <div class="confirm-content">
          <div class="confirm-message" style="margin-bottom: 1rem;">${message}</div>
          <div class="confirm-buttons" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
            <button class="btn btn-secondary btn-sm cancel-btn">Batal</button>
            <button class="btn btn-danger btn-sm confirm-btn confirm-danger">Ya, Lanjutkan</button>
          </div>
        </div>
      `;

      // Add to document
      document.body.appendChild(backdrop);
      document.body.appendChild(modal);

      // Add event listeners
      const cancelBtn = modal.querySelector('.cancel-btn');
      const confirmBtn = modal.querySelector('.confirm-btn');

      const closeConfirm = () => {
        backdrop.classList.add('fade');
        modal.classList.add('fade');
        setTimeout(() => {
          if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
          if (modal.parentNode) modal.parentNode.removeChild(modal);
        }, 150);
      };

      cancelBtn.addEventListener('click', closeConfirm);
      backdrop.addEventListener('click', closeConfirm);

      confirmBtn.addEventListener('click', () => {
        closeConfirm();
        onConfirm();
      });
    }

    // Bulk actions for sellers
    const bulkActionForm = document.getElementById('bulkActionForm');
    if (bulkActionForm) {
      bulkActionForm.addEventListener('submit', function(e) {
        const selectedSellers = Array.from(sellerCheckboxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
        if (selectedSellers.length === 0) {
          e.preventDefault();
          showNotification('Silakan pilih setidaknya satu penjual terlebih dahulu.', 'warning');
          return;
        }
        document.getElementById('sellerIdsInput').value = JSON.stringify(selectedSellers);
      });
    }

    // Bulk actions for buyers
    const bulkActionFormBuyer = document.getElementById('bulkActionFormBuyer');
    if (bulkActionFormBuyer) {
      bulkActionFormBuyer.addEventListener('submit', function(e) {
        const selectedUsers = Array.from(buyerCheckboxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
        if (selectedUsers.length === 0) {
          e.preventDefault();
          showNotification('Silakan pilih setidaknya satu pembeli terlebih dahulu.', 'warning');
          return;
        }
        document.getElementById('userIdsInput').value = JSON.stringify(selectedUsers);
      });
    }

    // Event listeners for confirmation
    document.addEventListener('click', function(e) {
      // Handle bulk action forms
      if (e.target.closest('#bulkActionForm button[type="submit"]')) {
        e.preventDefault();
        const form = e.target.closest('#bulkActionForm');
        showConfirm('Apakah Anda yakin ingin melakukan aksi ini pada penjual terpilih?', function() {
          form.submit();
        });
      } else if (e.target.closest('#bulkActionFormBuyer button[type="submit"]')) {
        e.preventDefault();
        const form = e.target.closest('#bulkActionFormBuyer');
        showConfirm('Apakah Anda yakin ingin melakukan aksi ini pada pembeli terpilih?', function() {
          form.submit();
        });
      }
    });

    // Handle individual action forms
    document.addEventListener('submit', function(e) {
      // Handle suspend seller form
      if (e.target.classList.contains('suspend-seller-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin menangguhkan penjual ini?', function() {
          e.target.submit();
        });
      }
      // Handle activate seller form
      else if (e.target.classList.contains('activate-seller-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin mengaktifkan kembali penjual ini?', function() {
          e.target.submit();
        });
      }
      // Handle delete seller form
      else if (e.target.classList.contains('delete-seller-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin menghapus penjual ini?', function() {
          e.target.submit();
        });
      }
      // Handle suspend user form
      else if (e.target.classList.contains('suspend-user-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin menangguhkan pembeli ini?', function() {
          e.target.submit();
        });
      }
      // Handle activate user form
      else if (e.target.classList.contains('activate-user-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin mengaktifkan kembali pembeli ini?', function() {
          e.target.submit();
        });
      }
    });
  });


  </script>

@endsection
