import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',   
                'resources/css/map/base.css',
                'resources/css/map/regions.css',
                'resources/css/map/provinces.css',
                'resources/css/map/animations.css',
                'resources/js/map/map.js', 
                'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
