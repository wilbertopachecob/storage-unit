<?php
/**
 * Export Page
 * Provides interface for data export functionality
 */

// Include header
include_once __DIR__ . '/../header.php';

// Get user data
$user = \StorageUnit\Models\User::getCurrentUser();
if (!$user) {
    header('Location: /signin.php');
    exit;
}

// Get data for export options
$categories = \StorageUnit\Models\Category::getWithItemCount($user->getId());
$locations = \StorageUnit\Models\Location::getWithItemCount($user->getId());
$totalItems = \StorageUnit\Models\Item::getCountForUser($user->getId());
$totalQuantity = \StorageUnit\Models\Item::getTotalQuantityForUser($user->getId());
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-download"></i> Export Data
            </h1>
            
            <!-- Export Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3><?= $totalItems ?></h3>
                            <p class="mb-0">Total Items</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3><?= $totalQuantity ?></h3>
                            <p class="mb-0">Total Quantity</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3><?= count($categories) ?></h3>
                            <p class="mb-0">Categories</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3><?= count($locations) ?></h3>
                            <p class="mb-0">Locations</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="row">
                <!-- All Items Export -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-boxes"></i> All Items
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Export all your storage items with complete details including categories, locations, and metadata.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Item details and descriptions</li>
                                <li><i class="fas fa-check text-success"></i> Category and location information</li>
                                <li><i class="fas fa-check text-success"></i> Quantities and timestamps</li>
                                <li><i class="fas fa-check text-success"></i> Image references</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="/export/items.php" class="btn btn-primary btn-block">
                                <i class="fas fa-download"></i> Export All Items (CSV)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Categories Export -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-tags"></i> Categories
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Export your category structure with item counts and metadata.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Category names and colors</li>
                                <li><i class="fas fa-check text-success"></i> Item counts per category</li>
                                <li><i class="fas fa-check text-success"></i> Icons and timestamps</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="/export/categories.php" class="btn btn-info btn-block">
                                <i class="fas fa-download"></i> Export Categories (CSV)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Locations Export -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-map-marker-alt"></i> Locations
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Export your location hierarchy with item counts and full paths.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Location hierarchy</li>
                                <li><i class="fas fa-check text-success"></i> Full location paths</li>
                                <li><i class="fas fa-check text-success"></i> Item counts per location</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="/export/locations.php" class="btn btn-warning btn-block">
                                <i class="fas fa-download"></i> Export Locations (CSV)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Search Export -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-search"></i> Search Results
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Export filtered search results with custom criteria.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Custom search terms</li>
                                <li><i class="fas fa-check text-success"></i> Category and location filters</li>
                                <li><i class="fas fa-check text-success"></i> Filtered results only</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="/index.php?script=search" class="btn btn-secondary btn-block">
                                <i class="fas fa-search"></i> Go to Search & Export
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category-specific Exports -->
            <?php if (!empty($categories)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <h4><i class="fas fa-tags"></i> Export by Category</h4>
                    <div class="row">
                        <?php foreach ($categories as $category): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="<?= htmlspecialchars($category['icon']) ?>" style="color: <?= htmlspecialchars($category['color']) ?>"></i>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </h6>
                                    <p class="card-text">
                                        <span class="badge badge-primary"><?= $category['item_count'] ?> items</span>
                                    </p>
                                    <a href="/export/category.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i> Export
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Location-specific Exports -->
            <?php if (!empty($locations)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <h4><i class="fas fa-map-marker-alt"></i> Export by Location</h4>
                    <div class="row">
                        <?php foreach ($locations as $location): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-folder"></i>
                                        <?= htmlspecialchars($location['name']) ?>
                                    </h6>
                                    <p class="card-text">
                                        <span class="badge badge-primary"><?= $location['item_count'] ?> items</span>
                                    </p>
                                    <a href="/export/location.php?id=<?= $location['id'] ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-download"></i> Export
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Export Information -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Export Information</h5>
                        <ul class="mb-0">
                            <li><strong>Format:</strong> CSV (Comma-Separated Values)</li>
                            <li><strong>Encoding:</strong> UTF-8 with BOM for Excel compatibility</li>
                            <li><strong>Timestamps:</strong> All dates are in your local timezone</li>
                            <li><strong>File Naming:</strong> Files include timestamp for uniqueness</li>
                            <li><strong>Data Security:</strong> Only your data is exported, no shared information</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once __DIR__ . '/../footer.php';
?>
