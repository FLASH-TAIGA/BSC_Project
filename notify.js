// Flash Learning — Browser Notification Manager

const FL_NOTIFY = {

    // Register service worker and request permission
    async init() {
        if (!('serviceWorker' in navigator) || !('Notification' in window)) return false;
        try {
            await navigator.serviceWorker.register('/sw.js');
            return true;
        } catch(e) { return false; }
    },

    // Request permission from user
    async requestPermission() {
        if (!('Notification' in window)) return false;
        if (Notification.permission === 'granted') return true;
        if (Notification.permission === 'denied')  return false;
        const result = await Notification.requestPermission();
        return result === 'granted';
    },

    // Show a local browser notification immediately
    async show(title, body, url) {
        const granted = await this.requestPermission();
        if (!granted) return;
        try {
            const reg = await navigator.serviceWorker.ready;
            reg.showNotification(title, {
                body:    body,
                icon:    'image/fl5.jpg',
                badge:   'image/fl5.jpg',
                tag:     'fl-' + Date.now(),
                data:    url ? { url } : {},
                vibrate: [200, 100, 200]
            });
        } catch(e) {
            // Fallback to basic Notification API
            if (Notification.permission === 'granted') {
                new Notification(title, { body, icon: 'image/fl5.jpg' });
            }
        }
    },

    // Notify about a new chat message
    newMessage(senderName, preview) {
        this.show(
            '💬 New message from ' + senderName,
            preview.substring(0, 100),
            'dashboard-student.html'
        );
    },

    // Notify about a session booking (tutor)
    sessionBooked(studentName, subject, date) {
        this.show(
            '📅 New Session Booked',
            studentName + ' booked a ' + subject + ' session on ' + date,
            'dashboard-tutor.html'
        );
    },

    // Notify about session status change (student)
    sessionUpdated(status, subject) {
        const icons = {confirmed:'✅', cancelled:'❌', completed:'🎓'};
        this.show(
            (icons[status]||'📋') + ' Session ' + status.charAt(0).toUpperCase() + status.slice(1),
            'Your ' + subject + ' session has been ' + status,
            'dashboard-student.html'
        );
    },

    // Notify about a broadcast (all users)
    broadcast(title, message) {
        this.show('📢 ' + title, message, window.location.href);
    },

    // Notify admin about new contact message
    newContactMessage(name, subject) {
        this.show(
            '📩 New Message from ' + name,
            'Subject: ' + subject,
            'dashboard-admin.html'
        );
    }
};

// Auto-init when script loads
FL_NOTIFY.init();
