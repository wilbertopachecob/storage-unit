<?php
/**
 * Analytics Dashboard
 * Shows statistics and insights about storage items
 */

// Include header
include_once __DIR__ . '/../header.php';

// Get user data
$user = \StorageUnit\Models\User::getCurrentUser();
if (!$user) {
    header('Location: /signin.php');
    exit;
}

// Get analytics data
$controller = new \StorageUnit\Controllers\EnhancedItemController();
$analytics = $controller->analytics();

// Get additional data
$items = \StorageUnit\Models\Item::getAllWithDetails($user->getId());
$categories = \StorageUnit\Models\Category::getWithItemCount($user->getId());
$locations = \StorageUnit\Models\Location::getWithItemCount($user->getId());

// Calculate additional statistics
$totalValue = 0; // Placeholder for future value tracking
$recentItems = array_slice($items, 0, 5);
$itemsWithoutImages = array_filter($items, function($item) {
    return empty($item['img']);
});

// Group items by month for chart data
$monthlyData = [];
foreach ($items as $item) {
    $month = date('Y-m', strtotime($item['created_at']));
    if (!isset($monthlyData[$month])) {
        $monthlyData[$month] = 0;
    }
    $monthlyData[$month]++;
}
ksort($monthlyData);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-chart-bar"></i> Analytics Dashboard
            </h1>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2 class="mb-0"><?= $analytics['total_items'] ?></h2>
                    <p class="mb-0">Total Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2 class="mb-0"><?= $analytics['total_quantity'] ?></h2>
                    <p class="mb-0">Total Quantity</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h2 class="mb-0"><?= count($categories) ?></h2>
                    <p class="mb-0">Categories</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h2 class="mb-0"><?= count($locations) ?></h2>
                    <p class="mb-0">Locations</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Items by Category Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tags"></i> Items by Category
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($analytics['items_by_category'])): ?>
                        <canvas id="categoryChart" width="400" height="200"></canvas>
                    <?php else: ?>
                        <p class="text-muted text-center">No categories yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Items by Location Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt"></i> Items by Location
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($analytics['items_by_location'])): ?>
                        <canvas id="locationChart" width="400" height="200"></canvas>
                    <?php else: ?>
                        <p class="text-muted text-center">No locations yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Items Added -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> Items Added Over Time
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($monthlyData)): ?>
                        <canvas id="monthlyChart" width="400" height="200"></canvas>
                    <?php else: ?>
                        <p class="text-muted text-center">No data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Quick Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4 class="text-primary"><?= count($itemsWithoutImages) ?></h4>
                            <small class="text-muted">Without Images</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-success"><?= count($items) - count($itemsWithoutImages) ?></h4>
                            <small class="text-muted">With Images</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-info"><?= round((count($items) - count($itemsWithoutImages)) / max(count($items), 1) * 100, 1) ?>%</h4>
                            <small class="text-muted">Image Coverage</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-warning"><?= $analytics['total_quantity'] > 0 ? round($analytics['total_quantity'] / $analytics['total_items'], 1) : 0 ?></h4>
                            <small class="text-muted">Avg Quantity</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Items -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock"></i> Recent Items
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentItems)): ?>
                        <div class="row">
                            <?php foreach ($recentItems as $item): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= htmlspecialchars($item['title']) ?></h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <?= $item['category_name'] ? 'Category: ' . htmlspecialchars($item['category_name']) : 'No Category' ?><br>
                                                    <?= $item['location_name'] ? 'Location: ' . htmlspecialchars($item['location_name']) : 'No Location' ?><br>
                                                    Quantity: <?= $item['qty'] ?><br>
                                                    Added: <?= date('M j, Y', strtotime($item['created_at'])) ?>
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">No items yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Category Chart
<?php if (!empty($analytics['items_by_category'])): ?>
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: [<?= implode(',', array_map(function($cat) { return "'" . addslashes($cat['name']) . "'"; }, $analytics['items_by_category'])) ?>],
        datasets: [{
            data: [<?= implode(',', array_column($analytics['items_by_category'], 'count')) ?>],
            backgroundColor: [<?= implode(',', array_map(function($cat) { return "'" . $cat['color'] . "'"; }, $analytics['items_by_category'])) ?>]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
<?php endif; ?>

// Location Chart
<?php if (!empty($analytics['items_by_location'])): ?>
const locationCtx = document.getElementById('locationChart').getContext('2d');
new Chart(locationCtx, {
    type: 'bar',
    data: {
        labels: [<?= implode(',', array_map(function($loc) { return "'" . addslashes($loc['name']) . "'"; }, $analytics['items_by_location'])) ?>],
        datasets: [{
            label: 'Items',
            data: [<?= implode(',', array_column($analytics['items_by_location'], 'count')) ?>],
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
<?php endif; ?>

// Monthly Chart
<?php if (!empty($monthlyData)): ?>
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: [<?= implode(',', array_map(function($month) { return "'" . date('M Y', strtotime($month . '-01')) . "'"; }, array_keys($monthlyData))) ?>],
        datasets: [{
            label: 'Items Added',
            data: [<?= implode(',', array_values($monthlyData)) ?>],
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
<?php endif; ?>
</script>

<?php
// Include footer
include_once __DIR__ . '/../footer.php';
?>
