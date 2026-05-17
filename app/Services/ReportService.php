<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\PosShift;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get sales report within a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getSalesReport(string $startDate, string $endDate): array
    {
        $transactions = Transaction::with(['items.product', 'user', 'posShift'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $transactions->sum('total_amount');
        $totalItems = $transactions->sum(function ($t) {
            return $t->items->sum('quantity');
        });

        return [
            'transactions' => $transactions,
            'total_revenue' => $totalRevenue,
            'total_items_sold' => $totalItems,
            'transaction_count' => $transactions->count(),
        ];
    }

    /**
     * Get shift report with payment breakdown.
     *
     * @param PosShift $shift
     * @return array
     */
    public function getShiftReport(PosShift $shift): array
    {
        $transactions = Transaction::where('pos_shift_id', $shift->id)->get();

        $cashPayments = TransactionPayment::where('pos_shift_id', $shift->id)
            ->where('payment_method', 'CASH')
            ->sum('amount_paid');

        $nonCashPayments = TransactionPayment::where('pos_shift_id', $shift->id)
            ->where('payment_method', '!=', 'CASH')
            ->sum('amount_paid');

        $paymentBreakdown = TransactionPayment::where('pos_shift_id', $shift->id)
            ->select('payment_method', DB::raw('SUM(amount_paid) as total'))
            ->groupBy('payment_method')
            ->get()
            ->pluck('total', 'payment_method');

        return [
            'shift' => $shift,
            'transactions' => $transactions,
            'cash_payments' => $cashPayments,
            'non_cash_payments' => $nonCashPayments,
            'payment_breakdown' => $paymentBreakdown,
            'total_transactions' => $transactions->count(),
            'total_revenue' => $transactions->sum('total_amount'),
        ];
    }

    /**
     * Get product sales report.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int|null $limit
     * @return \Illuminate\Support\Collection
     */
    public function getProductSalesReport(string $startDate, string $endDate, ?int $limit = null)
    {
        $query = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->select([
                'products.id',
                'products.sku',
                'products.name',
                DB::raw('SUM(transaction_items.quantity) as total_sold'),
                DB::raw('SUM(transaction_items.quantity * transaction_items.price) as total_revenue'),
            ])
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.sku', 'products.name')
            ->orderByDesc('total_sold');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get daily sales summary.
     *
     * @param int $days
     * @return \Illuminate\Support\Collection
     */
    public function getDailySalesSummary(int $days = 30)
    {
        return Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get cashier performance report.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getCashierPerformance(string $startDate, string $endDate)
    {
        return Transaction::join('users', 'transactions.user_id', '=', 'users.id')
            ->select([
                'users.id',
                'users.name',
                DB::raw('COUNT(transactions.id) as transaction_count'),
                DB::raw('SUM(transactions.total_amount) as total_revenue'),
            ])
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    /**
     * Get category sales report.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getCategorySalesReport(string $startDate, string $endDate)
    {
        return DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->select([
                'categories.id',
                'categories.name',
                DB::raw('SUM(transaction_items.quantity) as total_sold'),
                DB::raw('SUM(transaction_items.quantity * transaction_items.price) as total_revenue'),
            ])
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
    }
}
