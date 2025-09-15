# AI Placement Feature - Quick Start Guide

## 🚀 Getting Started in 30 Minutes

This guide will help you implement the basic AI placement feature quickly using existing PHP libraries and AI services.

## 📋 Prerequisites

- PHP 8.1+ with Imagick extension
- Composer
- OpenAI API key (or Google Cloud Vision API)
- Basic understanding of PHP and APIs

## ⚡ Quick Setup

### 1. Install Dependencies

```bash
# Install PHP extensions
pecl install imagick

# Install Composer packages
composer require guzzlehttp/guzzle
composer require openai-php/client
composer require intervention/image
```

### 2. Basic Implementation

Create the core service class:

```php
<?php
// app/Services/AIPlacementService.php

class AIPlacementService {
    private $openaiClient;
    private $imageProcessor;
    
    public function __construct() {
        $this->openaiClient = new \OpenAI\Client($_ENV['OPENAI_API_KEY']);
        $this->imageProcessor = new ImageProcessor();
    }
    
    public function suggestPlacement(string $roomImagePath, string $objectImagePath): array {
        // Step 1: Process images
        $processedRoom = $this->imageProcessor->preprocessRoomImage($roomImagePath);
        $processedObject = $this->imageProcessor->preprocessObjectImage($objectImagePath);
        
        // Step 2: Analyze room
        $roomAnalysis = $this->analyzeRoom($processedRoom);
        
        // Step 3: Analyze object
        $objectAnalysis = $this->analyzeObject($processedObject);
        
        // Step 4: Generate suggestions
        $suggestions = $this->generateSuggestions($roomAnalysis, $objectAnalysis);
        
        return $suggestions;
    }
    
    private function analyzeRoom(string $imagePath): array {
        $response = $this->openaiClient->chat()->create([
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Analyze this room and return JSON with: room_type, dimensions, existing_furniture, available_spaces, lighting, style'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => ['url' => 'data:image/jpeg;base64,' . base64_encode(file_get_contents($imagePath))]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 500
        ]);
        
        return json_decode($response->choices[0]->message->content, true);
    }
    
    private function analyzeObject(string $imagePath): array {
        $response = $this->openaiClient->chat()->create([
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Analyze this object and return JSON with: object_type, dimensions, style, placement_type, color'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => ['url' => 'data:image/jpeg;base64,' . base64_encode(file_get_contents($imagePath))]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 300
        ]);
        
        return json_decode($response->choices[0]->message->content, true);
    }
    
    private function generateSuggestions(array $room, array $object): array {
        $suggestions = [];
        
        // Simple placement logic
        foreach ($room['available_spaces'] as $space) {
            if ($this->spaceFitsObject($space, $object)) {
                $suggestions[] = [
                    'position' => $space['center'],
                    'confidence' => $this->calculateConfidence($space, $object, $room),
                    'reasoning' => $this->generateReasoning($space, $object, $room)
                ];
            }
        }
        
        // Sort by confidence
        usort($suggestions, fn($a, $b) => $b['confidence'] <=> $a['confidence']);
        
        return array_slice($suggestions, 0, 3);
    }
    
    private function spaceFitsObject(array $space, array $object): bool {
        return $space['width'] >= $object['dimensions']['width'] && 
               $space['height'] >= $object['dimensions']['height'];
    }
    
    private function calculateConfidence(array $space, array $object, array $room): float {
        $score = 0.0;
        
        // Size fit (40%)
        $sizeFit = min(1.0, $space['width'] / $object['dimensions']['width']);
        $score += $sizeFit * 0.4;
        
        // Style match (30%)
        $styleMatch = $this->calculateStyleMatch($object['style'], $room['style']);
        $score += $styleMatch * 0.3;
        
        // Functional fit (30%)
        $functionalFit = $this->calculateFunctionalFit($space, $object, $room);
        $score += $functionalFit * 0.3;
        
        return min(1.0, $score);
    }
    
    private function calculateStyleMatch(string $objectStyle, string $roomStyle): float {
        $styleMap = [
            'modern' => ['contemporary', 'minimalist', 'sleek'],
            'traditional' => ['classic', 'vintage', 'antique'],
            'rustic' => ['farmhouse', 'country', 'natural'],
            'industrial' => ['urban', 'loft', 'metallic']
        ];
        
        foreach ($styleMap as $style => $variations) {
            if (in_array($objectStyle, $variations) && in_array($roomStyle, $variations)) {
                return 1.0;
            }
        }
        
        return 0.5; // Default partial match
    }
    
    private function calculateFunctionalFit(array $space, array $object, array $room): float {
        $score = 0.0;
        
        // Check if placement type matches
        if ($space['type'] === $object['placement_type']) {
            $score += 0.5;
        }
        
        // Check if it's not blocking important areas
        if (!$this->blocksImportantAreas($space, $room)) {
            $score += 0.3;
        }
        
        // Check lighting compatibility
        if ($this->hasGoodLighting($space, $room)) {
            $score += 0.2;
        }
        
        return min(1.0, $score);
    }
    
    private function generateReasoning(array $space, array $object, array $room): string {
        $reasons = [];
        
        if ($space['width'] > $object['dimensions']['width'] * 1.5) {
            $reasons[] = "Plenty of space available";
        }
        
        if ($this->calculateStyleMatch($object['style'], $room['style']) > 0.8) {
            $reasons[] = "Style matches the room perfectly";
        }
        
        if ($space['type'] === 'corner' && $object['object_type'] === 'storage') {
            $reasons[] = "Corner placement maximizes storage efficiency";
        }
        
        return implode('. ', $reasons) . '.';
    }
}
```

