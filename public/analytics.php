<?php
/**
 * Analytics Page
 * Serves the React analytics dashboard with proper authentication
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

<!-- Load React App from build directory -->
<script>
// Load React app using asset manifest
const loadReactApp = async () => {
    try {
        // Fetch the asset manifest to get current file names
        const manifestResponse = await fetch('/manifest.php');
        const manifest = await manifestResponse.json();
        
        // Load CSS first
        const cssLink = document.createElement('link');
        cssLink.rel = 'stylesheet';
        cssLink.href = manifest.files['main.css'];
        document.head.appendChild(cssLink);
        
        // Clear the loading content before loading React
        document.getElementById('root').innerHTML = '';
        
        // Load the React app bundle
        const script = document.createElement('script');
        script.src = manifest.files['main.js'];
        script.onload = () => {
            console.log('✅ React app loaded successfully');
            // Give React a moment to initialize
            setTimeout(() => {
                const rootElement = document.getElementById('root');
                console.log('Root element children count:', rootElement.children.length);
                if (rootElement.children.length === 0) {
                    console.warn('⚠️ React app loaded but no content rendered');
                    // Check if there are any React errors
                    console.log('Root element innerHTML:', rootElement.innerHTML);
                    
                    // Check if React is available
                    if (typeof React === 'undefined') {
                        console.error('❌ React is not available');
                    } else {
                        console.log('✅ React is available');
                    }
                    
                    if (typeof ReactDOM === 'undefined') {
                        console.error('❌ ReactDOM is not available');
                    } else {
                        console.log('✅ ReactDOM is available');
                    }
                } else {
                    console.log('✅ React content rendered successfully');
                }
            }, 2000);
        };
        script.onerror = () => {
            throw new Error('Failed to load React app bundle');
        };
        document.head.appendChild(script);
        
    } catch (error) {
        console.error('❌ Failed to load React app:', error);
        document.getElementById('root').innerHTML = `
            <div class="container mt-4">
                <div class="alert alert-warning">
                    <h4>React App Not Built</h4>
                    <p>Please build the React frontend first by running:</p>
                    <pre><code>./scripts/build-react.sh</code></pre>
                    <p>Or manually:</p>
                    <pre><code>cd react-frontend && npm run build</code></pre>
                    <p>Then run the build script to copy files to the public directory.</p>
                </div>
            </div>
        `;
    }
};

// Initialize when page loads
document.addEventListener('DOMContentLoaded', loadReactApp);
</script>

<?php
// Include footer
include_once __DIR__ . '/../resources/views/footer.php';
?>
