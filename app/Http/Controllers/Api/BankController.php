<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BankController extends Controller
{
    use ApiResponseTrait;

    public function loanApplications(Request $request)
    {
        $bank = $request->user();

        $applications = FinancialRecord::where('bank_id', $bank->id)
            ->where('record_type', 'loan')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return $this->success(['applications' => $applications]);
    }

    public function approveLoan(Request $request, FinancialRecord $record)
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected']
        ]);

        $record->status = $request->input('status');
        $record->save();

        return $this->success($record, 'Loan status updated');
    }
}