### 3. Image Processor

```php
<?php
// app/Services/ImageProcessor.php

class ImageProcessor {
    public function preprocessRoomImage(string $imagePath): string {
        $image = new Imagick($imagePath);
        
        // Resize to standard size
        $image->resizeImage(1024, 768, Imagick::FILTER_LANCZOS, 1, true);
        
        // Enhance quality
        $image->enhanceImage();
        $image->sharpenImage(0, 1);
        
        // Convert to RGB
        $image->transformImageColorspace(Imagick::COLORSPACE_RGB);
        
        $outputPath = $this->getProcessedPath($imagePath, 'room');
        $image->writeImage($outputPath);
        
        return $outputPath;
    }
    
    public function preprocessObjectImage(string $imagePath): string {
        $image = new Imagick($imagePath);
        
        // Remove background (basic)
        $image->transparentPaintImage(
            $image->getImagePixelColor(0, 0),
            0,
            Imagick::FUZZ * 0.1,
            false
        );
        
        // Resize maintaining aspect ratio
        $image->resizeImage(512, 512, Imagick::FILTER_LANCZOS, 1, true);
        
        $outputPath = $this->getProcessedPath($imagePath, 'object');
        $image->writeImage($outputPath);
        
        return $outputPath;
    }
    
    private function getProcessedPath(string $originalPath, string $type): string {
        $pathInfo = pathinfo($originalPath);
        return $pathInfo['dirname'] . '/processed_' . $type . '_' . $pathInfo['basename'];
    }
}
```

### 4. API Controller

