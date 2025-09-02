import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
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
        host: '127.0.0.1',
        port: 5173,
        hmr: {
            host: '127.0.0.1',
            port: 5173,
        },
        cors: {
            origin: [
                'http://localhost:8000',
                'http://127.0.0.1:8000',
                'http://localhost:9000',
                'http://127.0.0.1:9000',
                'http://192.168.254.190:8000',
                'http://localhost',
                'http://127.0.0.1',
                'http://192.168.254.190',
                
                        ],
            credentials: true
        }
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    base: '/',
});
