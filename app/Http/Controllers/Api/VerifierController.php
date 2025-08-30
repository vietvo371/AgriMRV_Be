<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MrvDeclaration;
use App\Models\VerificationRecord;
use App\Models\User;
use App\Models\FarmProfile;
use App\Models\EvidenceFile;
use App\Models\AiAnalysisResult;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class VerifierController extends Controller
{
    use ApiResponseTrait;

    public function queue(Request $request)
    {
        // Danh sách MRV declarations đang chờ xác minh
        $pending = MrvDeclaration::with(['user:id,full_name,email,address', 'farmProfile:id,total_area_hectares,rice_area_hectares,agroforestry_area_hectares,primary_crop_type'])
            ->where('status', 'submitted')
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        // Add evidence files count
        $pending->each(function ($declaration) {
            $declaration->evidence_files_count = EvidenceFile::where('mrv_declaration_id', $declaration->id)->count();
            $declaration->estimated_carbon_credits = $declaration->estimated_carbon_credits ?? 0;
        });

        return $this->success(['queue' => $pending]);
    }

    public function myVerifications(Request $request)
    {
        $verifier = $request->user();

        // Get filters from request
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $verificationType = $request->get('verification_type');
        $verificationStatus = $request->get('verification_status');

        $query = VerificationRecord::with(['mrvDeclaration.user:id,full_name,email,address', 'mrvDeclaration.farmProfile:id,primary_crop_type'])
            ->where('verifier_id', $verifier->id);

        // Apply filters
        if ($startDate && $endDate) {
            $query->whereBetween('verification_date', [$startDate, $endDate]);
        }

        if ($verificationType) {
            $query->where('verification_type', $verificationType);
        }

        if ($verificationStatus) {
            $query->where('verification_status', $verificationStatus);
        }

        $records = $query->orderBy('verification_date', 'desc')->get();

        // Add additional data
        $records->each(function ($record) {
            $record->farmer_name = $record->mrvDeclaration->user->full_name ?? 'Unknown';
            $record->location = $record->mrvDeclaration->user->address ?? 'N/A';
            $record->carbon_claims = $record->mrvDeclaration->estimated_carbon_credits ?? 0;
        });

        return $this->success(['verifications' => $records]);
    }

    public function analytics(Request $request)
    {
        $verifier = $request->user();

        // Get filters
        $timePeriod = $request->get('time_period', 30);
        $region = $request->get('region');
        $farmSize = $request->get('farm_size');
        $cropType = $request->get('crop_type');
        $verificationMethod = $request->get('verification_method');

        // Calculate date range
        $endDate = now();
        $startDate = now()->subDays($timePeriod);

        // Get verifications for the period
        $verifications = VerificationRecord::with(['mrvDeclaration.user', 'mrvDeclaration.farmProfile'])
            ->where('verifier_id', $verifier->id)
            ->whereBetween('verification_date', [$startDate, $endDate])
            ->get();

        // Apply additional filters
        if ($region) {
            $verifications = $verifications->filter(function ($v) use ($region) {
                return str_contains(strtolower($v->mrvDeclaration->user->address ?? ''), strtolower($region));
            });
        }

        // Calculate summary metrics
        $totalVerifications = $verifications->count();
        $approvedCount = $verifications->where('verification_status', 'approved')->count();
        $performanceScore = $totalVerifications > 0 ? ($approvedCount / $totalVerifications) * 100 : 0;

        // Calculate efficiency rate (simplified)
        $efficiencyRate = 85; // Placeholder

        // Determine risk level
        $riskLevel = 'Low'; // Placeholder

        // Calculate carbon impact
        $carbonImpact = $verifications->sum(function ($v) {
            return $v->mrvDeclaration->estimated_carbon_credits ?? 0;
        });

        // Generate performance trends (last 7 days)
        $trendLabels = [];
        $trendValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('M d');
            $trendValues[] = $verifications->where('verification_date', $date->format('Y-m-d'))->count();
        }

        // Generate risk matrix data
        $riskMatrix = [
            'low_risk' => [],
            'medium_risk' => [],
            'high_risk' => []
        ];

        // Regional performance
        $regionalPerformance = [
            'north' => 75,
            'central' => 82,
            'south' => 78
        ];

        // Carbon distribution
        $carbonDistribution = [
            'small_farms' => 25,
            'medium_farms' => 45,
            'large_farms' => 30
        ];

        // Forecast data
        $forecast = [
            'labels' => $trendLabels,
            'historical' => $trendValues,
            'predicted' => array_map(function($val) { return $val + rand(-2, 2); }, $trendValues)
        ];

        // Benchmarks
        $benchmarks = [
            [
                'metric' => 'Verification Accuracy',
                'your_performance' => $performanceScore,
                'industry_average' => 78,
                'top_performers' => 95,
                'recommendations' => 'Focus on evidence quality review'
            ],
            [
                'metric' => 'Processing Speed',
                'your_performance' => $efficiencyRate,
                'industry_average' => 72,
                'top_performers' => 88,
                'recommendations' => 'Optimize field visit scheduling'
            ]
        ];

        return $this->success([
            'summary' => [
                'performance_score' => round($performanceScore, 1),
                'efficiency_rate' => $efficiencyRate,
                'risk_level' => $riskLevel,
                'carbon_impact' => round($carbonImpact, 1)
            ],
            'performance_trends' => [
                'labels' => $trendLabels,
                'values' => $trendValues
            ],
            'risk_matrix' => $riskMatrix,
            'regional_performance' => $regionalPerformance,
            'carbon_distribution' => $carbonDistribution,
            'forecast' => $forecast,
            'benchmarks' => $benchmarks
        ]);
    }

    public function aiInsights(Request $request)
    {
        $verifier = $request->user();

        // Get recent verifications for AI insights
        $recentVerifications = VerificationRecord::with(['mrvDeclaration'])
            ->where('verifier_id', $verifier->id)
            ->orderBy('verification_date', 'desc')
            ->take(10)
            ->get();

        // Generate AI insights based on verification patterns
        $insights = [];

        if ($recentVerifications->count() > 0) {
            $approvalRate = $recentVerifications->where('verification_status', 'approved')->count() / $recentVerifications->count();

            if ($approvalRate < 0.7) {
                $insights[] = [
                    'title' => 'Low Approval Rate Detected',
                    'description' => 'Your recent verification approval rate is below average. Consider reviewing your verification criteria.',
                    'confidence' => 85
                ];
            }

            $insights[] = [
                'title' => 'Performance Optimization',
                'description' => 'Based on your verification patterns, consider using hybrid verification methods for better efficiency.',
                'confidence' => 78
            ];

            $insights[] = [
                'title' => 'Risk Assessment',
                'description' => 'Your verification portfolio shows balanced risk distribution across different farm sizes.',
                'confidence' => 92
            ];
        }

        return $this->success(['insights' => $insights]);
    }
}



