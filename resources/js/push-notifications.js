/**
 * Push Notifications Setup for SISARAYA Ruang Kerja
 * Handles service worker registration and push subscription management
 */

// Check browser support
if ('serviceWorker' in navigator && 'PushManager' in window) {
    console.log('✅ Browser supports Push Notifications');
    
    // Register service worker when page loads
    window.addEventListener('load', function() {
        registerServiceWorker();
    });
} else {
    console.warn('⚠️ Browser does not support Push Notifications');
}

/**
 * Register Service Worker
 */
function registerServiceWorker() {
    navigator.serviceWorker.register('/sw.js')
        .then(function(registration) {
            console.log('✅ Service Worker registered successfully');
            console.log('Scope:', registration.scope);
            
            // Initialize push notifications after successful registration
            initPushNotifications(registration);
        })
        .catch(function(error) {
            console.error('❌ Service Worker registration failed:', error);
        });
}

/**
 * Initialize Push Notifications
 */
function initPushNotifications(registration) {
    // Check current notification permission
    const permission = Notification.permission;
    console.log('Current permission status:', permission);
    
    if (permission === 'granted') {
        // Already granted, subscribe to push
        subscribeToPush(registration);
    } else if (permission === 'default') {
        // Show custom prompt after 3 seconds (better UX than immediate prompt)
        setTimeout(function() {
            showNotificationPrompt();
        }, 3000);
    } else if (permission === 'denied') {
        console.warn('⚠️ Push notifications blocked by user');
    }
}

/**
 * Subscribe to Push Notifications
 */
function subscribeToPush(registration) {
    const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
    
    if (!vapidPublicKey) {
        console.error('❌ VAPID public key not found in meta tag');
        return;
    }

    console.log('Subscribing to push notifications...');

    registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
    })
    .then(function(subscription) {
        console.log('✅ Push subscription successful');
        console.log('Subscription:', subscription);
        
        // Save subscription to server
        savePushSubscription(subscription);
    })
    .catch(function(error) {
        if (Notification.permission === 'denied') {
            console.warn('⚠️ Push notifications blocked by user');
        } else {
            console.error('❌ Failed to subscribe to push:', error);
        }
    });
}

/**
 * Save Push Subscription to Server
 */
function savePushSubscription(subscription) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!csrfToken) {
        console.error('❌ CSRF token not found');
        return;
    }

    const subscriptionData = {
        endpoint: subscription.endpoint,
        keys: {
            p256dh: arrayBufferToBase64(subscription.getKey('p256dh')),
            auth: arrayBufferToBase64(subscription.getKey('auth'))
        }
    };

    console.log('Saving subscription to server...');

    fetch('/push-subscriptions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(subscriptionData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Subscription saved successfully:', data);
        
        // Hide prompt if visible
        const prompt = document.getElementById('notification-prompt');
        if (prompt) {
            prompt.remove();
        }
    })
    .catch(error => {
        console.error('❌ Failed to save subscription:', error);
    });
}

/**
 * Show Custom Notification Prompt
 */
function showNotificationPrompt() {
    // Don't show if already prompted recently (within 7 days)
    const lastDismissed = localStorage.getItem('notification_prompt_dismissed');
    if (lastDismissed) {
        const daysSince = (Date.now() - parseInt(lastDismissed)) / (1000 * 60 * 60 * 24);
        if (daysSince < 7) {
            console.log('Notification prompt dismissed recently, skipping');
            return;
        }
    }

    // Don't show if already exists
    if (document.getElementById('notification-prompt')) {
        return;
    }

    const promptHTML = `
        <div id="notification-prompt" class="fixed bottom-4 right-4 bg-white rounded-lg shadow-xl p-4 max-w-sm z-50 border border-gray-200 animate-slide-up">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900">Aktifkan Notifikasi</h3>
                    <p class="text-sm text-gray-600 mt-1">Dapatkan update real-time untuk tiket, event, dan approval.</p>
                    <div class="flex gap-2 mt-3">
                        <button onclick="requestNotificationPermission()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            Aktifkan
                        </button>
                        <button onclick="dismissNotificationPrompt()" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Nanti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', promptHTML);
}

/**
 * Request Notification Permission
 */
window.requestNotificationPermission = function() {
    console.log('Requesting notification permission...');
    
    Notification.requestPermission().then(function(permission) {
        console.log('Permission result:', permission);
        
        if (permission === 'granted') {
            navigator.serviceWorker.ready.then(subscribeToPush);
        }
        
        dismissNotificationPrompt();
    });
};

/**
 * Dismiss Notification Prompt
 */
window.dismissNotificationPrompt = function() {
    const prompt = document.getElementById('notification-prompt');
    if (prompt) {
        prompt.remove();
        localStorage.setItem('notification_prompt_dismissed', Date.now().toString());
    }
};

/**
 * Utility: Convert URL-safe Base64 to Uint8Array
 */
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');
    
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    
    return outputArray;
}

/**
 * Utility: Convert ArrayBuffer to Base64
 */
function arrayBufferToBase64(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    
    for (let i = 0; i < bytes.byteLength; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    
    return window.btoa(binary);
}

// Add CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-up {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
    }
`;
document.head.appendChild(style);

// Export for testing (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        urlBase64ToUint8Array,
        arrayBufferToBase64
    };
}
