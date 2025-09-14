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
            // If the item already has an image and the user is not 
            // uploading a new one then $img = image value on DB else
            // $img equal to new value or null
            if(isset($_POST['imagen']) && $_FILES['img']['size'] == 0){
                $img = $_POST['imagen'];     
            }
            else{
                $img = $file_name ?? null;
            }            
            //editItem($id, $title, $description, $qty, $img, $categoryId, $locationId):bool
            $id = $_GET['id'];
            $user_id = $_SESSION['user_id'];
            $controller = new \StorageUnit\Controllers\EnhancedItemController;
            $item = new \StorageUnit\Models\Item($title, $description, $qty, $user_id, $img, $categoryId, $locationId);
            $item->setDb(new \StorageUnit\Database\Connection);          
            $item = $controller->update($id);
            if ($item) {
                $messages[] = 'Item successfuly edited';
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
endif
?>

<?php
//Showing errors
if (isset($errors) && count($errors) > 0):
    foreach ($errors as $error):
?>
<div class="alert alert-danger" role="alert">
    <?=$error?>
</div>
<?php
endforeach;
endif
?>

<div class="container mt-3 mb-3">
    <div class="row">
        <?php
//Searching for the item
if (isset($_GET['id'])):
    $item_id = $_GET['id'];
    //echo $_GET['id'];
    $controller = new \StorageUnit\Controllers\ItemController;
    $conn = new \StorageUnit\Database\Connection;
    $item = $controller->getItemById($item_id, $conn);
    //echo var_dump($item);
    ?>
        <div class="col-md-3 offset-md-4">
        <h1 class="text-center" style="color: #111; font-family: 'Rancho', serif; font-weight: bolder;">Edit Item</h1>
        <div class="dropdown-divider"></div>
            <form method="post" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>?script=editItem&id=<?=$item[0]['id']?>">
                <?php
    if (isset($item[0]['img'])):
        //echo gettype($item[0]['img']);
    ?>
    <div style="position: relative;" class="image-del-container">
                <i class="fas fa-times-circle float-right delete_img" title="Delete image"></i>
                <img src="/uploads/<?=$item[0]['img']?>" class="img-fluid edit_img" alt="<?=$item[0]['title']?>" />
    </div>
                <?php
endif;
?>
                <div class="form-group">
                    <label for="description">Upload image</label>
                </div>
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <!-- Checking if the item has an image to update the DB-->
                        <?php 
                        if(isset($item[0]['img'])):
                        ?>
                        <input type="hidden" name="imagen" value="<?=$item[0]['img']?>" />
                        <?php endif; ?>
                        <input type="file" class="custom-file-input" name="img" id="inputGroupFile03" aria-describedby="inputGroupFileAddon03">
                        <label style="overflow: hidden;" class="custom-file-label" for="inputGroupFile03">Choose file</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title">Title*</label>
                    <input type="text" class="form-control" name="title" id="title" value="<?=$item[0]['title']?>" />
                </div>
                <div class="form-group">
                    <label for="qty">Quantity</label>
                    <input type="number" class="form-control" name="qty" id="qty" value="<?=$item[0]['qty']?>" />
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="3"><?=$item[0]['description']?></textarea>
                </div>
                
                <!-- Category Selection -->
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select class="form-control" name="category_id" id="category_id">
                        <option value="">Select a category (optional)</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                    <?= (isset($item[0]['category_id']) && $item[0]['category_id'] == $category['id']) ? 'selected' : '' ?>
                                    style="color: <?= htmlspecialchars($category['color']) ?>">
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
                            <option value="<?= $location['id'] ?>" 
                                    <?= (isset($item[0]['location_id']) && $item[0]['location_id'] == $location['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($location['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-danger" type="submit" name="btn_delete">
                <i class="fas fa-trash-alt"></i>
                Delete
                </button>
                <button class="btn btn-primary" type="submit" name="btn_submit">
                <i class="fas fa-edit"></i>    
                Edit
                </button>
            </form>

        </div>

        <?php
else:
?>
        <div class="col-sm-12">
            <div class="jumbotron">
                <h1 class="display-4">Oooops. Something when wrong.</h1>
                <p class="lead">No item was selected.</p>
                <hr class="my-4">
            </div>
        </div>
        <?php
endif;
?>
    </div>
</div>