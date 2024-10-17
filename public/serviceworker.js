var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/css/app.css',
    '/js/app.js',
    "/storage/01JA4WP9976X1MJ6AECS1GYV58.png",
    "/storage/01JA764RRA9KTY31PYDEJHXGRH.png",
    "/storage/01JA764RSDTGYK1S11PA7BG91V.png",
    "/storage/01JA764RSJ9GEW8331FW0XDR5C.png",
    "/storage/01JA764RSQYHPVMQ7FB22G756W.png",
    "/storage/01JA764RSW4YS3V55F8RG2D5E2.png",
    "/storage/01JA764RT2MTP6XTXP8M2BD2DH.png",
    "/storage/01JA764RTAX9CHQ6N2WQ1N5WYD.png"
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
