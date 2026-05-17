<?php

namespace App\Services;

use App\Models\PosShift;
use App\Models\TransactionPayment;
use Illuminate\Support\Facades\Auth;

class ShiftService
{
    /**
     * Open a new POS shift for the current user.
     *
     * @param int $startingCash
     * @return PosShift
     * @throws \Exception
     */
    public function openShift(int $startingCash): PosShift
    {
        $activeShift = $this->getActiveShift();

        if ($activeShift) {
            throw new \Exception('Anda sudah memiliki shift yang aktif.');
        }

        return PosShift::create([
            'user_id' => Auth::id(),
            'start_time' => now(),
            'starting_cash' => $startingCash,
            'status' => 'OPEN',
        ]);
    }

    /**
     * Close the active shift with reconciliation.
     *
     * @param int $actualCash
     * @param string|null $notes
     * @return PosShift
     * @throws \Exception
     */
    public function closeShift(int $actualCash, ?string $notes = null): PosShift
    {
        $shift = $this->getActiveShift();

        if (!$shift) {
            throw new \Exception('Tidak ada shift aktif.');
        }

        $summary = $this->getShiftSummary($shift);

        $shift->update([
            'end_time' => now(),
            'expected_cash' => $summary['expected_cash'],
            'actual_cash' => $actualCash,
            'difference' => $actualCash - $summary['expected_cash'],
            'status' => 'CLOSED',
            'notes' => $notes,
        ]);

        return $shift->fresh();
    }

    /**
     * Get the active shift for the current user.
     *
     * @return PosShift|null
     */
    public function getActiveShift(): ?PosShift
    {
        return PosShift::where('user_id', Auth::id())
            ->where('status', 'OPEN')
            ->first();
    }

    /**
     * Get shift summary including payment breakdown.
     *
     * @param PosShift $shift
     * @return array
     */
    public function getShiftSummary(PosShift $shift): array
    {
        $cashPayments = TransactionPayment::where('pos_shift_id', $shift->id)
            ->where('payment_method', 'CASH')
            ->sum('amount_paid');

        $nonCashPayments = TransactionPayment::where('pos_shift_id', $shift->id)
            ->where('payment_method', '!=', 'CASH')
            ->sum('amount_paid');

        $paymentBreakdown = TransactionPayment::where('pos_shift_id', $shift->id)
            ->select('payment_method', \DB::raw('SUM(amount_paid) as total'))
            ->groupBy('payment_method')
            ->get()
            ->pluck('total', 'payment_method');

        $expectedCash = $shift->starting_cash + $cashPayments;

        return [
            'cash_payments' => $cashPayments,
            'non_cash_payments' => $nonCashPayments,
            'payment_breakdown' => $paymentBreakdown,
            'expected_cash' => $expectedCash,
        ];
    }

    /**
     * Get shift by ID with ownership validation.
     *
     * @param int $shiftId
     * @return PosShift
     * @throws \Exception
     */
    public function getShiftForClosing(int $shiftId): PosShift
    {
        $shift = PosShift::find($shiftId);

        if (!$shift) {
            throw new \Exception('Shift tidak ditemukan.');
        }

        if ($shift->user_id !== Auth::id()) {
            throw new \Exception('Anda tidak memiliki akses ke shift ini.');
        }

        if ($shift->status === 'CLOSED') {
            throw new \Exception('Shift ini sudah ditutup.');
        }

        return $shift;
    }
}
