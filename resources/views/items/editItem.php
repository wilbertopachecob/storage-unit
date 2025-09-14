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

// Initialize variables
$errors = [];
$messages = [];
$item = null;

// Get item data for editing
if (isset($_GET['id'])) {
    $itemId = (int)$_GET['id'];
    $item = \StorageUnit\Models\Item::findById($itemId, $user->getId());
    
    if (!$item) {
        $errors[] = 'Item not found';
    }
} else {
    $errors[] = 'No item ID provided';
}

// Handle form submission
if (isset($_POST['btn_submit']) && $item) {
    $controller = new \StorageUnit\Controllers\EnhancedItemController();
    
    try {
        $result = $controller->update($item->getId());
        
        if ($result['success']) {
            $messages[] = $result['message'];
            // Refresh item data after successful update
            $item = \StorageUnit\Models\Item::findById($itemId, $user->getId());
        } else {
            $errors = array_merge($errors, $result['errors']);
        }
    } catch (\Exception $e) {
        $errors[] = 'Error updating item: ' . $e->getMessage();
    }
}
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
        <?php if ($item): ?>
        <div class="col-md-3 offset-md-4">
        <h1 class="text-center" style="color: #111; font-family: 'Rancho', serif; font-weight: bolder;">Edit Item</h1>
        <div class="dropdown-divider"></div>
            <form method="post" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>?script=editItem&id=<?=$item->getId()?>">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= \StorageUnit\Core\Security::generateCSRFToken() ?>" />
                
                <?php if ($item->getImg()): ?>
                <div style="position: relative;" class="image-del-container">
                    <i class="fas fa-times-circle float-right delete_img" title="Delete image"></i>
                    <img src="/uploads/<?= htmlspecialchars($item->getImg()) ?>" class="img-fluid edit_img" alt="<?= htmlspecialchars($item->getTitle()) ?>" />
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="description">Upload image</label>
                </div>
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <!-- Keep existing image if no new one uploaded -->
                        <?php if ($item->getImg()): ?>
                        <input type="hidden" name="imagen" value="<?= htmlspecialchars($item->getImg()) ?>" />
                        <?php endif; ?>
                        <input type="file" class="custom-file-input" name="img" id="inputGroupFile03" aria-describedby="inputGroupFileAddon03">
                        <label style="overflow: hidden;" class="custom-file-label" for="inputGroupFile03">Choose file</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title">Title*</label>
                    <input type="text" class="form-control" name="title" id="title" value="<?= htmlspecialchars($item->getTitle()) ?>" required />
                </div>
                <div class="form-group">
                    <label for="qty">Quantity</label>
                    <input type="number" class="form-control" name="qty" id="qty" value="<?= $item->getQty() ?>" min="1" />
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="3"><?= htmlspecialchars($item->getDescription() ?? '') ?></textarea>
                </div>
                
                <!-- Category Selection -->
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select class="form-control" name="category_id" id="category_id">
                        <option value="">Select a category (optional)</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                    <?= ($item->getCategoryId() == $category['id']) ? 'selected' : '' ?>
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
                                    <?= ($item->getLocationId() == $location['id']) ? 'selected' : '' ?>>
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

        <?php else: ?>
        <div class="col-sm-12">
            <div class="jumbotron">
                <h1 class="display-4">Oops. Something went wrong.</h1>
                <p class="lead">Item not found or no item was selected.</p>
                <hr class="my-4">
                <a class="btn btn-primary btn-lg" href="?script=itemsList" role="button">Back to Items</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>