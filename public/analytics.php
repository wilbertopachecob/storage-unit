<?php
/**
 * Analytics Page
 * Serves the React analytics dashboard with proper header
 */

session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app/config.php';
require_once __DIR__ . '/../config/app/constants.php';
require_once __DIR__ . '/../app/Helpers/helpers.php';
require_once __DIR__ . '/../app/Middleware/guards.php';

// Check if user is logged in
if (!isloggedIn()) {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    header("Location: " . $protocol . "://" . $host . "/signin.php");
    exit;
}

// Include the header
include_once __DIR__ . '/../resources/views/header.php';
?>

<!-- React App Container -->
<div id="root">
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3">Loading Analytics Dashboard...</p>
            </div>
        </div>
    </div>
</div>

<!-- React and ReactDOM from CDN -->
<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

<!-- Chart.js for charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Initialize React app when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing React app...');
    
    // Check if React is available
    if (typeof React === 'undefined' || typeof ReactDOM === 'undefined') {
        console.error('React or ReactDOM not available');
        document.getElementById('root').innerHTML = '<div class="alert alert-danger">React libraries not loaded. Please refresh the page.</div>';
        return;
    }
    
    console.log('React and ReactDOM available, creating analytics dashboard...');
    
    // Create the Analytics Dashboard React component
    const AnalyticsDashboard = () => {
        const [data, setData] = React.useState(null);
        const [loading, setLoading] = React.useState(true);
        const [error, setError] = React.useState(null);

        React.useEffect(() => {
            console.log('Making API call to /api/analytics.php');
            fetch('/api/analytics.php', {
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('API response status:', response.status);
                return response.json();
            })
            .then(result => {
                console.log('API response data:', result);
                if (result.success) {
                    setData(result.data);
                } else {
                    setError(result.message || 'API returned success: false');
                }
                setLoading(false);
            })
            .catch(err => {
                console.error('API Error:', err);
                setError(err.message || 'Failed to load analytics data');
                setLoading(false);
            });
        }, []);

        if (loading) {
            return React.createElement('div', { className: 'container mt-4' },
                React.createElement('div', { className: 'row' },
                    React.createElement('div', { className: 'col-12 text-center' },
                        React.createElement('div', { className: 'spinner-border text-primary', role: 'status' },
                            React.createElement('span', { className: 'sr-only' }, 'Loading...')
                        ),
                        React.createElement('p', { className: 'mt-3' }, 'Loading Analytics Dashboard...')
                    )
                )
            );
        }

        if (error) {
            return React.createElement('div', { className: 'container mt-4' },
                React.createElement('div', { className: 'alert alert-danger' },
                    React.createElement('h4', null, 'Error Loading Analytics'),
                    React.createElement('p', null, error),
                    React.createElement('button', { 
                        className: 'btn btn-primary', 
                        onClick: () => window.location.reload() 
                    }, 'Retry')
                )
            );
        }

        if (!data) {
            return React.createElement('div', { className: 'container mt-4' },
                React.createElement('div', { className: 'alert alert-warning' }, 'No data available')
            );
        }

        // Render the dashboard matching the original design
        return React.createElement('div', { className: 'container mt-4' },
            // Page title
            React.createElement('div', { className: 'row' },
                React.createElement('div', { className: 'col-12' },
                    React.createElement('h1', { className: 'mb-4' },
                        React.createElement('i', { className: 'fas fa-chart-bar' }),
                        ' Analytics Dashboard'
                    )
                )
            ),
            
            // Key Metrics Cards
            React.createElement('div', { className: 'row mb-4' },
                React.createElement('div', { className: 'col-md-3' },
                    React.createElement('div', { className: 'card bg-primary text-white' },
                        React.createElement('div', { className: 'card-body text-center' },
                            React.createElement('h2', { className: 'mb-0' }, data.total_items),
                            React.createElement('p', { className: 'mb-0' }, 'Total Items')
                        )
                    )
                ),
                React.createElement('div', { className: 'col-md-3' },
                    React.createElement('div', { className: 'card bg-success text-white' },
                        React.createElement('div', { className: 'card-body text-center' },
                            React.createElement('h2', { className: 'mb-0' }, data.total_quantity),
                            React.createElement('p', { className: 'mb-0' }, 'Total Quantity')
                        )
                    )
                ),
                React.createElement('div', { className: 'col-md-3' },
                    React.createElement('div', { className: 'card bg-info text-white' },
                        React.createElement('div', { className: 'card-body text-center' },
                            React.createElement('h2', { className: 'mb-0' }, data.items_by_category.length),
                            React.createElement('p', { className: 'mb-0' }, 'Categories')
                        )
                    )
                ),
                React.createElement('div', { className: 'col-md-3' },
                    React.createElement('div', { className: 'card bg-warning text-white' },
                        React.createElement('div', { className: 'card-body text-center' },
                            React.createElement('h2', { className: 'mb-0' }, data.items_by_location.length),
                            React.createElement('p', { className: 'mb-0' }, 'Locations')
                        )
                    )
                )
            ),
            
            // Charts Row
            React.createElement('div', { className: 'row' },
                // Items by Category Chart
                React.createElement('div', { className: 'col-md-6 mb-4' },
                    React.createElement('div', { className: 'card' },
                        React.createElement('div', { className: 'card-header' },
                            React.createElement('h5', { className: 'mb-0' },
                                React.createElement('i', { className: 'fas fa-tags' }),
                                ' Items by Category'
                            )
                        ),
                        React.createElement('div', { className: 'card-body' },
                            data.items_by_category.length > 0 ?
                                React.createElement('canvas', { id: 'categoryChart', width: 400, height: 200 }) :
                                React.createElement('p', { className: 'text-muted text-center' }, 'No categories yet')
                        )
                    )
                ),
                
                // Items by Location Chart
                React.createElement('div', { className: 'col-md-6 mb-4' },
                    React.createElement('div', { className: 'card' },
                        React.createElement('div', { className: 'card-header' },
                            React.createElement('h5', { className: 'mb-0' },
                                React.createElement('i', { className: 'fas fa-map-marker-alt' }),
                                ' Items by Location'
                            )
                        ),
                        React.createElement('div', { className: 'card-body' },
                            data.items_by_location.length > 0 ?
                                React.createElement('canvas', { id: 'locationChart', width: 400, height: 200 }) :
                                React.createElement('p', { className: 'text-muted text-center' }, 'No locations yet')
                        )
                    )
                )
            ),
            
            // Monthly Chart and Quick Stats Row
            React.createElement('div', { className: 'row' },
                // Monthly Items Added Chart
                React.createElement('div', { className: 'col-md-8 mb-4' },
                    React.createElement('div', { className: 'card' },
                        React.createElement('div', { className: 'card-header' },
                            React.createElement('h5', { className: 'mb-0' },
                                React.createElement('i', { className: 'fas fa-calendar-alt' }),
                                ' Items Added Over Time'
                            )
                        ),
                        React.createElement('div', { className: 'card-body' },
                            data.monthly_data && Object.keys(data.monthly_data).length > 0 ?
                                React.createElement('canvas', { id: 'monthlyChart', width: 400, height: 200 }) :
                                React.createElement('p', { className: 'text-muted text-center' }, 'No data available')
                        )
                    )
                ),
                
                // Quick Stats
                React.createElement('div', { className: 'col-md-4 mb-4' },
                    React.createElement('div', { className: 'card' },
                        React.createElement('div', { className: 'card-header' },
                            React.createElement('h5', { className: 'mb-0' },
                                React.createElement('i', { className: 'fas fa-info-circle' }),
                                ' Quick Stats'
                            )
                        ),
                        React.createElement('div', { className: 'card-body' },
                            React.createElement('div', { className: 'row text-center' },
                                React.createElement('div', { className: 'col-6 mb-3' },
                                    React.createElement('h4', { className: 'text-primary' }, data.items_without_images || 0),
                                    React.createElement('small', { className: 'text-muted' }, 'Without Images')
                                ),
                                React.createElement('div', { className: 'col-6 mb-3' },
                                    React.createElement('h4', { className: 'text-success' }, data.items_with_images || 0),
                                    React.createElement('small', { className: 'text-muted' }, 'With Images')
                                ),
                                React.createElement('div', { className: 'col-6 mb-3' },
                                    React.createElement('h4', { className: 'text-info' }, data.image_coverage || 0, '%'),
                                    React.createElement('small', { className: 'text-muted' }, 'Image Coverage')
                                ),
                                React.createElement('div', { className: 'col-6 mb-3' },
                                    React.createElement('h4', { className: 'text-warning' }, data.avg_quantity || 0),
                                    React.createElement('small', { className: 'text-muted' }, 'Avg Quantity')
                                )
                            )
                        )
                    )
                )
            ),
            
            // Recent Items
            React.createElement('div', { className: 'row' },
                React.createElement('div', { className: 'col-12' },
                    React.createElement('div', { className: 'card' },
                        React.createElement('div', { className: 'card-header' },
                            React.createElement('h5', { className: 'mb-0' },
                                React.createElement('i', { className: 'fas fa-clock' }),
                                ' Recent Items'
                            )
                        ),
                        React.createElement('div', { className: 'card-body' },
                            data.recent_items && data.recent_items.length > 0 ?
                                React.createElement('div', { className: 'row' },
                                    data.recent_items.map((item, index) =>
                                        React.createElement('div', { key: index, className: 'col-md-4 mb-3' },
                                            React.createElement('div', { className: 'card' },
                                                React.createElement('div', { className: 'card-body' },
                                                    React.createElement('h6', { className: 'card-title' }, item.title),
                                                    React.createElement('p', { className: 'card-text' }, item.description),
                                                    React.createElement('div', { className: 'd-flex justify-content-between align-items-center' },
                                                        React.createElement('small', { className: 'text-muted' },
                                                            React.createElement('span', { 
                                                                className: 'badge', 
                                                                style: { backgroundColor: item.category_color }
                                                            }, item.category_name)
                                                        ),
                                                        React.createElement('small', { className: 'text-muted' }, 'Qty: ', item.qty)
                                                    ),
                                                    React.createElement('small', { className: 'text-muted d-block mt-2' },
                                                        React.createElement('i', { className: 'fas fa-map-marker-alt' }),
                                                        ' ', item.location_name
                                                    )
                                                )
                                            )
                                        )
                                    )
                                ) :
                                React.createElement('p', { className: 'text-muted text-center' }, 'No recent items')
                        )
                    )
                )
            )
        );
    };

    // Render the React component
    const rootElement = document.getElementById('root');
    const root = ReactDOM.createRoot(rootElement);
    root.render(React.createElement(AnalyticsDashboard));
    
    console.log('✅ React analytics dashboard initialized successfully!');
    
    // Initialize charts after React renders
    setTimeout(() => {
        initializeCharts(data);
    }, 100);
});

// Function to initialize Chart.js charts
function initializeCharts(data) {
    // Category Chart
    if (data && data.items_by_category && data.items_by_category.length > 0) {
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: data.items_by_category.map(cat => cat.name),
                    datasets: [{
                        data: data.items_by_category.map(cat => cat.count),
                        backgroundColor: data.items_by_category.map(cat => cat.color),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    // Location Chart
    if (data && data.items_by_location && data.items_by_location.length > 0) {
        const locationCtx = document.getElementById('locationChart');
        if (locationCtx) {
            new Chart(locationCtx, {
                type: 'bar',
                data: {
                    labels: data.items_by_location.map(loc => loc.name),
                    datasets: [{
                        label: 'Items',
                        data: data.items_by_location.map(loc => loc.count),
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
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    // Monthly Chart
    if (data && data.monthly_data && Object.keys(data.monthly_data).length > 0) {
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            const months = Object.keys(data.monthly_data);
            const values = Object.values(data.monthly_data);
            
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Items Added',
                        data: values,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
}
</script>

<?php
// Include footer
include_once __DIR__ . '/../resources/views/footer.php';
?>
