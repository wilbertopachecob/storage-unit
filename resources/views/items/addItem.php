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
<div class="container mt-3 mb-3">
    <div class="row">
        <div class="col-md-3 offset-md-4">
        <h1 class="text-center" style="color: #111; font-family: 'Rancho', serif; font-weight: bolder;">Add Item</h1>
        <div class="dropdown-divider"></div>
          <form method="post" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>?script=addItem">
  <div class="form-group">
    <label for="title">Title*</label>
    <input type="text" class="form-control" name="title" id="title" />
  </div>
  <div class="form-group">
    <label for="qty">Quantity</label>
    <input type="number" class="form-control" name="qty" id="qty" value="1" />
  </div>
  <div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control" name="description" id="description" rows="3"></textarea>
  </div>
  
  <!-- Category Selection -->
  <div class="form-group">
    <label for="category_id">Category</label>
    <select class="form-control" name="category_id" id="category_id">
      <option value="">Select a category (optional)</option>
      <?php foreach ($categories as $category): ?>
        <option value="<?= $category['id'] ?>" style="color: <?= htmlspecialchars($category['color']) ?>">
          <i class="<?= htmlspecialchars($category['icon']) ?>"></i> <?= htmlspecialchars($category['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  
  <!-- Location Selection -->
  <div class="form-group">
    <label for="location_id">Location</label>
    <select class="form-control" name="location_id" id="location_id">
      <option value="">Select a location (optional)</option>
      <?php foreach ($locations as $location): ?>
        <option value="<?= $location['id'] ?>">
          <?= htmlspecialchars($location['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  
  <div class="form-group">
    <label for="description">Upload image</label>
  </div>
  <div class="input-group mb-3">
  <div class="custom-file">
    <input type="file" class="custom-file-input" name="img" id="inputGroupFile03" aria-describedby="inputGroupFileAddon03">
    <label style="overflow: hidden;" class="custom-file-label" for="inputGroupFile03">Choose file</label>
  </div>
</div>
<div class="form-group">
    <img scr="" style="display:none" class="img-thumbnail" id="imagePreview" />
  </div>
  <button class="btn btn-primary" type="submit" name="btn_submit">
      Add
  </button>
</form>  
        </div>
    </div>
</div>
