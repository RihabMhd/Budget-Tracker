import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Shared — loaded on every page via layouts.app
                'resources/css/app.css',
                'resources/js/app.js',

                // Page-specific CSS
                'resources/css/budgets.css',
                'resources/css/transactions.css',
                'resources/css/profile.css',
                'resources/css/dashboard.css',
                'resources/css/categories.css',

                // Page-specific JS
                'resources/js/budgets.js',
                'resources/js/transactions.js',
                'resources/js/dashboard.js',
                'resources/js/categories.js',
            ],
            refresh: true,
        }),
    ],
});