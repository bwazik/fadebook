importScripts(
    "https://www.gstatic.com/firebasejs/10.8.1/firebase-app-compat.js",
);
importScripts(
    "https://www.gstatic.com/firebasejs/10.8.1/firebase-messaging-compat.js",
);

const firebaseConfig = {
    apiKey: "AIzaSyCFhC22GzeDFhg8Y7P8FAZi9kQ6zY54GzM",
    authDomain: "gymz-489420.firebaseapp.com",
    projectId: "gymz-489420",
    storageBucket: "gymz-489420.firebasestorage.app",
    messagingSenderId: "1093509789268",
    appId: "1:1093509789268:web:a48af1b3bd29eac45adfc1",
    measurementId: "G-4XKXQ1VZK1",
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log("[sw.js] Received background message ", payload);

    const notificationTitle = payload.notification?.title || "BanhaFade";
    const notificationOptions = {
        body: payload.notification?.body,
        icon: "/icons/icon-192x192.png",
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

const CACHE_NAME = "banhafade-cache-v1";
const OFFLINE_URL = "/offline";

// Assets to pre-cache on install
const PRECACHE_ASSETS = ["/", "/offline", "/manifest.json"];

// Install: pre-cache essential assets
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        }),
    );
    self.skipWaiting();
});

// Activate: clean up old caches
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name)),
            );
        }),
    );
    self.clients.claim();
});

// Fetch: network-first strategy, fallback to cache, then offline page
self.addEventListener("fetch", (event) => {
    // Skip non-GET requests (important for Livewire POST requests)
    if (event.request.method !== "GET") {
        return;
    }

    // Skip Livewire internal requests and OAuth routes
    const url = new URL(event.request.url);
    if (
        url.pathname.startsWith("/livewire/") ||
        url.pathname === "/login" ||
        url.pathname === "/logout"
    ) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Cache successful responses for static assets
                if (response.ok && isStaticAsset(event.request.url)) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Try to serve from cache
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // For navigation requests, serve the offline page
                    if (event.request.mode === "navigate") {
                        return caches.match(OFFLINE_URL);
                    }

                    return new Response("", {
                        status: 503,
                        statusText: "Offline",
                    });
                });
            }),
    );
});

// Helper: determine if a URL is a static asset worth caching
function isStaticAsset(url) {
    return url.match(
        /\.(css|js|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico|webp)(\?.*)?$/i,
    );
}
