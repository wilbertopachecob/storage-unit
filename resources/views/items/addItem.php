<?php
// Get user data
$user = \StorageUnit\Models\User::getCurrentUser();
if (!$user) {
    header('Location: /signin.php');
    exit;
}

// Get categories and locations for the form
$categories = \StorageUnit\Models\Category::getAllForUser($user->getId());
$locations = \StorageUnit\Models\Location::getAllForUser($user->getId());

if (isset($_POST['btn_submit'])):
    $errors = [];
    if ($_POST['title'] != ''):
        $title = $_POST['title'];
        $description = (isset($_POST['description']) && $_POST['description'] != '') ? $_POST['description'] : null;
        $qty = $_POST['qty'] ?? 1;
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $locationId = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;
        
        // Validate category
        if ($categoryId) {
            $category = \StorageUnit\Models\Category::findById($categoryId, $user->getId());
            if (!$category) {
                $errors[] = 'Selected category not found';
            }
        }
        
        // Validate location
        if ($locationId) {
            $location = \StorageUnit\Models\Location::findById($locationId, $user->getId());
            if (!$location) {
                $errors[] = 'Selected location not found';
            }
        }
        
        if ($_FILES['img']['size'] != 0) {
            $imageProcessor = new \StorageUnit\Helpers\ImageProcessor();
            
            // Validate image
            $validationErrors = $imageProcessor->validateImage($_FILES['img']);
            if (!empty($validationErrors)) {
                $errors = array_merge($errors, $validationErrors);
            } else {
                // Generate optimized filename
                $file_name = $imageProcessor->generateOptimizedFilename($_FILES['img']['name']);
                $file_tmp = $_FILES['img']['tmp_name'];
                
                // Process and optimize image
                try {
                    $uploadPath = "uploads/" . $file_name;
                    $thumbnailPath = "uploads/thumbnails/" . $imageProcessor->generateOptimizedFilename($_FILES['img']['name'], 'thumb');
                    
                    // Create thumbnails directory if it doesn't exist
                    if (!is_dir("uploads/thumbnails")) {
                        mkdir("uploads/thumbnails", 0755, true);
                    }
                    
                    // Process main image
                    if (!$imageProcessor->processImage($file_tmp, $uploadPath)) {
                        $errors[] = 'Failed to process image';
                    }
                    
                    // Create thumbnail
                    if (!$imageProcessor->createThumbnail($file_tmp, $thumbnailPath)) {
                        $errors[] = 'Failed to create thumbnail';
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = 'Image processing error: ' . $e->getMessage();
                }
            }
        }
        //Then I can insert into the database
        if (empty($errors)) {
            $img = $file_name ?? null;
            //addItem($title, $description, $qty, $img, $categoryId, $locationId)
            $user_id = $_SESSION['user_id'];
            $controller = new \StorageUnit\Controllers\EnhancedItemController;
            $item = new \StorageUnit\Models\Item($title, $description, $qty, $user_id, $img, $categoryId, $locationId);
            $item->setDb(new \StorageUnit\Database\Connection);
            $item = $controller->addItem($item);
            if ($item) {
                $messages[] = 'Item successfully added';
            }

        } else {
            $error[] = 'Title is mandatory';
        }

    endif;

endif;
?>
<?php
if (isset($messages) && count($messages) > 0):
?>
<div class="alert alert-success" role="alert">
  <?=$messages[0]?>
</div>
<?php
endif;
?>

<?php
if (isset($errors) && count($errors) > 0):
?>
<div class="alert alert-danger" role="alert">
  <ul class="mb-0">
    <?php foreach ($errors as $error): ?>
      <li><?= htmlspecialchars($error) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php
endif;
?>
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-10">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center py-4">
                <h2 class="mb-0 font-weight-bold">
                    <i class="fas fa-plus-circle mr-2"></i>Add New Item
                </h2>
                <p class="mb-0 mt-2 opacity-75">Organize your storage with detailed item information</p>
            </div>
            <div class="card-body p-4">
          <form method="post" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>?script=addItem">
  <div class="form-group">
    <label for="title" class="form-label font-weight-bold text-dark">
      <i class="fas fa-tag text-primary mr-2"></i>Item Title <span class="text-danger">*</span>
    </label>
    <input type="text" class="form-control form-control-lg" name="title" id="title" 
           placeholder="Enter item name" required />
  </div>
  
  <div class="form-group">
    <label for="qty" class="form-label font-weight-bold text-dark">
      <i class="fas fa-hashtag text-primary mr-2"></i>Quantity
    </label>
    <input type="number" class="form-control form-control-lg" name="qty" id="qty" 
           value="1" min="1" placeholder="Enter quantity" />
  </div>
  
  <div class="form-group">
    <label for="description" class="form-label font-weight-bold text-dark">
      <i class="fas fa-align-left text-primary mr-2"></i>Description
    </label>
    <textarea class="form-control" name="description" id="description" rows="4"
              placeholder="Enter item description (optional)"></textarea>
  </div>
  
  <!-- Category and Location Row -->
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="category_id" class="form-label font-weight-bold text-dark">
          <i class="fas fa-folder text-primary mr-2"></i>Category
        </label>
        <select class="form-control form-control-lg" name="category_id" id="category_id">
          <option value="">Choose category (optional)</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>">
              <?= htmlspecialchars($category['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label for="location_id" class="form-label font-weight-bold text-dark">
          <i class="fas fa-map-marker-alt text-primary mr-2"></i>Location
        </label>
        <select class="form-control form-control-lg" name="location_id" id="location_id">
          <option value="">Choose location (optional)</option>
          <?php foreach ($locations as $location): ?>
            <option value="<?= $location['id'] ?>">
              <?= htmlspecialchars($location['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>
  
  <div class="form-group">
    <label for="inputGroupFile03" class="form-label font-weight-bold text-dark">
      <i class="fas fa-camera text-primary mr-2"></i>Item Image
    </label>
    <div class="custom-file">
      <input type="file" class="custom-file-input" name="img" id="inputGroupFile03" 
             accept="image/*" onchange="previewImage(this)">
      <label class="custom-file-label" for="inputGroupFile03">
        <i class="fas fa-cloud-upload-alt mr-2"></i>Choose image file
      </label>
    </div>
    <small class="form-text text-muted">
      <i class="fas fa-info-circle mr-1"></i>
      Supported formats: JPG, PNG, GIF. Max size: 5MB
    </small>
  </div>
  
  <div class="form-group text-center" id="imagePreviewContainer" style="display: none;">
    <img src="" class="img-thumbnail" id="imagePreview" style="max-width: 200px; max-height: 200px;" />
    <div class="mt-2">
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
        <i class="fas fa-trash mr-1"></i>Remove Image
      </button>
    </div>
  </div>
  
  <div class="form-group mt-4">
    <button class="btn btn-primary btn-lg btn-block py-3 font-weight-bold" type="submit" name="btn_submit">
      <i class="fas fa-plus mr-2"></i>Add Item to Storage
    </button>
  </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('imagePreviewContainer');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const input = document.getElementById('inputGroupFile03');
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('imagePreviewContainer');
    const label = document.querySelector('.custom-file-label');
    
    input.value = '';
    preview.src = '';
    container.style.display = 'none';
    label.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i>Choose image file';
}
</script>
