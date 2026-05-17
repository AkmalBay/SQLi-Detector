<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view reports');
    }

    public function index(Request $request)
    {
        // 1. Setup Tanggal Filter
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();

        // 2. Query Data dengan Pagination (15 baris per halaman)
        $transactions = Transaction::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(15);

        $totalRevenue = Transaction::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                                   ->sum('total_price');

        // 4. Hitung Breakdown Tunai vs Non-Tunai
        $transactionIds = Transaction::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                                     ->pluck('id');

        $totalCash = \App\Models\TransactionPayment::whereIn('transaction_id', $transactionIds)
                                    ->where('payment_method', 'CASH')
                                    ->sum('amount_paid');

        $totalNonCash = \App\Models\TransactionPayment::whereIn('transaction_id', $transactionIds)
                                    ->where('payment_method', '!=', 'CASH')
                                    ->sum('amount_paid');

        return view('reports.index', compact('transactions', 'totalRevenue', 'totalCash', 'totalNonCash', 'startDate', 'endDate'));
    }

    public function print(Request $request)
    {
        // --- LOGIKA BARU: CEK CHECKBOX ---

        if ($request->has('selected_ids') && is_array($request->input('selected_ids'))) {
            // A. JIKA ADA YANG DICENTANG (Cetak Pilihan)
            $selectedIds = $request->input('selected_ids');

            // Ambil data berdasarkan ID yang dipilih
            $transactions = Transaction::whereIn('id', $selectedIds)
                                       ->orderBy('created_at', 'desc')
                                       ->get();

            // Tanggal Header menyesuaikan data yang dipilih
            if ($transactions->count() > 0) {
                $startDate = $transactions->last()->created_at; // Data terlama
                $endDate   = $transactions->first()->created_at; // Data terbaru
            } else {
                $startDate = Carbon::now();
                $endDate   = Carbon::now();
            }

        } else {
            // B. JIKA TIDAK ADA YANG DICENTANG (Cetak per Tanggal / Default)
            $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
            $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();

            $transactions = Transaction::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Hitung Total dari data yang akan dicetak
        $totalRevenue = $transactions->sum('total_price');

        // Hitung Breakdown Tunai vs Non-Tunai
        $transIdsForPrint = $transactions->pluck('id');
        $totalCash = \App\Models\TransactionPayment::whereIn('transaction_id', $transIdsForPrint)
                                    ->where('payment_method', 'CASH')
                                    ->sum('amount_paid');

        $totalNonCash = \App\Models\TransactionPayment::whereIn('transaction_id', $transIdsForPrint)
                                    ->where('payment_method', '!=', 'CASH')
                                    ->sum('amount_paid');

        // Return ke View Print
        return view('reports.print', compact('transactions', 'totalRevenue', 'totalCash', 'totalNonCash', 'startDate', 'endDate'));
    }
}
