<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FarmProfile;
use App\Models\PlotBoundary;
use App\Models\MrvDeclaration;
use App\Models\EvidenceFile;
use App\Models\AiAnalysisResult;
use App\Models\VerificationRecord;
use App\Models\CarbonCredit;
use App\Models\CarbonTransaction;
use App\Models\CooperativeMembership;
use App\Models\TrainingRecord;
use App\Models\FinancialRecord;
use App\Models\BlockchainAnchor;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create user types
        $this->createUsers();
        $this->createFarmProfiles();
        $this->createPlotBoundaries();
        $this->createMrvDeclarations();
        $this->createEvidenceFiles();
        $this->createAiAnalysisResults();
        $this->createVerificationRecords();
        $this->createCarbonCredits();
        $this->createCarbonTransactions();
        $this->createCooperativeMemberships();
        $this->createTrainingRecords();
        $this->createFinancialRecords();
        $this->createBlockchainAnchors();
    }

    private function createUsers()
    {
        // Create different user types
        $users = [
            [
                'email' => 'farmer@example.com',
                'phone' => '1234567890',
                'full_name' => 'John Farmer',
                'date_of_birth' => '1980-05-15',
                'user_type' => 'farmer',
                'gps_latitude' => 10.762622,
                'gps_longitude' => 106.660172,
                'organization_name' => 'Green Farm Co.',
                'organization_type' => 'individual',
                'address' => '123 Farm Road, Rural Area',
                'password' => Hash::make('12345678'),
            ],
            [
                'email' => 'bank@example.com',
                'phone' => '1234567891',
                'full_name' => 'Agricultural Bank',
                'date_of_birth' => '1985-01-01',
                'user_type' => 'bank',
                'gps_latitude' => 10.762622,
                'gps_longitude' => 106.660172,
                'organization_name' => 'AgriBank',
                'organization_type' => 'bank',
                'address' => '456 Bank Street, City Center',
                'password' => Hash::make('12345678'),
            ],
            [
                'email' => 'verifier@example.com',
                'phone' => '1234567892',
                'full_name' => 'Sarah Verifier',
                'date_of_birth' => '1985-08-20',
                'user_type' => 'verifier',
                'gps_latitude' => 10.762622,
                'gps_longitude' => 106.660172,
                'organization_name' => 'Carbon Verification Services',
                'organization_type' => 'verification_company',
                'address' => '789 Verification Ave, Business District',
                'password' => Hash::make('12345678'),
            ],
            [
                'email' => 'cooperative@example.com',
                'phone' => '1234567893',
                'full_name' => 'Farmers Cooperative',
                'date_of_birth' => '1990-01-01',
                'user_type' => 'cooperative',
                'gps_latitude' => 10.762622,
                'gps_longitude' => 106.660172,
                'organization_name' => 'Sustainable Farmers Co-op',
                'organization_type' => 'cooperative',
                'address' => '321 Cooperative Way, Rural Area',
                'password' => Hash::make('12345678'),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }

    private function createFarmProfiles()
    {
                $farmer = User::where('user_type', 'farmer')->first();

        if ($farmer) {
            FarmProfile::create([
                'user_id' => $farmer->id,
                'total_area_hectares' => 25.50,
                'rice_area_hectares' => 20.00,
                'agroforestry_area_hectares' => 5.50,
                'primary_crop_type' => 'Rice',
                'farming_experience_years' => 15,
                'irrigation_type' => 'Flood irrigation',
                'soil_type' => 'Clay loam',
            ]);
        }
    }

    private function createPlotBoundaries()
    {
        $farmProfile = FarmProfile::first();

        if ($farmProfile) {
            PlotBoundary::create([
                'farm_profile_id' => $farmProfile->id,
                'plot_name' => 'Main Rice Field',
                'boundary_coordinates' => [
                    ['lat' => 10.762622, 'lng' => 106.660172],
                    ['lat' => 10.762722, 'lng' => 106.660272],
                    ['lat' => 10.762822, 'lng' => 106.660372],
                    ['lat' => 10.762922, 'lng' => 106.660472],
                ],
                'area_hectares' => 20.00,
                'plot_type' => 'rice',
            ]);

            PlotBoundary::create([
                'farm_profile_id' => $farmProfile->id,
                'plot_name' => 'Agroforestry Area',
                'boundary_coordinates' => [
                    ['lat' => 10.763022, 'lng' => 106.660572],
                    ['lat' => 10.763122, 'lng' => 106.660672],
                    ['lat' => 10.763222, 'lng' => 106.660772],
                    ['lat' => 10.763322, 'lng' => 106.660872],
                ],
                'area_hectares' => 5.50,
                'plot_type' => 'agroforestry',
            ]);
        }
    }

    private function createMrvDeclarations()
    {
        $farmer = User::where('user_type', 'f        armer')->first();
        $farmProfile = FarmProfile::first();

        if ($farmer && $farmProfile) {
            MrvDeclaration::create([
                'user_id' => $farmer->id,
                'farm_profile_id' => $farmProfile->id,
                'declaration_period' => '2024-Q1',
                'rice_sowing_date' => '2024-01-15',
                'rice_harvest_date' => '2024-04-15',
                'awd_cycles_per_season' => 3,
                'water_management_method' => 'Alternate wetting and drying',
                'straw_management' => 'Incorporated into soil',
                'tree_density_per_hectare' => 150,
                'tree_species' => ['Mangrove', 'Acacia', 'Eucalyptus'],
                'intercrop_species' => ['Beans', 'Peanuts'],
                'planting_date' => '2023-06-01',
                'carbon_performance_score' => 85.50,
                'mrv_reliability_score' => 92.00,
                'estimated_carbon_credits' => 45.75,
                'status' => 'submitted',
            ]);
        }
    }

    private function createEvidenceFiles()
    {
        $mrvDeclaration = MrvDeclaration::first();

        if ($mrvDeclaration) {
            EvidenceFile::create([
                'mrv_declaration_id' => $mrvDeclaration->id,
                'file_type' => 'satellite_image',
                'file_url' => 'https://example.com/satellite_image_001.jpg',
                'file_name' => 'satellite_image_001.jpg',
                'file_size_bytes' => 2048576,
                'gps_latitude' => 10.762622,
                'gps_longitude' => 106.660172,
                'capture_timestamp' => '2024-01-15 08:00:00',
                'description' => 'Satellite image showing rice field boundaries',
            ]);

            EvidenceFile::create([
                'mrv_declaration_id' => $mrvDeclaration->id,
                'file_type' => 'field_photo',
                'file_url' => 'https://example.com/field_photo_001.jpg',
                'file_name' => 'field_photo_001.jpg',
                'file_size_bytes' => 1048576,
                'gps_latitude' => 10.762622,
                'gps_longitude' => 106.660172,
                'capture_timestamp' => '2024-01-15 09:00:00',
                'description' => 'Field photo showing rice growth stage',
            ]);
        }
    }

    private function createAiAnalysisResults()
    {
        $evidenceFile = EvidenceFile::first();

        if ($evidenceFile) {
            AiAnalysisResult::create([
                'evidence_file_id' => $evidenceFile->id,
                'analysis_type' => 'crop_health_analysis',
                'confidence_score' => 94.50,
                'analysis_results' => [
                    'crop_health' => 'excellent',
                    'growth_stage' => 'vegetative',
                    'estimated_yield' => 'high'
                ],
                'crop_health_score' => 92.00,
                'authenticity_score' => 98.50,
                'quality_indicators' => [
                    'image_quality' => 'high',
                    'gps_accuracy' => 'excellent',
                    'timestamp_validity' => 'valid'
                ],
                'recommendations' => 'Continue current farming practices. Monitor water levels for optimal AWD implementation.',
            ]);
        }
    }

    private function createVerificationRecords()
    {
        $mrvDeclaration = MrvDeclaration::first();
                $verifier = User::where('user_type', 'verifier')->first();

        if ($mrvDeclaration && $verifier) {
            VerificationRecord::create([
                'mrv_declaration_id' => $mrvDeclaration->id,
                'verifier_id' => $verifier->id,
                'verification_type' => 'hybrid',
                'verification_date' => '2024-01-20',
                'verification_status' => 'approved',
                'verification_score' => 88.50,
                'field_visit_notes' => 'Field visit confirmed rice cultivation practices. AWD implementation verified.',
                'verification_evidence' => [
                    'field_photos' => ['photo1.jpg', 'photo2.jpg'],
                    'soil_samples' => ['sample1.pdf'],
                    'water_measurements' => ['measurement1.xlsx']
                ],
                'verifier_comments' => 'Excellent implementation of sustainable farming practices. Carbon credit calculation appears accurate.',
            ]);
        }
    }

    private function createCarbonCredits()
    {
        $mrvDeclaration = MrvDeclaration::first        ();
        $verificationRecord = VerificationRecord::first();

        if ($mrvDeclaration && $verificationRecord) {
            CarbonCredit::create([
                'mrv_declaration_id' => $mrvDeclaration->id,
                'verification_record_id' => $verificationRecord->id,
                'credit_amount' => 45.75,
                'credit_type' => 'rice_cultivation',
                'vintage_year' => 2024,
                'certification_standard' => 'Gold Standard',
                'serial_number' => 'GS-2024-001-001',
                'status' => 'issued',
                'price_per_credit' => 25.00,
                'issued_date' => '2024-01-25',
                'expiry_date' => '2034-01-25',
            ]);
        }
    }

    private function createCarbonTransactions()
    {
        $carbonCredit = CarbonCredit::first();
        $farmer = User::where('user_type', 'farmer')->first(        );
        $buyer = User::where('user_type', 'bank')->first();

        if ($carbonCredit && $farmer && $buyer) {
            CarbonTransaction::create([
                'carbon_credit_id' => $carbonCredit->id,
                'seller_id' => $farmer->id,
                'buyer_id' => $buyer->id,
                'quantity' => 20.00,
                'price_per_credit' => 25.00,
                'total_amount' => 500.00,
                'transaction_date' => '2024-02-01',
                'payment_status' => 'completed',
                'transaction_hash' => '0x1234567890abcdef1234567890abcdef12345678',
            ]);
        }
    }

    private function createCooperativeMemberships()
    {
        $farmer = User::where('user_type', 'farmer')->first();
        $cooperative = User::where('user_type', 'cooperative')->first();

        if ($farmer && $cooperative) {
            CooperativeMembership::create([
                'user_id' => $farmer->id,
                'cooperative_id' => $cooperative->id,
                'membership_number' => 'COOP-2024-001',
                'join_date' => '2023-01-01',
                'membership_status' => 'active',
                'membership_fee_paid' => true,
            ]);
        }
    }

    private function createTrainingRecords()
    {
                $farmer = User::where('user_type', 'farmer')->first();

        if ($farmer) {
            TrainingRecord::create([
                'user_id' => $farmer->id,
                'training_type' => 'sustainable_farming',
                'training_title' => 'Advanced Rice Cultivation Techniques',
                'completion_date' => '2023-12-15',
                'completion_status' => 'completed',
                'score' => 95.50,
                'certificate_url' => 'https://example.com/certificates/farmer_001.pdf',
            ]);
        }
    }

    private function createFinancialRecords()
    {
        $farmer = User::where('user_type', 'farmer')->first        ();
        $bank = User::where('user_type', 'bank')->first();

        if ($farmer && $bank) {
            FinancialRecord::create([
                'user_id' => $farmer->id,
                'bank_id' => $bank->id,
                'record_type' => 'carbon_revenue',
                'amount' => 500.00,
                'currency' => 'USD',
                'transaction_date' => '2024-02-01',
                'description' => 'Carbon credit sale revenue',
                'status' => 'completed',
                'reference_number' => 'TXN-2024-001',
            ]);
        }
    }

    private function createBlockchainAnchors()
    {
        $mrvDeclaration = MrvDeclaration::first();

        if ($mrvDeclaration) {
            BlockchainAnchor::create([
                'record_type' => 'mrv_declaration',
                'record_id' => $mrvDeclaration->id,
                'blockchain_network' => 'Ethereum',
                'transaction_hash' => '0xabcdef1234567890abcdef1234567890abcdef12',
                'block_number' => 18500000,
                'gas_used' => 21000,
                'anchor_data' => [
                    'declaration_hash' => 'sha256_hash_here',
                    'timestamp' => '2024-01-15T08:00:00Z',
                    'verifier_signature' => 'verifier_sig_here'
                ],
                'anchor_timestamp' => '2024-01-15 08:00:00',
                'verification_url' => 'https://etherscan.io/tx/0xabcdef1234567890abcdef1234567890abcdef12',
            ]);
        }
    }
}
