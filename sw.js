const CACHE_NAME = 'filmhaat-v3';
const STATIC_CACHE = 'filmhaat-static-v3';
const DYNAMIC_CACHE = 'filmhaat-dynamic-v2';

const STATIC_ASSETS = [
  '/styles.css',
  '/script.min.js',
  '/manifest.php',
  '/offline.html',
  '/icons/icon-192x192.webp',
  '/icons/icon-512x512.webp'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => {
        return cache.addAll(STATIC_ASSETS).catch(err => {
        });
      })
      .then(() => {
        return self.skipWaiting();
      })
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((cacheName) => {
            return cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE;
          })
          .map((cacheName) => {
            return caches.delete(cacheName);
          })
      );
    }).then(() => {
      return self.clients.claim();
    })
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  if (request.method !== 'GET') {
    return;
  }

  if (url.origin === location.origin) {
    const isDocument = request.mode === 'navigate' || 
                       request.headers.get('accept')?.includes('text/html') ||
                       url.pathname.endsWith('.php');
    
    const isStaticAsset = STATIC_ASSETS.some(asset => url.pathname === asset) ||
                          url.pathname.startsWith('/icons/') ||
                          url.pathname.endsWith('.css') ||
                          url.pathname.endsWith('.js') ||
                          url.pathname.endsWith('.webp') ||
                          url.pathname.endsWith('.jpg') ||
                          url.pathname.endsWith('.png');

    if (isDocument) {
      event.respondWith(
        fetch(request)
          .then((response) => {
            if (response && response.status === 200) {
              const responseToCache = response.clone();
              caches.open(DYNAMIC_CACHE).then((cache) => {
                cache.put(request, responseToCache);
              });
            }
            return response;
          })
          .catch(() => {
            return caches.match(request).then((cachedResponse) => {
              if (cachedResponse) {
                return cachedResponse;
              }
              return caches.match('/offline.html');
            });
          })
      );
    } else if (isStaticAsset) {
      event.respondWith(
        caches.match(request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }

          return fetch(request).then((response) => {
            if (response && response.status === 200) {
              const responseToCache = response.clone();
              caches.open(STATIC_CACHE).then((cache) => {
                cache.put(request, responseToCache);
              });
            }
            return response;
          });
        })
      );
    }
  }
});

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

