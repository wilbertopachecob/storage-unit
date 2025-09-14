<?php
// Check if user is logged in
if (!isloggedIn()) {
    header('Location: signin.php');
    exit;
}

// Get item ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ' . $_SERVER['PHP_SELF'] . '?script=itemsList');
    exit;
}

$item_id = $_GET['id'];
$controller = new ItemController;
$conn = new Connection;
$item = $controller->getItemById($item_id, $conn);

// Check if item exists and belongs to current user
if (!$item || empty($item)) {
    header('Location: ' . $_SERVER['PHP_SELF'] . '?script=itemsList');
    exit;
}

$item = $item[0]; // Get first (and only) item from array
$item['img'] = $item['img'] ?? 'image-not-found.png';
?>

<div class="container pt-3 pb-3">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h1 class="card-title text-center mb-0" style="font-family: 'Rancho', serif; font-size: 2.5rem;">
                        Item Details
                    </h1>
                </div>
                <div class="card-body">
                    <!-- Item Image -->
                    <div class="text-center mb-4">
                        <img src="/uploads/<?=$item['img']?>" 
                             class="img-fluid rounded" 
                             alt="<?=htmlspecialchars($item['title'])?>"
                             style="max-height: 400px; width: auto;">
                    </div>
                    
                    <!-- Item Information -->
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Title:</strong>
                        </div>
                        <div class="col-sm-8">
                            <h4 style="font-family: 'Rancho', serif; color: #333;">
                                <?=htmlspecialchars($item['title'])?>
                            </h4>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Quantity:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge badge-primary badge-lg" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                                <?=htmlspecialchars($item['qty'])?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-sm-8">
                            <p class="text-muted" style="font-size: 1.1rem; line-height: 1.6;">
                                <?=htmlspecialchars($item['description'])?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Created:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="text-muted">
                                <?=date('F j, Y \a\t g:i A', strtotime($item['created_at']))?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($item['updated_at'] && $item['updated_at'] !== $item['created_at']): ?>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Last Updated:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="text-muted">
                                <?=date('F j, Y \a\t g:i A', strtotime($item['updated_at']))?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Action Buttons -->
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-sm-6 mb-2">
                            <a href="<?=$_SERVER['PHP_SELF']?>?script=itemsList" 
                               class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i>
                                Back to List
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="<?=$_SERVER['PHP_SELF']?>?script=editItem&id=<?=$item['id']?>" 
                               class="btn btn-success btn-block">
                                <i class="fas fa-edit"></i>
                                Edit Item
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Clean up variables
unsetVariables([$controller, $conn]);
?>
