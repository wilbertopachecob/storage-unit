/**
 * Offline Manager
 * Handles offline functionality and data synchronization
 */

class OfflineManager {
    constructor() {
        this.isOnline = navigator.onLine;
        this.pendingActions = [];
        this.dbName = 'StorageUnitOffline';
        this.dbVersion = 1;
        this.db = null;
        
        this.init();
    }

    async init() {
        // Register service worker
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker registered:', registration);
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }

        // Initialize IndexedDB
        await this.initIndexedDB();

        // Set up event listeners
        this.setupEventListeners();

        // Check for pending actions
        await this.checkPendingActions();
    }

    async initIndexedDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve();
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Create pending actions store
                if (!db.objectStoreNames.contains('pendingActions')) {
                    const store = db.createObjectStore('pendingActions', { 
                        keyPath: 'id', 
                        autoIncrement: true 
                    });
                    store.createIndex('type', 'type', { unique: false });
                    store.createIndex('timestamp', 'timestamp', { unique: false });
                }

                // Create offline items store
                if (!db.objectStoreNames.contains('offlineItems')) {
                    const store = db.createObjectStore('offlineItems', { 
                        keyPath: 'id' 
                    });
                    store.createIndex('title', 'title', { unique: false });
                    store.createIndex('category', 'category', { unique: false });
                    store.createIndex('location', 'location', { unique: false });
                }
            };
        });
    }

    setupEventListeners() {
        // Online/offline events
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.showOnlineStatus();
            this.syncPendingActions();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.showOfflineStatus();
        });

        // Intercept form submissions for offline handling
        document.addEventListener('submit', (e) => {
            if (!this.isOnline && this.shouldCacheForm(e.target)) {
                e.preventDefault();
                this.handleOfflineForm(e.target);
            }
        });

        // Intercept fetch requests for offline handling
        this.interceptFetch();
    }

    shouldCacheForm(form) {
        // Only cache certain forms
        const formId = form.id || form.className;
        return formId.includes('addItem') || 
               formId.includes('editItem') || 
               formId.includes('deleteItem');
    }

    async handleOfflineForm(form) {
        const formData = new FormData(form);
        const action = form.action;
        const method = form.method || 'POST';

        // Create pending action
        const pendingAction = {
            type: this.getFormType(form),
            url: action,
            method: method,
            data: Object.fromEntries(formData),
            timestamp: Date.now()
        };

        // Store in IndexedDB
        await this.storePendingAction(pendingAction);

        // Show success message
        this.showMessage('Action saved for when you\'re back online', 'info');

        // Reset form
        form.reset();
    }

    getFormType(form) {
        if (form.id.includes('addItem')) return 'add';
        if (form.id.includes('editItem')) return 'edit';
        if (form.id.includes('deleteItem')) return 'delete';
        return 'unknown';
    }

    async storePendingAction(action) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['pendingActions'], 'readwrite');
            const store = transaction.objectStore('pendingActions');
            const request = store.add(action);

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    async checkPendingActions() {
        if (!this.isOnline) return;

        try {
            const actions = await this.getPendingActions();
            if (actions.length > 0) {
                this.showMessage(`${actions.length} pending actions will be synced`, 'info');
                await this.syncPendingActions();
            }
        } catch (error) {
            console.error('Error checking pending actions:', error);
        }
    }

    async getPendingActions() {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['pendingActions'], 'readonly');
            const store = transaction.objectStore('pendingActions');
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    async syncPendingActions() {
        if (!this.isOnline) return;

        try {
            const actions = await this.getPendingActions();
            
            for (const action of actions) {
                try {
                    await this.syncAction(action);
                    await this.removePendingAction(action.id);
                } catch (error) {
                    console.error('Failed to sync action:', action, error);
                }
            }

            if (actions.length > 0) {
                this.showMessage('All pending actions synced successfully', 'success');
            }
        } catch (error) {
            console.error('Error syncing pending actions:', error);
        }
    }

    async syncAction(action) {
        const response = await fetch(action.url, {
            method: action.method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(action.data)
        });

        if (!response.ok) {
            throw new Error(`Sync failed: ${response.status}`);
        }

        return response;
    }

    async removePendingAction(id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['pendingActions'], 'readwrite');
            const store = transaction.objectStore('pendingActions');
            const request = store.delete(id);

            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    interceptFetch() {
        const originalFetch = window.fetch;
        
        window.fetch = async (url, options = {}) => {
            // If online, use normal fetch
            if (this.isOnline) {
                return originalFetch(url, options);
            }

            // If offline, try to serve from cache
            try {
                const response = await caches.match(url);
                if (response) {
                    return response;
                }
            } catch (error) {
                console.error('Cache match failed:', error);
            }

            // If no cache, return offline response
            return new Response('Offline - content not available', {
                status: 503,
                statusText: 'Service Unavailable'
            });
        };
    }

    showOnlineStatus() {
        this.showMessage('You\'re back online!', 'success');
    }

    showOfflineStatus() {
        this.showMessage('You\'re offline. Some features may be limited.', 'warning');
    }

    showMessage(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '6px';
        toast.style.color = 'white';
        toast.style.fontWeight = 'bold';
        toast.style.zIndex = '9999';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'transform 0.3s ease';

        // Set background color based on type
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            info: '#17a2b8',
            warning: '#ffc107'
        };
        toast.style.backgroundColor = colors[type] || colors.info;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);

        // Auto-remove
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    // Cache data for offline use
    async cacheData(key, data) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['offlineItems'], 'readwrite');
            const store = transaction.objectStore('offlineItems');
            const request = store.put({ id: key, data: data, timestamp: Date.now() });

            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    // Get cached data
    async getCachedData(key) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['offlineItems'], 'readonly');
            const store = transaction.objectStore('offlineItems');
            const request = store.get(key);

            request.onsuccess = () => {
                resolve(request.result ? request.result.data : null);
            };
            request.onerror = () => reject(request.error);
        });
    }

    // Clear old cached data
    async clearOldCache(maxAge = 7 * 24 * 60 * 60 * 1000) { // 7 days
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['offlineItems'], 'readwrite');
            const store = transaction.objectStore('offlineItems');
            const request = store.getAll();

            request.onsuccess = () => {
                const now = Date.now();
                const deletePromises = [];

                request.result.forEach(item => {
                    if (now - item.timestamp > maxAge) {
                        const deleteRequest = store.delete(item.id);
                        deletePromises.push(new Promise((res, rej) => {
                            deleteRequest.onsuccess = () => res();
                            deleteRequest.onerror = () => rej(deleteRequest.error);
                        }));
                    }
                });

                Promise.all(deletePromises)
                    .then(() => resolve())
                    .catch(reject);
            };
            request.onerror = () => reject(request.error);
        });
    }
}

// Initialize offline manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.offlineManager = new OfflineManager();
});

// Export for use in other scripts
window.OfflineManager = OfflineManager;
