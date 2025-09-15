# AI-Powered Room Placement Suggestions - Implementation Path

## 🎯 Overview

This document outlines the complete implementation path for the AI-Powered Room Placement Suggestions feature, including PHP libraries, AI services, and step-by-step development approach.

## 🏗️ Architecture Overview

```
User Upload → Image Processing → AI Analysis → Placement Algorithm → Visualization → User Feedback
     ↓              ↓              ↓              ↓              ↓              ↓
  PHP Backend → Image Preprocessing → AI Services → Space Calculation → 3D Render → Learning Loop
```

## 📚 Technology Stack Analysis

### **PHP Libraries & Extensions**

#### 1. **Image Processing**
- **Imagick** (Recommended)
  - Native PHP extension for ImageMagick
  - Excellent for image preprocessing, resizing, format conversion
  - Supports advanced image manipulation
  - Installation: `pecl install imagick`

- **GD Extension** (Alternative)
  - Built-in PHP extension
  - Basic image processing capabilities
  - Limited compared to Imagick

#### 2. **Machine Learning Libraries**
- **Rubix ML** (Recommended)
  - Modern PHP ML library with active development
  - Supports neural networks, clustering, classification
  - Good documentation and examples
  - Installation: `composer require rubix/ml`

- **PHP-ML** (Alternative)
  - Older but stable ML library
  - Limited neural network support
  - Good for basic ML tasks

#### 3. **HTTP Clients for AI Services**
- **Guzzle HTTP** (Recommended)
  - Industry standard for HTTP requests
  - Excellent for API integrations
  - Installation: `composer require guzzlehttp/guzzle`

### **AI Services Integration**

#### 1. **OpenAI Vision API** (Primary Choice)
```php
// Example integration
$client = new \GuzzleHttp\Client();
$response = $client->post('https://api.openai.com/v1/chat/completions', [
    'headers' => [
        'Authorization' => 'Bearer ' . $apiKey,
        'Content-Type' => 'application/json',
    ],
    'json' => [
        'model' => 'gpt-4-vision-preview',
        'messages' => [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Analyze this room image and identify available spaces for furniture placement'
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => ['url' => $roomImageUrl]
                    ]
                ]
            ]
        ]
    ]
]);
```

**Pros:**
- Excellent object detection and scene understanding
- Natural language processing for room analysis
- High accuracy for furniture and space identification
- Easy PHP integration

**Cons:**
- Cost per API call
- Requires internet connection
- Rate limits

#### 2. **Google Cloud Vision API** (Alternative)
```php
// Example integration
$client = new \Google\Cloud\Vision\V1\ImageAnnotatorClient();
$image = file_get_contents($imagePath);
$image = \Google\Cloud\Vision\V1\Image::fromString($image);

$response = $client->objectLocalization($image);
$objects = $response->getLocalizedObjectAnnotations();
```

**Pros:**
- Excellent object detection
- Good pricing model
- Reliable service
- Detailed object information

**Cons:**
- More complex setup
- Requires Google Cloud account
- Less natural language processing

#### 3. **Custom AI Model** (Advanced)
- Train custom models using TensorFlow/PyTorch
- Deploy as REST API
- Integrate via PHP HTTP client
- Full control over model behavior

## 🛠️ Implementation Phases

### **Phase 1: Foundation Setup (Week 1-2)**

#### 1.1 Environment Setup
```bash
# Install required PHP extensions
pecl install imagick
pecl install redis  # For caching

# Install Composer dependencies
composer require rubix/ml
composer require guzzlehttp/guzzle
composer require google/cloud-vision
composer require openai-php/client
```

