<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Dashboard DUMMY. Data yang ditampilkan (jumlah aset, karyawan, aktivitas, dll)
 * masih hardcoded di view, belum diambil dari database.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard');
    }
}