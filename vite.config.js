import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/boots_styles.css', 'resources/js/dinamic_login.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