#### 1.2 Database Schema Implementation
```sql
-- Room analysis storage
CREATE TABLE room_analyses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_image_path VARCHAR(255) NOT NULL,
    room_dimensions JSON,
    detected_objects JSON,
    available_spaces JSON,
    room_type VARCHAR(50),
    lighting_conditions VARCHAR(50),
    analysis_confidence DECIMAL(3,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Object analysis storage
CREATE TABLE object_analyses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    object_image_path VARCHAR(255) NOT NULL,
    object_type VARCHAR(100),
    dimensions JSON,
    color_palette JSON,
    style_category VARCHAR(50),
    compatibility_score DECIMAL(3,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Placement suggestions
CREATE TABLE placement_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_analysis_id INT NOT NULL,
    object_analysis_id INT NOT NULL,
    suggested_position JSON,
    confidence_score DECIMAL(3,2),
    reasoning TEXT,
    user_feedback ENUM('accepted', 'rejected', 'modified') NULL,
    feedback_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_analysis_id) REFERENCES room_analyses(id) ON DELETE CASCADE,
    FOREIGN KEY (object_analysis_id) REFERENCES object_analyses(id) ON DELETE CASCADE
);
```

#### 1.3 Core Classes Structure
```php
// app/Services/AI/RoomAnalyzer.php
class RoomAnalyzer {
    private $openaiClient;
    private $imageProcessor;
    
    public function analyzeRoom(string $imagePath): array
    public function detectObjects(string $imagePath): array
    public function identifySpaces(string $imagePath): array
}

// app/Services/AI/ObjectAnalyzer.php
class ObjectAnalyzer {
    public function analyzeObject(string $imagePath): array
    public function extractDimensions(string $imagePath): array
    public function identifyStyle(string $imagePath): string
}

// app/Services/AI/PlacementEngine.php
class PlacementEngine {
    public function suggestPlacement(array $roomData, array $objectData): array
    public function calculateOptimalPosition(array $spaces, array $objectDimensions): array
    public function validatePlacement(array $suggestion): bool
}
```

### **Phase 2: Image Processing Pipeline (Week 3-4)**

#### 2.1 Image Preprocessing Service
```php
<?php
// app/Services/ImageProcessor.php

class ImageProcessor {
    private $imagick;
    
    public function __construct() {
        $this->imagick = new Imagick();
    }
    
    public function preprocessRoomImage(string $imagePath): string {
        $image = new Imagick($imagePath);
        
        // Resize to standard dimensions
        $image->resizeImage(1024, 768, Imagick::FILTER_LANCZOS, 1, true);
        
        // Enhance image quality
        $image->enhanceImage();
        $image->sharpenImage(0, 1);
        
        // Convert to RGB
        $image->transformImageColorspace(Imagick::COLORSPACE_RGB);
        
        $processedPath = $this->getProcessedImagePath($imagePath);
        $image->writeImage($processedPath);
        
        return $processedPath;
    }
    
    public function preprocessObjectImage(string $imagePath): string {
        $image = new Imagick($imagePath);
        
        // Remove background (basic implementation)
        $image->transparentPaintImage(
            $image->getImagePixelColor(0, 0),
            0,
            Imagick::FUZZ * 0.1,
            false
        );
        
        // Resize maintaining aspect ratio
        $image->resizeImage(512, 512, Imagick::FILTER_LANCZOS, 1, true);
        
        $processedPath = $this->getProcessedImagePath($imagePath);
        $image->writeImage($processedPath);
        
        return $processedPath;
    }
    
    private function getProcessedImagePath(string $originalPath): string {
        $pathInfo = pathinfo($originalPath);
        return $pathInfo['dirname'] . '/processed_' . $pathInfo['basename'];
    }
}
```

#### 2.2 AI Service Integration
```php
<?php
// app/Services/AI/OpenAIService.php

class OpenAIService {
    private $client;
    private $apiKey;
    
    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
        $this->client = new \GuzzleHttp\Client();
    }
    
    public function analyzeRoom(string $imagePath): array {
        $imageData = base64_encode(file_get_contents($imagePath));
        
        $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4-vision-preview',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyze this room image and provide detailed information about:
                                1. Room type and dimensions (estimate)
                                2. Existing furniture and their positions
                                3. Available spaces for new furniture
                                4. Lighting conditions
                                5. Color scheme and style
                                6. Architectural features (windows, doors, etc.)
                                
                                Return the response as JSON format.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => ['url' => 'data:image/jpeg;base64,' . $imageData]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 1000
            ]
        ]);
        
        $data = json_decode($response->getBody(), true);
        return json_decode($data['choices'][0]['message']['content'], true);
    }
    
    public function analyzeObject(string $imagePath): array {
        $imageData = base64_encode(file_get_contents($imagePath));
        
        $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4-vision-preview',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyze this furniture/object image and provide:
                                1. Object type and category
                                2. Estimated dimensions (width, height, depth)
                                3. Style and design characteristics
                                4. Color palette
                                5. Placement requirements (floor, wall-mounted, etc.)
                                6. Compatibility with different room types
                                
                                Return as JSON format.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => ['url' => 'data:image/jpeg;base64,' . $imageData]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 800
            ]
        ]);
        
        $data = json_decode($response->getBody(), true);
        return json_decode($data['choices'][0]['message']['content'], true);
    }
}
```

