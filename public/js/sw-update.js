/**
 * Service Worker Update Manager
 * Handles Service Worker updates and cache management
 */

class SWUpdateManager {
    constructor() {
        this.registration = null;
        this.init();
    }

    async init() {
        if ('serviceWorker' in navigator) {
            this.registration = await navigator.serviceWorker.ready;
            this.setupUpdateHandlers();
        }
    }

    setupUpdateHandlers() {
        // Listen for service worker updates
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            console.log('Service Worker updated, reloading page...');
            window.location.reload();
        });

        // Check for updates periodically
        setInterval(() => {
            this.checkForUpdates();
        }, 60000); // Check every minute
    }

    async checkForUpdates() {
        if (this.registration) {
            try {
                await this.registration.update();
                console.log('Service Worker update check completed');
            } catch (error) {
                console.error('Service Worker update check failed:', error);
            }
        }
    }

    async clearCache() {
        if ('caches' in window) {
            const cacheNames = await caches.keys();
            await Promise.all(
                cacheNames.map(cacheName => caches.delete(cacheName))
            );
            console.log('All caches cleared');
        }
    }

    async updateServiceWorker() {
        if (this.registration && this.registration.waiting) {
            // Tell the waiting service worker to skip waiting
            this.registration.waiting.postMessage({ type: 'SKIP_WAITING' });
        }
    }

    getCacheInfo() {
        if ('caches' in window) {
            return caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => 
                        caches.open(cacheName).then(cache => 
                            cache.keys().then(requests => ({
                                name: cacheName,
                                size: requests.length,
                                urls: requests.map(req => req.url)
                            }))
                        )
                    )
                );
            });
        }
        return Promise.resolve([]);
    }
}

// Initialize the update manager
document.addEventListener('DOMContentLoaded', () => {
    window.swUpdateManager = new SWUpdateManager();
});

// Export for use in other scripts
window.SWUpdateManager = SWUpdateManager;
