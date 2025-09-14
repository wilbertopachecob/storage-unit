/**
 * Touch Gestures Handler
 * Provides swipe actions and touch interactions for mobile devices
 */

class TouchGestures {
    constructor() {
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.touchEndX = 0;
        this.touchEndY = 0;
        this.minSwipeDistance = 50;
        this.maxVerticalDistance = 100;
        this.isTouchDevice = 'ontouchstart' in window;
        
        this.init();
    }

    init() {
        if (!this.isTouchDevice) return;

        // Add touch event listeners to cards
        this.addSwipeListeners();
        
        // Add pull-to-refresh functionality
        this.addPullToRefresh();
        
        // Add touch feedback
        this.addTouchFeedback();
    }

    addSwipeListeners() {
        const cards = document.querySelectorAll('.card[data-item-id]');
        
        cards.forEach(card => {
            card.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: true });
            card.addEventListener('touchmove', this.handleTouchMove.bind(this), { passive: true });
            card.addEventListener('touchend', this.handleTouchEnd.bind(this), { passive: true });
            
            // Add visual feedback
            card.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
        });
    }

    handleTouchStart(e) {
        this.touchStartX = e.touches[0].clientX;
        this.touchStartY = e.touches[0].clientY;
        
        // Add touch class for styling
        e.currentTarget.classList.add('touch-active');
    }

    handleTouchMove(e) {
        if (!this.touchStartX || !this.touchStartY) return;

        this.touchEndX = e.touches[0].clientX;
        this.touchEndY = e.touches[0].clientY;

        const deltaX = this.touchEndX - this.touchStartX;
        const deltaY = this.touchEndY - this.touchStartY;

        // Only handle horizontal swipes
        if (Math.abs(deltaY) > this.maxVerticalDistance) return;

        const card = e.currentTarget;
        const swipeDistance = Math.min(Math.max(deltaX, -100), 100);
        
        // Apply visual feedback
        card.style.transform = `translateX(${swipeDistance}px)`;
        
        // Add shadow effect
        if (Math.abs(swipeDistance) > 20) {
            card.style.boxShadow = `0 4px 12px rgba(0, 0, 0, 0.15)`;
        }
    }

    handleTouchEnd(e) {
        if (!this.touchStartX || !this.touchStartY) return;

        const deltaX = this.touchEndX - this.touchStartX;
        const deltaY = this.touchEndY - this.touchStartY;

        // Reset visual state
        const card = e.currentTarget;
        card.classList.remove('touch-active');
        card.style.transform = '';
        card.style.boxShadow = '';

        // Check if it's a valid swipe
        if (Math.abs(deltaY) > this.maxVerticalDistance) return;
        if (Math.abs(deltaX) < this.minSwipeDistance) return;

        // Determine swipe direction and action
        if (deltaX > 0) {
            this.handleSwipeRight(card);
        } else {
            this.handleSwipeLeft(card);
        }

        // Reset touch coordinates
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.touchEndX = 0;
        this.touchEndY = 0;
    }

    handleSwipeRight(card) {
        // Swipe right - show edit options
        this.showActionMenu(card, 'edit');
    }

    handleSwipeLeft(card) {
        // Swipe left - show delete options
        this.showActionMenu(card, 'delete');
    }

    showActionMenu(card, action) {
        const itemId = card.dataset.itemId;
        if (!itemId) return;

        // Create action menu
        const menu = document.createElement('div');
        menu.className = 'touch-action-menu';
        menu.innerHTML = this.getActionMenuHTML(action, itemId);
        
        // Position menu
        const rect = card.getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.top = `${rect.top}px`;
        menu.style.left = `${rect.left}px`;
        menu.style.width = `${rect.width}px`;
        menu.style.height = `${rect.height}px`;
        menu.style.zIndex = '1000';
        menu.style.display = 'flex';
        menu.style.alignItems = 'center';
        menu.style.justifyContent = 'center';
        menu.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
        menu.style.borderRadius = '8px';
        menu.style.color = 'white';
        menu.style.fontSize = '14px';
        menu.style.fontWeight = 'bold';

        // Add to document
        document.body.appendChild(menu);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (menu.parentNode) {
                menu.parentNode.removeChild(menu);
            }
        }, 3000);

        // Add click handlers
        const buttons = menu.querySelectorAll('.touch-action-btn');
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.handleActionClick(e.target.dataset.action, itemId);
                menu.remove();
            });
        });

        // Add close handler
        menu.addEventListener('click', () => {
            menu.remove();
        });
    }

    getActionMenuHTML(action, itemId) {
        if (action === 'edit') {
            return `
                <div class="touch-action-content">
                    <i class="fas fa-edit"></i>
                    <span>Swipe right to edit</span>
                    <button class="touch-action-btn btn btn-primary btn-sm" data-action="edit">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            `;
        } else {
            return `
                <div class="touch-action-content">
                    <i class="fas fa-trash"></i>
                    <span>Swipe left to delete</span>
                    <button class="touch-action-btn btn btn-danger btn-sm" data-action="delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            `;
        }
    }

    handleActionClick(action, itemId) {
        switch (action) {
            case 'edit':
                window.location.href = `/index.php?script=editItem&id=${itemId}`;
                break;
            case 'delete':
                if (confirm('Are you sure you want to delete this item?')) {
                    this.deleteItem(itemId);
                }
                break;
        }
    }

    async deleteItem(itemId) {
        try {
            const response = await fetch('/api/items/delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: itemId })
            });

            if (response.ok) {
                // Remove card from DOM
                const card = document.querySelector(`[data-item-id="${itemId}"]`);
                if (card) {
                    card.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
                    card.style.transform = 'translateX(-100%)';
                    card.style.opacity = '0';
                    
                    setTimeout(() => {
                        card.remove();
                        this.showToast('Item deleted successfully', 'success');
                    }, 300);
                }
            } else {
                throw new Error('Failed to delete item');
            }
        } catch (error) {
            console.error('Error deleting item:', error);
            this.showToast('Failed to delete item', 'error');
        }
    }

    addPullToRefresh() {
        let startY = 0;
        let currentY = 0;
        let isPulling = false;
        let pullDistance = 0;
        const maxPullDistance = 100;

        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                startY = e.touches[0].clientY;
                isPulling = true;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (!isPulling) return;

            currentY = e.touches[0].clientY;
            pullDistance = currentY - startY;

            if (pullDistance > 0 && pullDistance < maxPullDistance) {
                // Add visual feedback
                document.body.style.transform = `translateY(${pullDistance * 0.5}px)`;
                document.body.style.transition = 'none';
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            if (!isPulling) return;

            isPulling = false;
            document.body.style.transition = 'transform 0.3s ease';
            document.body.style.transform = '';

            if (pullDistance > 60) {
                // Trigger refresh
                this.refreshPage();
            }

            pullDistance = 0;
        }, { passive: true });
    }

    refreshPage() {
        // Show loading indicator
        this.showToast('Refreshing...', 'info');
        
        // Reload page after short delay
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }

    addTouchFeedback() {
        // Add ripple effect to buttons
        document.addEventListener('touchstart', (e) => {
            if (e.target.classList.contains('btn')) {
                this.createRipple(e);
            }
        }, { passive: true });

        // Add haptic feedback if available
        document.addEventListener('touchstart', (e) => {
            if (navigator.vibrate) {
                navigator.vibrate(10);
            }
        }, { passive: true });
    }

    createRipple(e) {
        const button = e.target;
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.touches[0].clientX - rect.left - size / 2;
        const y = e.touches[0].clientY - rect.top - size / 2;

        const ripple = document.createElement('span');
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255, 255, 255, 0.6)';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'ripple 0.6s linear';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.width = size + 'px';
        ripple.style.height = size + 'px';
        ripple.style.pointerEvents = 'none';

        button.style.position = 'relative';
        button.style.overflow = 'hidden';
        button.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    showToast(message, type = 'info') {
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
}

// Add CSS for touch interactions
const touchStyles = `
<style>
.touch-active {
    transform: scale(0.98);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.touch-action-menu {
    position: fixed;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.8);
    border-radius: 8px;
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.touch-action-content {
    text-align: center;
}

.touch-action-content i {
    display: block;
    font-size: 24px;
    margin-bottom: 8px;
}

.touch-action-content span {
    display: block;
    margin-bottom: 12px;
}

.touch-action-btn {
    margin: 0 4px;
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 6px;
    color: white;
    font-weight: bold;
    z-index: 9999;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.toast-success { background-color: #28a745; }
.toast-error { background-color: #dc3545; }
.toast-info { background-color: #17a2b8; }
.toast-warning { background-color: #ffc107; }
</style>
`;

// Add styles to document
document.head.insertAdjacentHTML('beforeend', touchStyles);

// Initialize touch gestures when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new TouchGestures();
});

// Export for use in other scripts
window.TouchGestures = TouchGestures;