### **Phase 3: Placement Algorithm (Week 5-6)**

#### 3.1 Placement Engine Implementation
```php
<?php
// app/Services/AI/PlacementEngine.php

class PlacementEngine {
    private $roomAnalyzer;
    private $objectAnalyzer;
    
    public function __construct() {
        $this->roomAnalyzer = new RoomAnalyzer();
        $this->objectAnalyzer = new ObjectAnalyzer();
    }
    
    public function suggestPlacement(array $roomData, array $objectData): array {
        $suggestions = [];
        
        // Calculate available spaces
        $availableSpaces = $this->calculateAvailableSpaces($roomData);
        
        // Filter spaces by object requirements
        $suitableSpaces = $this->filterSuitableSpaces($availableSpaces, $objectData);
        
        // Score each potential placement
        foreach ($suitableSpaces as $space) {
            $score = $this->calculatePlacementScore($space, $objectData, $roomData);
            $suggestions[] = [
                'position' => $space['position'],
                'rotation' => $space['rotation'],
                'confidence' => $score,
                'reasoning' => $this->generateReasoning($space, $objectData, $roomData)
            ];
        }
        
        // Sort by confidence score
        usort($suggestions, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });
        
        return array_slice($suggestions, 0, 3); // Return top 3 suggestions
    }
    
    private function calculateAvailableSpaces(array $roomData): array {
        $spaces = [];
        
        // Analyze room layout
        $roomDimensions = $roomData['dimensions'];
        $existingFurniture = $roomData['existing_furniture'];
        
        // Calculate free spaces
        foreach ($roomData['available_areas'] as $area) {
            $spaces[] = [
                'position' => $area['center'],
                'dimensions' => $area['dimensions'],
                'rotation' => $this->calculateOptimalRotation($area, $roomData),
                'type' => $area['type'] // 'floor', 'wall', 'corner'
            ];
        }
        
        return $spaces;
    }
    
    private function filterSuitableSpaces(array $spaces, array $objectData): array {
        return array_filter($spaces, function($space) use ($objectData) {
            // Check if space is large enough
            if (!$this->spaceFitsObject($space, $objectData)) {
                return false;
            }
            
            // Check placement requirements
            if ($objectData['placement_type'] === 'floor' && $space['type'] !== 'floor') {
                return false;
            }
            
            if ($objectData['placement_type'] === 'wall' && $space['type'] !== 'wall') {
                return false;
            }
            
            return true;
        });
    }
    
    private function calculatePlacementScore(array $space, array $objectData, array $roomData): float {
        $score = 0.0;
        
        // Size compatibility (40% weight)
        $sizeScore = $this->calculateSizeCompatibility($space, $objectData);
        $score += $sizeScore * 0.4;
        
        // Style compatibility (25% weight)
        $styleScore = $this->calculateStyleCompatibility($objectData, $roomData);
        $score += $styleScore * 0.25;
        
        // Functional placement (20% weight)
        $functionalScore = $this->calculateFunctionalScore($space, $objectData, $roomData);
        $score += $functionalScore * 0.2;
        
        // Aesthetic placement (15% weight)
        $aestheticScore = $this->calculateAestheticScore($space, $objectData, $roomData);
        $score += $aestheticScore * 0.15;
        
        return min(1.0, max(0.0, $score));
    }
    
    private function generateReasoning(array $space, array $objectData, array $roomData): string {
        $reasons = [];
        
        // Size reasoning
        if ($space['dimensions']['width'] > $objectData['dimensions']['width'] * 1.2) {
            $reasons[] = "Plenty of space for the object";
        }
        
        // Style reasoning
        if ($this->calculateStyleCompatibility($objectData, $roomData) > 0.7) {
            $reasons[] = "Matches the room's style and color scheme";
        }
        
        // Functional reasoning
        if ($space['type'] === 'corner' && $objectData['category'] === 'storage') {
            $reasons[] = "Corner placement maximizes storage efficiency";
        }
        
        return implode('. ', $reasons) . '.';
    }
}
```

