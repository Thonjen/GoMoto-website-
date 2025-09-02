import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

const host = process.env.VITE_HOST || 'localhost';

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
        host: true,
        port: 5173,
        hmr: {
            protocol: 'ws',
            host: host,
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
