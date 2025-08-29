<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarbonCredit;
use App\Models\CooperativeMembership;
use App\Models\FarmProfile;
use App\Models\FinancialRecord;
use App\Models\MrvDeclaration;
use App\Models\PlotBoundary;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function user(Request $request)
    {
        $user = $request->user();

        // Lấy farm profile để tính carbon grade
        $farmProfile = FarmProfile::where('user_id', $user->id)->first();

        // Tính carbon grade dựa trên credit score
        $carbonGrade = $this->calculateCarbonGrade($user->id);

        // Kiểm tra MRV verification status
        $mrvVerified = MrvDeclaration::where('user_id', $user->id)
            ->where('status', 'verified')
            ->exists();

        return $this->success([
            'farmer' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'avatar' => $user->avatar ?? 'https://example.com/avatar.jpg',
                'carbon_grade' => $carbonGrade,
                'join_date' => $user->created_at->format('Y-m-d'),
                'phone' => $user->phone,
                'email' => $user->email,
                'location' => $user->address ?? 'Unknown',
                'date_of_birth' => $user->date_of_birth?->format('Y-m-d'),
                'mrv_verified' => $mrvVerified
            ]
        ]);
    }

    public function farmStats(Request $request)
    {
        $user = $request->user();

        // Lấy farm profile
        $farmProfile = FarmProfile::where('user_id', $user->id)->first();
        if (!$farmProfile) {
            return $this->error('No farm profile found', 404);
        }

        // Tính toán các stats
        $totalArea = $farmProfile->total_area_hectares;

        $carbonCreditsEarned = CarbonCredit::whereHas('mrvDeclaration', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'verified')->sum('credit_amount');

        $verificationRate = $this->calculateVerificationRate($user->id);

        $monthlyIncome = FinancialRecord::where('user_id', $user->id)
            ->where('record_type', 'carbon_revenue')
            ->where('status', 'paid')
            ->where('transaction_date', '>=', now()->subMonth())
            ->sum('amount');

        $carbonReduction = MrvDeclaration::where('user_id', $user->id)
            ->where('status', 'verified')
            ->sum('estimated_carbon_credits');

        $mrvReliability = MrvDeclaration::where('user_id', $user->id)
            ->where('status', 'verified')
            ->avg('mrv_reliability_score') ?? 0;

        return $this->success([
            'farm_stats' => [
                'total_area' => round($totalArea, 2),
                'carbon_credits_earned' => round($carbonCreditsEarned, 2),
                'verification_rate' => round($verificationRate, 2),
                'monthly_income' => round($monthlyIncome, 2),
                'carbon_reduction' => round($carbonReduction, 2),
                'mrv_reliability' => round($mrvReliability, 2)
            ]
        ]);
    }

    public function landPlots(Request $request)
    {
        $user = $request->user();

        // Lấy farm profile
        $farmProfile = FarmProfile::where('user_id', $user->id)->first();
        if (!$farmProfile) {
            return $this->error('No farm profile found', 404);
        }

        // Lấy land plots
        $plots = PlotBoundary::where('farm_profile_id', $farmProfile->id)
            ->get()
            ->map(function ($plot) use ($user) {
                // Lấy MRV declaration cho plot này thông qua farm_profile_id
                $latestDeclaration = MrvDeclaration::where('farm_profile_id', $plot->farm_profile_id)
                    ->where('user_id', $user->id)
                    ->where('status', 'verified')
                    ->latest()
                    ->first();

                $carbonScore = $this->calculateCarbonScore($latestDeclaration);

                return [
                    'id' => $plot->id,
                    'name' => $plot->plot_name,
                    'location' => $this->getLocationFromCoordinates($plot->boundary_coordinates),
                    'status' => $latestDeclaration ? $latestDeclaration->status : 'pending',
                    'area' => round($plot->area_hectares, 2),
                    'crop_type' => $plot->plot_type === 'rice' ? 'Rice' : 'Agroforestry',
                    'carbon_score' => $carbonScore,
                    'verification_date' => $latestDeclaration && $latestDeclaration->status === 'verified'
                        ? $latestDeclaration->updated_at->format('Y-m-d')
                        : null
                ];
            });

        return $this->success([
            'land_plots' => $plots
        ]);
    }

    public function yieldHistory(Request $request)
    {
        $user = $request->user();
        $limit = min(20, max(1, (int) $request->input('limit', 5)));

        // Lấy yield history từ financial records (giả lập)
        // Trong thực tế, có thể có bảng yields riêng
        $yields = $this->generateYieldHistory($user->id, $limit);

        return $this->success([
            'yields' => $yields
        ]);
    }

    public function memberships(Request $request)
    {
        $user = $request->user();

        // Lấy cooperative membership
        $membership = CooperativeMembership::where('user_id', $user->id)
            ->with('cooperative')
            ->first();

        // Lấy training records
        $trainingRecords = TrainingRecord::where('user_id', $user->id)
            ->orderBy('completion_date', 'desc')
            ->get()
            ->map(function ($training) {
                return [
                    'name' => $training->training_name,
                    'completion_date' => $training->completion_date->format('Y-m-d'),
                    'score' => $training->score ?? 0
                ];
            });

        $overallTrainingScore = $trainingRecords->count() > 0
            ? round($trainingRecords->avg('score'), 2)
            : 0;

        return $this->success([
            'memberships' => [
                'cooperative' => $membership ? $membership->cooperative->organization_name : 'No Cooperative',
                'cooperative_status' => $membership ? $membership->status : 'inactive',
                'training_completed' => $trainingRecords,
                'overall_training_score' => $overallTrainingScore
            ]
        ]);
    }

    public function loanHistory(Request $request)
    {
        $user = $request->user();

        // Lấy loan history từ financial records
        $loans = FinancialRecord::where('user_id', $user->id)
            ->where('record_type', 'loan')
            ->orderBy('transaction_date', 'desc')
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'date' => $loan->transaction_date->format('Y-m-d'),
                    'amount' => round($loan->amount, 2),
                    'status' => $loan->status,
                    'purpose' => $loan->description ?? 'Agricultural Loan',
                    'repayment_date' => $loan->status === 'repaid'
                        ? $loan->transaction_date->addMonths(6)->format('Y-m-d')
                        : null,
                    'interest_rate' => 8.5 // Giả lập interest rate
                ];
            });

        $totalBorrowed = $loans->sum('amount');
        $totalRepaid = $loans->where('status', 'repaid')->sum('amount');
        $creditScore = $this->calculateLoanCreditScore($totalBorrowed, $totalRepaid);

        return $this->success([
            'loans' => $loans,
            'total_borrowed' => round($totalBorrowed, 2),
            'total_repaid' => round($totalRepaid, 2),
            'credit_score' => $creditScore
        ]);
    }

    // Helper methods
    private function calculateCarbonGrade(int $userId): string
    {
        $declarations = MrvDeclaration::where('user_id', $userId)
            ->where('status', 'verified')
            ->get();

        if ($declarations->isEmpty()) {
            return 'F';
        }

        $avgScore = ($declarations->avg('carbon_performance_score') + $declarations->avg('mrv_reliability_score')) / 2;

        if ($avgScore >= 90) return 'A+';
        if ($avgScore >= 85) return 'A';
        if ($avgScore >= 80) return 'A-';
        if ($avgScore >= 75) return 'B+';
        if ($avgScore >= 70) return 'B';
        if ($avgScore >= 65) return 'B-';
        if ($avgScore >= 60) return 'C+';
        if ($avgScore >= 55) return 'C';
        if ($avgScore >= 50) return 'C-';
        if ($avgScore >= 45) return 'D+';
        if ($avgScore >= 40) return 'D';
        return 'F';
    }

    private function calculateVerificationRate(int $userId): float
    {
        $total = MrvDeclaration::where('user_id', $userId)->count();
        $verified = MrvDeclaration::where('user_id', $userId)
            ->where('status', 'verified')
            ->count();

        return $total > 0 ? ($verified / $total) * 100 : 0;
    }

    private function calculateCarbonScore($declaration): string
    {
        if (!$declaration) return 'N/A';

        $score = ($declaration->carbon_performance_score + $declaration->mrv_reliability_score) / 2;

        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'A-';
        if ($score >= 75) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 65) return 'B-';
        if ($score >= 60) return 'C+';
        if ($score >= 55) return 'C';
        if ($score >= 50) return 'C-';
        if ($score >= 45) return 'D+';
        if ($score >= 40) return 'D';
        return 'F';
    }

    private function getLocationFromCoordinates($coordinates): string
    {
        if (!$coordinates || !is_array($coordinates) || empty($coordinates)) {
            return 'Unknown';
        }

        // Giả lập location từ coordinates
        // Trong thực tế, có thể sử dụng reverse geocoding API
        $locations = ['An Giang', 'Dong Thap', 'Can Tho', 'Soc Trang', 'Bac Lieu'];
        return $locations[array_rand($locations)];
    }

    private function generateYieldHistory(int $userId, int $limit): array
    {
        // Giả lập yield history vì không có bảng yields thật
        $seasons = ['Spring', 'Summer', 'Autumn', 'Winter'];
        $crops = ['Rice', 'Corn', 'Vegetables'];
        $yields = [];

        for ($i = 0; $i < $limit; $i++) {
            $year = 2024 - floor($i / 4);
            $season = $seasons[$i % 4];
            $crop = $crops[$i % 3];

            $yields[] = [
                'id' => $i + 1,
                'season' => $season . ' ' . $year,
                'crop' => $crop,
                'yield' => round(4 + (rand(0, 30) / 10), 1),
                'price' => 8000 + (rand(0, 2000)),
                'harvest_date' => date('Y-m-d', strtotime("-$i months")),
                'is_highlight' => $i === 0
            ];
        }

        return $yields;
    }

    private function calculateLoanCreditScore(float $totalBorrowed, float $totalRepaid): string
    {
        if ($totalBorrowed <= 0) return 'excellent';

        $repaymentRate = ($totalRepaid / $totalBorrowed) * 100;

        if ($repaymentRate >= 100) return 'excellent';
        if ($repaymentRate >= 90) return 'good';
        if ($repaymentRate >= 80) return 'fair';
        if ($repaymentRate >= 70) return 'poor';
        return 'very_poor';
    }
}