### **Phase 4: Visualization & UI (Week 7-8)**

#### 4.1 3D Visualization Service
```php
<?php
// app/Services/VisualizationService.php

class VisualizationService {
    public function generatePlacementPreview(array $roomData, array $objectData, array $placement): string {
        // Create a visual representation of the placement
        $canvas = new Imagick();
        $canvas->newImage(1024, 768, new ImagickPixel('white'));
        
        // Load room image
        $roomImage = new Imagick($roomData['image_path']);
        $roomImage->resizeImage(1024, 768, Imagick::FILTER_LANCZOS, 1, true);
        
        // Composite room image
        $canvas->compositeImage($roomImage, Imagick::COMPOSITE_OVER, 0, 0);
        
        // Add object placement visualization
        $this->drawObjectPlacement($canvas, $objectData, $placement);
        
        // Add placement indicators
        $this->drawPlacementIndicators($canvas, $placement);
        
        $previewPath = $this->getPreviewPath($roomData['id'], $objectData['id']);
        $canvas->writeImage($previewPath);
        
        return $previewPath;
    }
    
    private function drawObjectPlacement(Imagick $canvas, array $objectData, array $placement): void {
        $draw = new ImagickDraw();
        
        // Set object outline color
        $draw->setFillColor('rgba(0, 255, 0, 0.3)');
        $draw->setStrokeColor('green');
        $draw->setStrokeWidth(2);
        
        // Draw object placement rectangle
        $x = $placement['position']['x'] - $objectData['dimensions']['width'] / 2;
        $y = $placement['position']['y'] - $objectData['dimensions']['height'] / 2;
        $width = $objectData['dimensions']['width'];
        $height = $objectData['dimensions']['height'];
        
        $draw->rectangle($x, $y, $x + $width, $y + $height);
        
        $canvas->drawImage($draw);
    }
    
    private function drawPlacementIndicators(Imagick $canvas, array $placement): void {
        $draw = new ImagickDraw();
        
        // Draw confidence indicator
        $confidence = $placement['confidence'];
        $color = $this->getConfidenceColor($confidence);
        
        $draw->setFillColor($color);
        $draw->circle($placement['position']['x'], $placement['position']['y'], 
                     $placement['position']['x'] + 10, $placement['position']['y'] + 10);
        
        $canvas->drawImage($draw);
    }
}
```

