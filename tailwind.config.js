import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // Baris di atas (**/*.blade.php) sebenarnya memeriksa seluruh sub-folder di dalam views.
        // Namun, jika masih belum terbaca, tambahkan baris spesifik di bawah ini untuk mengunci folder publik:
        './resources/views/publik/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily['sans']],
            },
        },
    },

    plugins: [forms],
};