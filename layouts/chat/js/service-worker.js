// service-worker.js

self.addEventListener('push', function(event) {
    const data = event.data.json();
    const title = data.title || 'Pesan Baru dari Admin';
    const options = {
        body: data.body || 'Anda memiliki pesan baru.',
        icon: data.icon || './assets/images/profile-picture/the-winner.jpeg', // Path ke ikon notifikasi Anda
        badge: data.badge || './assets/images/profile-picture/the-winner.jpeg', // Opsional, untuk tampilan di Android
        data: {
            url: data.url || self.registration.scope // URL yang akan dibuka saat notifikasi diklik
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close(); // Tutup notifikasi setelah diklik
    const urlToOpen = event.notification.data.url;

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function(clientList) {
            // Coba fokus ke tab yang sudah ada jika URL cocok
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(urlToOpen) && 'focus' in client) {
                    return client.focus();
                }
            }
            // Jika tidak ada tab yang cocok, buka tab baru
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Cache assets (opsional, untuk Progressive Web App)
self.addEventListener('install', function(event) {
    console.log('Service Worker: Installed');
    event.waitUntil(
        caches.open('chat-app-cache-v1').then(function(cache) {
            return cache.addAll([
                './',
                './layouts/chat/css/style.css',
                './layouts/chat/js/chat-ui.js',
                './layouts/chat/js/koneksi-api-chat.js',
                './layouts/chat/js/chatbot.js',
                './assets/images/profile-picture/the-winner.jpeg' // Pastikan ini path yang benar
                // Tambahkan aset lain yang ingin di-cache
            ]);
        })
    );
});

self.addEventListener('activate', function(event) {
    console.log('Service Worker: Activated');
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(cacheName) {
                    return cacheName.startsWith('chat-app-cache-') && cacheName !== 'chat-app-cache-v1';
                }).map(function(cacheName) {
                    return caches.delete(cacheName);
                })
            );
        })
    );
});