#### 4.2 API Endpoints
```php
<?php
// app/Controllers/AIPlacementController.php

class AIPlacementController extends BaseController {
    private $placementService;
    private $imageProcessor;
    
    public function __construct() {
        $this->placementService = new PlacementService();
        $this->imageProcessor = new ImageProcessor();
    }
    
    public function analyzeRoom(Request $request): JsonResponse {
        try {
            $roomImage = $request->file('room_image');
            $processedImage = $this->imageProcessor->preprocessRoomImage($roomImage->getPathname());
            
            $analysis = $this->placementService->analyzeRoom($processedImage);
            
            return $this->successResponse([
                'room_analysis' => $analysis,
                'processed_image' => $processedImage
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Room analysis failed: ' . $e->getMessage());
        }
    }
    
    public function analyzeObject(Request $request): JsonResponse {
        try {
            $objectImage = $request->file('object_image');
            $processedImage = $this->imageProcessor->preprocessObjectImage($objectImage->getPathname());
            
            $analysis = $this->placementService->analyzeObject($processedImage);
            
            return $this->successResponse([
                'object_analysis' => $analysis,
                'processed_image' => $processedImage
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Object analysis failed: ' . $e->getMessage());
        }
    }
    
    public function suggestPlacement(Request $request): JsonResponse {
        try {
            $roomAnalysisId = $request->input('room_analysis_id');
            $objectAnalysisId = $request->input('object_analysis_id');
            
            $suggestions = $this->placementService->suggestPlacement($roomAnalysisId, $objectAnalysisId);
            
            return $this->successResponse([
                'suggestions' => $suggestions
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Placement suggestion failed: ' . $e->getMessage());
        }
    }
    
    public function provideFeedback(Request $request): JsonResponse {
        try {
            $suggestionId = $request->input('suggestion_id');
            $feedback = $request->input('feedback'); // 'accepted', 'rejected', 'modified'
            $notes = $request->input('notes');
            
            $this->placementService->recordFeedback($suggestionId, $feedback, $notes);
            
            return $this->successResponse(['message' => 'Feedback recorded']);
        } catch (Exception $e) {
            return $this->errorResponse('Feedback recording failed: ' . $e->getMessage());
        }
    }
}
```

## 🚀 Implementation Timeline

### **Week 1-2: Foundation**
- [ ] Set up development environment
- [ ] Install required PHP extensions and libraries
- [ ] Create database schema
- [ ] Set up basic project structure

### **Week 3-4: Image Processing**
- [ ] Implement ImageProcessor class
- [ ] Set up OpenAI API integration
- [ ] Create room and object analysis services
- [ ] Test image preprocessing pipeline

### **Week 5-6: AI Logic**
- [ ] Implement PlacementEngine
- [ ] Create placement scoring algorithms
- [ ] Develop space calculation logic
- [ ] Test placement suggestions

### **Week 7-8: Visualization & UI**
- [ ] Create visualization service
- [ ] Build API endpoints
- [ ] Develop frontend interface
- [ ] Implement user feedback system

## 💰 Cost Estimation

### **Development Costs**
- **OpenAI API**: ~$0.01-0.03 per image analysis
- **Google Cloud Vision**: ~$1.50 per 1,000 images
- **Server Resources**: ~$50-100/month for processing
- **Storage**: ~$10-20/month for image storage

### **Monthly Operational Costs** (1000 users)
- **API Calls**: ~$200-300/month
- **Server**: ~$100/month
- **Storage**: ~$50/month
- **Total**: ~$350-450/month

## 🔧 Configuration

### **Environment Variables**
```env
OPENAI_API_KEY=your_openai_api_key
GOOGLE_CLOUD_PROJECT_ID=your_project_id
GOOGLE_CLOUD_CREDENTIALS_PATH=path/to/credentials.json
IMAGE_PROCESSING_QUALITY=high
MAX_IMAGE_SIZE=10485760
CACHE_PLACEMENTS=true
```

### **Composer Dependencies**
```json
{
    "require": {
        "rubix/ml": "^2.0",
        "guzzlehttp/guzzle": "^7.0",
        "openai-php/client": "^0.4",
        "google/cloud-vision": "^1.0",
        "intervention/image": "^2.7"
    }
}
```

## 📊 Success Metrics

### **Technical Metrics**
- Image processing time: < 5 seconds
- AI analysis accuracy: > 85%
- API response time: < 3 seconds
- User satisfaction: > 4.0/5.0

### **Business Metrics**
- Feature adoption rate: > 60%
- User retention: +25%
- Premium conversion: +15%
- Customer satisfaction: > 90%

## 🔄 Continuous Improvement

### **Learning System**
1. **Collect User Feedback**: Track accepted/rejected suggestions
2. **Analyze Patterns**: Identify successful placement patterns
3. **Update Algorithms**: Improve scoring based on feedback
4. **A/B Testing**: Test different placement strategies

### **Model Updates**
- Monthly retraining with new data
- Quarterly algorithm improvements
- Annual major feature updates

---

**Last Updated**: December 2024  
**Version**: 1.0  
**Maintainer**: Engr. Wilberto Pacheco Batista
