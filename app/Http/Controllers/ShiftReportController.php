<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PosShift;
use Illuminate\Support\Facades\Auth;

class ShiftReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Kasir hanya melihat shift miliknya sendiri
        // Pemilik bisa melihat semua shift (opsional, tapi di req diminta untuk kasir)
        
        $query = PosShift::query();

        if (Auth::user()->hasRole('kasir')) {
            $query->where('user_id', Auth::id());
        }

        $shifts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pos.shifts.index', compact('shifts'));
    }
    
    // Bisa tambah method show() untuk detail shift jika perlu
}
