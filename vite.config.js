import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import os from 'os';

function getLocalIp() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return 'localhost';
}

const hostIp = process.env.VITE_HOST || getLocalIp();

export default defineConfig({
    // use relative paths to work with dynamic HTTPS tunnels
    base: './',
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
server: {
        host: true, // listen on all interfaces
        port: 5173,
        hmr: {
            protocol: 'ws',
            host: hostIp, // HMR uses your actual LAN IP
            port: 5173,
        },
    },
    build: {
        rollupOptions: {
            output: { manualChunks: undefined },
        },
        assetsDir: 'assets', // keep build assets in public/build/assets
        manifest: true,       // ensure Laravel can read asset paths
    },
});
