import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
             'resources/sass/app.scss',
             'resources/js/app.js',
             'resources/css/filament/admin/theme.css',
             'resources/css/filament/customer/theme.css',
             'resources/css/app.css',
            ],
            refresh: true,
        }),
    ],
})
