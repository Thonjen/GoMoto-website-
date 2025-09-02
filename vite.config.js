import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';

export default defineConfig({
    base: '', // relative paths
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: { transformAssetUrls: { base: null, includeAbsolute: false } },
        }),
    ],
server: {
    host: '0.0.0.0',  // local machine, all interfaces
    port: 5173,
    hmr: {
        protocol: 'ws',
        host: 'localhost', // local host for HMR
        port: 5173,
    },
},



    build: {
        rollupOptions: { output: { manualChunks: undefined } },
    },
});
