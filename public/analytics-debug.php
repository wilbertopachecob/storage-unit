<?php
/**
 * Analytics Page - Debug Version
 * Shows debugging information for the React app
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

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-chart-bar"></i> Analytics Dashboard - Debug
            </h1>
        </div>
    </div>

    <!-- Debug Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Debug Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Session ID:</strong> <?= session_id() ?></p>
                    <p><strong>User Logged In:</strong> <?= isloggedIn() ? 'Yes' : 'No' ?></p>
                    <p><strong>Current User:</strong> <?= \StorageUnit\Models\User::getCurrentUser() ? \StorageUnit\Models\User::getCurrentUser()->getName() : 'None' ?></p>
                    <p><strong>API Endpoint:</strong> <a href="/api/analytics.php" target="_blank">/api/analytics.php</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- React App Container -->
    <div id="root">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3">Loading React app...</p>
        </div>
    </div>
</div>

<!-- React and ReactDOM from CDN -->
<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

<script>
console.log('=== ANALYTICS DEBUG PAGE ===');
console.log('Page loaded, initializing React app...');

// Check if React is available
if (typeof React === 'undefined' || typeof ReactDOM === 'undefined') {
    console.error('React or ReactDOM not available');
    document.getElementById('root').innerHTML = '<div class="alert alert-danger">React libraries not loaded. Please refresh the page.</div>';
} else {
    console.log('React and ReactDOM available, creating debug component...');
    
    // Create a debug component that shows what's happening
    const DebugAnalyticsDashboard = () => {
        const [data, setData] = React.useState(null);
        const [loading, setLoading] = React.useState(true);
        const [error, setError] = React.useState(null);
        const [debugInfo, setDebugInfo] = React.useState([]);

        const addDebugInfo = (message) => {
            setDebugInfo(prev => [...prev, `${new Date().toLocaleTimeString()}: ${message}`]);
        };

        React.useEffect(() => {
            addDebugInfo('Starting API call to /api/analytics.php');
            
            fetch('/api/analytics.php', {
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                addDebugInfo(`API response status: ${response.status}`);
                return response.json();
            })
            .then(result => {
                addDebugInfo(`API response data: ${JSON.stringify(result).substring(0, 100)}...`);
                if (result.success) {
                    setData(result.data);
                    addDebugInfo('Data loaded successfully');
                } else {
                    setError(result.message || 'API returned success: false');
                    addDebugInfo(`API error: ${result.message}`);
                }
                setLoading(false);
            })
            .catch(err => {
                console.error('API Error:', err);
                setError(err.message || 'Failed to load analytics data');
                addDebugInfo(`API error: ${err.message}`);
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

        // Show a simple dashboard
        return React.createElement('div', { className: 'container mt-4' },
            React.createElement('div', { className: 'row' },
                React.createElement('div', { className: 'col-12' },
                    React.createElement('h2', null, 'Analytics Data Loaded Successfully!'),
                    React.createElement('p', null, `Total Items: ${data.total_items}`),
                    React.createElement('p', null, `Total Quantity: ${data.total_quantity}`),
                    React.createElement('p', null, `Categories: ${data.items_by_category.length}`),
                    React.createElement('p', null, `Locations: ${data.items_by_location.length}`)
                )
            )
        );
    };

    // Render the debug component
    const rootElement = document.getElementById('root');
    const root = ReactDOM.createRoot(rootElement);
    root.render(React.createElement(DebugAnalyticsDashboard));
    
    console.log('✅ Debug React component initialized successfully!');
}
</script>

<?php
// Include footer
include_once __DIR__ . '/../resources/views/footer.php';
?>
