/**
 * Analytics Dashboard Loader
 * Handles loading and initialization of the React analytics dashboard
 */

class AnalyticsLoader {
    constructor() {
        this.manifestUrl = '/manifest.php';
        this.rootElement = document.getElementById('root');
        this.loadTimeout = 5000; // 5 seconds timeout
    }

    /**
     * Initialize the analytics dashboard
     */
    async init() {
        try {
            console.log('🚀 Initializing Analytics Dashboard...');
            
            // Load the asset manifest
            const manifest = await this.loadManifest();
            
            // Load CSS first
            await this.loadCSS(manifest);
            
            // Clear loading content
            this.clearLoadingContent();
            
            // Load the React app
            await this.loadReactApp(manifest);
            
        } catch (error) {
            console.error('❌ Failed to initialize analytics dashboard:', error);
            this.showError(error);
        }
    }

    /**
     * Load the asset manifest
     */
    async loadManifest() {
        try {
            const response = await fetch(this.manifestUrl);
            if (!response.ok) {
                throw new Error(`Failed to load manifest: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            throw new Error(`Manifest loading failed: ${error.message}`);
        }
    }

    /**
     * Load CSS files
     */
    async loadCSS(manifest) {
        return new Promise((resolve, reject) => {
            const cssLink = document.createElement('link');
            cssLink.rel = 'stylesheet';
            cssLink.href = manifest.files['main.css'];
            cssLink.onload = () => {
                console.log('✅ CSS loaded successfully');
                resolve();
            };
            cssLink.onerror = () => {
                reject(new Error('Failed to load CSS'));
            };
            document.head.appendChild(cssLink);
        });
    }

    /**
     * Load the React application
     */
    async loadReactApp(manifest) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = manifest.files['main.js'];
            
            // Set up timeout
            const timeout = setTimeout(() => {
                reject(new Error('React app loading timeout'));
            }, this.loadTimeout);

            script.onload = () => {
                clearTimeout(timeout);
                console.log('✅ React app loaded successfully');
                
                // Give React time to initialize
                setTimeout(() => {
                    this.verifyReactApp();
                    resolve();
                }, 1000);
            };

            script.onerror = () => {
                clearTimeout(timeout);
                reject(new Error('Failed to load React app bundle'));
            };

            document.head.appendChild(script);
        });
    }

    /**
     * Verify that React app has rendered content
     */
    verifyReactApp() {
        const childrenCount = this.rootElement.children.length;
        
        if (childrenCount === 0) {
            console.warn('⚠️ React app loaded but no content rendered');
            this.checkReactAvailability();
        } else {
            console.log('✅ React content rendered successfully');
        }
    }

    /**
     * Check if React and ReactDOM are available
     */
    checkReactAvailability() {
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
    }

    /**
     * Clear the loading content
     */
    clearLoadingContent() {
        this.rootElement.innerHTML = '';
    }

    /**
     * Show error message
     */
    showError(error) {
        this.rootElement.innerHTML = `
            <div class="container mt-4">
                <div class="alert alert-danger">
                    <h4><i class="fas fa-exclamation-triangle"></i> Analytics Dashboard Error</h4>
                    <p><strong>Error:</strong> ${error.message}</p>
                    <hr>
                    <h5>Possible Solutions:</h5>
                    <ol>
                        <li>Build the React frontend:
                            <pre><code>cd react-frontend && npm run build</code></pre>
                        </li>
                        <li>Copy build files to public directory:
                            <pre><code>cp -r react-frontend/build/* public/</code></pre>
                        </li>
                        <li>Check browser console for additional errors</li>
                        <li>Verify all dependencies are installed</li>
                    </ol>
                    <button class="btn btn-primary mt-3" onclick="location.reload()">
                        <i class="fas fa-refresh"></i> Retry
                    </button>
                </div>
            </div>
        `;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const loader = new AnalyticsLoader();
    loader.init();
});
