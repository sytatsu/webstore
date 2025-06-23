import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    server: {
        hmr: {
            host: 'localhost',
        },
        port: 5174,
    },
    plugins: [
        laravel({
            input: [
                'resources/scss/sytatsu.scss',
                'resources/js/sytatsu.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@images': path.resolve('/resources/images'),
        }
    }
});
