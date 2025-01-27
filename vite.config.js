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
                'resources/scss/app.scss',
                'resources/js/app.js',

                'resources/scss/sytatsu.scss',
                'resources/js/sytatsu.js',

                'resources/scss/welcome.scss',
                'resources/js/welcome.js',

                'resources/scss/stpronk.scss',
                'resources/js/stpronk.js'
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