```php
<?php
// app/Controllers/AIPlacementController.php

class AIPlacementController extends BaseController {
    private $placementService;
    
    public function __construct() {
        $this->placementService = new AIPlacementService();
    }
    
    public function suggestPlacement(Request $request): JsonResponse {
        try {
            // Validate input
            $request->validate([
                'room_image' => 'required|image|max:10240',
                'object_image' => 'required|image|max:10240'
            ]);
            
            // Get file paths
            $roomImage = $request->file('room_image');
            $objectImage = $request->file('object_image');
            
            // Generate suggestions
            $suggestions = $this->placementService->suggestPlacement(
                $roomImage->getPathname(),
                $objectImage->getPathname()
            );
            
            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### 5. Frontend Integration

```html
<!-- public/ai-placement.html -->
<!DOCTYPE html>
<html>
<head>
    <title>AI Placement Suggestions</title>
    <style>
        .upload-area {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .suggestion {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .confidence {
            background: #e8f5e8;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>AI Room Placement Suggestions</h1>
    
    <form id="placementForm" enctype="multipart/form-data">
        <div class="upload-area">
            <h3>Upload Room Image</h3>
            <input type="file" id="roomImage" name="room_image" accept="image/*" required>
        </div>
        
        <div class="upload-area">
            <h3>Upload Object Image</h3>
            <input type="file" id="objectImage" name="object_image" accept="image/*" required>
        </div>
        
        <button type="submit">Get AI Suggestions</button>
    </form>
    
    <div id="results"></div>
    
    <script>
        document.getElementById('placementForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('room_image', document.getElementById('roomImage').files[0]);
            formData.append('object_image', document.getElementById('objectImage').files[0]);
            
            try {
                const response = await fetch('/api/ai-placement/suggest', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    displaySuggestions(data.suggestions);
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });
        
        function displaySuggestions(suggestions) {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '<h2>AI Suggestions</h2>';
            
            suggestions.forEach((suggestion, index) => {
                const suggestionDiv = document.createElement('div');
                suggestionDiv.className = 'suggestion';
                suggestionDiv.innerHTML = `
                    <h3>Suggestion ${index + 1}</h3>
                    <p><strong>Position:</strong> X: ${suggestion.position.x}, Y: ${suggestion.position.y}</p>
                    <p><strong>Confidence:</strong> <span class="confidence">${Math.round(suggestion.confidence * 100)}%</span></p>
                    <p><strong>Reasoning:</strong> ${suggestion.reasoning}</p>
                `;
                resultsDiv.appendChild(suggestionDiv);
            });
        }
    </script>
</body>
</html>
```

## 🔧 Configuration

### Environment Variables (.env)
```env
OPENAI_API_KEY=your_openai_api_key_here
MAX_IMAGE_SIZE=10485760
IMAGE_PROCESSING_QUALITY=high
```

### Routes (routes/api.php)
```php
Route::post('/ai-placement/suggest', [AIPlacementController::class, 'suggestPlacement']);
```

## 🧪 Testing

### Basic Test
```php
<?php
// tests/Feature/AIPlacementTest.php

class AIPlacementTest extends TestCase {
    public function test_ai_placement_suggestion() {
        $roomImage = UploadedFile::fake()->image('room.jpg');
        $objectImage = UploadedFile::fake()->image('object.jpg');
        
        $response = $this->postJson('/api/ai-placement/suggest', [
            'room_image' => $roomImage,
            'object_image' => $objectImage
        ]);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'suggestions' => [
                        '*' => [
                            'position',
                            'confidence',
                            'reasoning'
                        ]
                    ]
                ]);
    }
}
```

## 🚀 Deployment

### 1. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 2. Set Environment Variables
```bash
export OPENAI_API_KEY="your_key_here"
```

### 3. Test the Feature
```bash
php artisan test --filter=AIPlacementTest
```

## 📊 Monitoring

### Basic Logging
```php
// Add to AIPlacementService
Log::info('AI Placement Request', [
    'room_image' => $roomImagePath,
    'object_image' => $objectImagePath,
    'suggestions_count' => count($suggestions)
]);
```

## 🎯 Next Steps

1. **Improve Accuracy**: Add more sophisticated placement algorithms
2. **Add Visualization**: Create visual previews of suggestions
3. **User Feedback**: Implement feedback collection system
4. **Caching**: Add Redis caching for repeated analyses
5. **Batch Processing**: Handle multiple objects at once

## 💡 Tips

- Start with high-quality, well-lit images for better results
- Test with different room types and object categories
- Monitor API costs and implement rate limiting
- Consider using image compression to reduce API costs
- Add error handling for network issues and API failures

---

**Ready to go!** This basic implementation will get you started with AI-powered room placement suggestions in your storage unit management system.
