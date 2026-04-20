import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Shared — loaded on every page via layouts.app
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/auth.css',

                // Page-specific CSS
                'resources/css/budgets.css',
                'resources/css/transactions.css',
                'resources/css/profile.css',
                'resources/css/dashboard.css',
                'resources/css/categories.css',
                'resources/css/sidebar.css',

                // Page-specific JS
                'resources/js/budgets.js',
                'resources/js/transactions.js',
                'resources/js/dashboard.js',
                'resources/js/categories.js',
                'resources/js/analytics.js',
            ],
            refresh: true,
        }),
    ],
});