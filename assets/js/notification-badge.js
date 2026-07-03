/**
 * Notification Badge System
 * Updates browser tab title and favicon with unread notification count
 */

(function() {
    'use strict';

    function apiBaseUrl() {
        const raw = window.__RECEPSIONIS_API_BASE_URL__ || '../api/';
        return String(raw).replace(/\/?$/, '/');
    }

    // Store original title and favicon
    const originalTitle = window.originalPageTitle || document.title;
    let originalFavicon = null;
    let notificationCount = 0;
    let updateInterval = null;

    // Get original favicon
    function getOriginalFavicon() {
        const link = document.querySelector("link[rel*='icon']");
        if (link) {
            originalFavicon = link.href;
        } else {
            // Default favicon
            originalFavicon = '/favicon.ico';
        }
        return originalFavicon;
    }

    // Create notification badge favicon using canvas
    function createBadgeFavicon(count) {
        return new Promise((resolve) => {
            const canvas = document.createElement('canvas');
            canvas.width = 32;
            canvas.height = 32;
            const ctx = canvas.getContext('2d');

            // Create a simple bell icon with badge
            // Background circle (red)
            ctx.fillStyle = '#dc3545';
            ctx.beginPath();
            ctx.arc(26, 8, 8, 0, 2 * Math.PI);
            ctx.fill();

            // White text for count
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 10px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const text = count > 99 ? '99+' : count.toString();
            ctx.fillText(text, 26, 8);

            // Convert to data URL
            canvas.toBlob((blob) => {
                const url = URL.createObjectURL(blob);
                resolve(url);
            }, 'image/png');
        });
    }

    // Update favicon
    function updateFavicon(count) {
        if (count === 0) {
            // Restore original favicon
            const link = document.querySelector("link[rel*='icon']");
            if (link && originalFavicon) {
                link.href = originalFavicon;
            }
            return;
        }

        createBadgeFavicon(count).then((badgeUrl) => {
            let link = document.querySelector("link[rel*='icon']");
            if (!link) {
                link = document.createElement('link');
                link.rel = 'icon';
                document.head.appendChild(link);
            }
            link.href = badgeUrl;
        });
    }

    // Update tab title
    function updateTitle(count) {
        if (count === 0) {
            document.title = originalTitle;
        } else {
            document.title = `(${count}) ${originalTitle}`;
        }
    }

    // Fetch notification count from API
    function fetchNotificationCount() {
        fetch(apiBaseUrl() + 'get_notification_count.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const newCount = parseInt(data.count) || 0;
                    
                    // Only update if count changed
                    if (newCount !== notificationCount) {
                        notificationCount = newCount;
                        updateTitle(notificationCount);
                        updateFavicon(notificationCount);
                        
                        // Also update sidebar badge if exists
                        updateSidebarBadge(notificationCount);
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching notification count:', error);
            });
    }

    // Update sidebar badge
    function updateSidebarBadge(count) {
        const sidebarLink = document.querySelector('a[href*="notifications.php"]');
        if (sidebarLink) {
            // Remove existing badge
            const existingBadge = sidebarLink.querySelector('.notification-badge');
            if (existingBadge) {
                existingBadge.remove();
            }

            // Add new badge if count > 0
            if (count > 0) {
                const badge = document.createElement('span');
                badge.className = 'notification-badge badge bg-danger rounded-pill';
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.cssText = 'position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 0.7rem; padding: 2px 6px;';
                sidebarLink.style.position = 'relative';
                sidebarLink.appendChild(badge);
            }
        }
    }

    // Initialize
    function init() {
        // Get original favicon
        getOriginalFavicon();

        // Fetch immediately
        fetchNotificationCount();

        // Set up polling (every 30 seconds)
        updateInterval = setInterval(fetchNotificationCount, 30000);

        // Also fetch when page becomes visible (user switches back to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                fetchNotificationCount();
            }
        });

        // Fetch when window gains focus
        window.addEventListener('focus', fetchNotificationCount);
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });

})();
