// Flash Learning — Service Worker for Push Notifications
self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : {};
    const title   = data.title   || 'Flash Learning';
    const options = {
        body:    data.body    || 'You have a new notification.',
        icon:    data.icon    || '/image/fl5.jpg',
        badge:   data.badge   || '/image/fl5.jpg',
        tag:     data.tag     || 'fl-notification',
        data:    data.url     ? { url: data.url } : {},
        vibrate: [200, 100, 200],
        requireInteraction: false
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data && event.notification.data.url
        ? event.notification.data.url
        : '/';
    event.waitUntil(clients.openWindow(url));
});

self.addEventListener('install',  () => self.skipWaiting());
self.addEventListener('activate', () => clients.claim());
