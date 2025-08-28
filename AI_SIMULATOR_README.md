# AI Analysis Simulator Service

## Tổng quan

AI Analysis Simulator Service là một service giả lập AI để tạo ra các kết quả phân tích giống như AI thật, giúp bạn test hệ thống mà không cần phát triển AI thực tế.

## Tính năng

### 🎯 **Phân tích đa dạng theo loại file:**
- **satellite_image, field_photo, drone_image** → `crop_health_analysis`
- **soil_sample** → `soil_analysis`
- **water_sample** → `water_quality_analysis`
- **yield_photo** → `yield_prediction`
- **irrigation_photo** → `water_management_analysis`

### 📊 **Dữ liệu phân tích thực tế:**
- **Crop Health Analysis**: Sức khỏe cây trồng, giai đoạn phát triển, dự đoán năng suất
- **Soil Analysis**: Loại đất, chất lượng, pH, hàm lượng hữu cơ, độ ẩm
- **Water Quality Analysis**: Chất lượng nước, pH, độ đục, oxy hòa tan, dinh dưỡng
- **Yield Prediction**: Dự đoán năng suất, xu hướng tăng trưởng, yếu tố rủi ro
- **Water Management**: Hiệu quả sử dụng nước, lịch tưới, phân bố nước

### 🎲 **Tính điểm thông minh:**
- Điểm cơ bản dựa trên metadata của file
- Tăng điểm theo file size, GPS accuracy, timestamp
- Điểm cuối cùng có biến động ngẫu nhiên (±5 điểm)

## Cách sử dụng

### 1. **Trong Seeder (Tự động):**
```php
// DatabaseSeeder.php đã được cập nhật để sử dụng AI Simulator
private function createAiAnalysisResults()
{
    $aiSimulator = new \App\Services\AiAnalysisSimulatorService();
    $evidenceFiles = EvidenceFile::all();
    
    foreach ($evidenceFiles as $evidenceFile) {
        $aiSimulator->simulateAnalysis($evidenceFile);
    }
}
```

### 2. **Trong Controller/Service:**
```php
use App\Services\AiAnalysisSimulatorService;

class SomeController extends Controller
{
    public function analyzeEvidence(EvidenceFile $evidenceFile)
    {
        $aiSimulator = new AiAnalysisSimulatorService();
        $analysisResult = $aiSimulator->simulateAnalysis($evidenceFile);
        
        return response()->json([
            'analysis' => $analysisResult,
            'recommendations' => $analysisResult->recommendations
        ]);
    }
}
```

### 3. **Test Command:**
```bash
# Test với 5 evidence files (mặc định)
php artisan test:ai-simulator

# Test với số lượng tùy chỉnh
php artisan test:ai-simulator --count=10
```

## Cấu trúc dữ liệu

### **Crop Health Analysis:**
```json
{
  "analysis_type": "crop_health_analysis",
  "confidence_score": 88.5,
  "analysis_results": {
    "crop_health": "excellent",
    "growth_stage": "vegetative",
    "estimated_yield": "high",
    "disease_detection": "none_detected",
    "nutrient_deficiency": "optimal",
    "stress_factors": ["drought"]
  },
  "crop_health_score": 95.0,
  "authenticity_score": 90.2,
  "quality_indicators": {
    "image_quality": "high",
    "gps_accuracy": "excellent",
    "timestamp_validity": "valid",
    "file_integrity": "verified",
    "resolution_quality": "ultra_hd"
  },
  "recommendations": "Cây trồng phát triển rất tốt. Tiếp tục duy trì các biện pháp chăm sóc hiện tại."
}
```

### **Soil Analysis:**
```json
{
  "analysis_type": "soil_analysis",
  "analysis_results": {
    "soil_type": "sandy_loam",
    "soil_quality": "good",
    "ph_level": 6.8,
    "organic_matter": 3.2,
    "moisture_content": 28.5,
    "compaction_level": "low"
  },
  "recommendations": "Đất có chất lượng tốt. Có thể cải thiện thêm bằng phân bón hữu cơ."
}
```

### **Water Quality Analysis:**
```json
{
  "analysis_type": "water_quality_analysis",
  "analysis_results": {
    "water_quality": "excellent",
    "ph_level": 7.2,
    "turbidity": 3.5,
    "dissolved_oxygen": 8.5,
    "nutrient_levels": {
      "nitrogen": "15 mg/L",
      "phosphorus": "1.2 mg/L",
      "potassium": "8 mg/L"
    },
    "contamination_risk": "very_low"
  },
  "recommendations": "Chất lượng nước rất tốt. Phù hợp cho tưới tiêu và nuôi trồng."
}
```

## Tùy chỉnh

### **Thêm loại phân tích mới:**
```php
// Trong AiAnalysisSimulatorService.php
private function determineAnalysisType(string $fileType): string
{
    $typeMap = [
        // ... existing types ...
        'new_file_type' => 'new_analysis_type',
    ];
    
    return $typeMap[$fileType] ?? 'general_analysis';
}

// Thêm method generateNewAnalysisData()
private function generateNewAnalysisData(EvidenceFile $evidenceFile, float $baseScore): array
{
    // Logic tạo dữ liệu cho loại phân tích mới
}
```

### **Điều chỉnh điểm số:**
```php
private function calculateBaseScore(EvidenceFile $evidenceFile): float
{
    $score = 85.0; // Điểm cơ bản
    
    // Tùy chỉnh logic tính điểm
    if ($evidenceFile->file_size_bytes > 5 * 1024 * 1024) { // > 5MB
        $score += 10; // Tăng điểm cho file lớn
    }
    
    return min(100, $score);
}
```

## Lợi ích

### ✅ **Cho Development:**
- Test toàn bộ workflow mà không cần AI thật
- Dữ liệu đa dạng và thực tế
- Dễ dàng debug và validate

### ✅ **Cho Testing:**
- Dữ liệu nhất quán và có thể dự đoán
- Có thể tạo test cases cụ thể
- Performance testing với dữ liệu lớn

### ✅ **Cho Demo:**
- Hiển thị tính năng AI cho stakeholders
- Dữ liệu đẹp và chuyên nghiệp
- Không cần setup AI infrastructure

## Khi nào thay thế bằng AI thật

1. **Khi có AI model thực tế**
2. **Khi cần độ chính xác cao**
3. **Khi có đủ dữ liệu training**
4. **Khi có budget cho AI development**

## Troubleshooting

### **Lỗi thường gặp:**
```bash
# Lỗi: Class not found
composer dump-autoload

# Lỗi: Database connection
php artisan config:cache

# Lỗi: Permission
chmod -R 755 storage/
```

### **Debug:**
```php
// Trong service
\Log::info('AI Analysis Data:', $analysisData);

// Trong command
$this->error('Debug: ' . json_encode($result->toArray()));
```

## Kết luận

AI Analysis Simulator Service cung cấp giải pháp hoàn hảo để test và demo hệ thống AgriMR mà không cần phát triển AI thực tế. Service này tạo ra dữ liệu chất lượng cao, đa dạng và có thể tùy chỉnh theo nhu cầu cụ thể của dự án.
