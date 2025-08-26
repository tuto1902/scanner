import './bootstrap';
import { Html5Qrcode } from 'html5-qrcode';

// Alpine.js app store for global state management
document.addEventListener('alpine:init', () => {
    Alpine.store('app', {
        token: localStorage.getItem('auth_token') || null,
        isAuthenticated: false,
        scanner: null,
        isScanning: false,

        init() {
            this.isAuthenticated = !!this.token;

            // Listen for auth token events
            window.addEventListener('auth-token-received', (event) => {
                this.setToken(event.detail.token);
            });

            window.addEventListener('logout', () => {
                this.logout();
            });
        },

        setToken(token) {
            this.token = token;
            this.isAuthenticated = true;
            localStorage.setItem('auth_token', token);
        },

        logout() {
            this.token = null;
            this.isAuthenticated = false;
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        },

        getToken() {
            return this.token;
        },

        async startScanner(elementId, onSuccess, onError) {
            if (this.isScanning) {
                return;
            }

            try {
                this.scanner = new Html5Qrcode(elementId);
                this.isScanning = true;

                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                this.scanner.start({ facingMode: 'environment' }, config, onSuccess).catch(err => console.error('Unable to start scanning.', err));
            } catch (err) {
                this.isScanning = false;
                console.error('Failed to start scanner:', err);
                onError(err);
            }
        },

        async stopScanner() {
            if (this.scanner && this.isScanning) {
                try {
                    await this.scanner.stop();
                    this.scanner.clear();
                    this.isScanning = false;
                } catch (err) {
                    console.error('Failed to stop scanner:', err);
                }
            }
        }
    });
});

// Global function to initialize the app
window.appStore = () => ({
    init() {
        Alpine.store('app').init();
    }
});

window.initApp = () => {
    Alpine.store('app').init();
};
