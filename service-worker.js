const CACHE_NAME = 'filmhaat-cache-v5';
const BACKGROUND_IMAGE = 'attached_image/background-image.webp';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll([
                BACKGROUND_IMAGE
            ]);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);
    
    // Never cache POST requests (API calls for tracking)
    if (event.request.method === 'POST') {
        return;
    }
    
    // Never cache API endpoints
    if (url.pathname.includes('api-weekly-top10.php') || 
        url.pathname.includes('.php') && url.search.includes('action=')) {
        return;
    }
    
    // Never cache JavaScript files to ensure updates are immediate
    if (url.pathname.includes('script.js')) {
        return;
    }
    
    // Only cache the background image
    if (event.request.url.includes(BACKGROUND_IMAGE)) {
        event.respondWith(
            caches.match(event.request).then((response) => {
                return response || fetch(event.request).then((fetchResponse) => {
                    return caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, fetchResponse.clone());
                        return fetchResponse;
                    });
                });
            })
        );
    }
});
