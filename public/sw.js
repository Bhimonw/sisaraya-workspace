/**
 * Service Worker untuk SISARAYA Push Notifications
 * Handles push events dari server dan menampilkan notifications
 */

self.addEventListener('push', function (e) {
    if (!e.data) {
        console.log('Push event tidak ada data');
        return;
    }

    try {
        const data = e.data.json();
        console.log('Push event received:', data);
        
        const options = {
            body: data.body || 'Notifikasi baru dari SISARAYA',
            icon: data.icon || '/images/notification-icon.png',
            badge: data.badge || '/images/badge-icon.png',
            data: data.data || {},
            actions: data.actions || [
                {
                    action: 'view',
                    title: 'Lihat Detail'
                },
                {
                    action: 'close',
                    title: 'Tutup'
                }
            ],
            tag: data.tag || 'sisaraya-notification',
            requireInteraction: false,
            vibrate: [200, 100, 200],
            renotify: true,
        };

        e.waitUntil(
            self.registration.showNotification(data.title || 'SISARAYA', options)
        );
    } catch (error) {
        console.error('Error handling push event:', error);
    }
});

self.addEventListener('notificationclick', function (e) {
    console.log('Notification clicked:', e);
    
    e.notification.close();
    
    // Handle action buttons
    if (e.action === 'close') {
        return;
    }
    
    // Get URL from notification data
    const urlToOpen = e.notification.data?.url || '/dashboard';
    
    e.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function (clientList) {
                // Check if already have a window open with this URL
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url.includes(urlToOpen) && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Open new window if no existing window found
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

self.addEventListener('notificationclose', function (e) {
    console.log('Notification closed:', e);
});

// Install event
self.addEventListener('install', function(event) {
    console.log('Service Worker installing.');
    // Force waiting service worker to become active
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', function(event) {
    console.log('Service Worker activating.');
    // Claim all clients immediately
    event.waitUntil(self.clients.claim());
});
