import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                display: ['Fredoka', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                asoka: { 50: '#f7fcfe', 100: '#ecf3f9', 200: '#cde7fe', 300: '#add9ff', 400: '#62c5f3', 500: '#24b3fe', 600: '#1c71fe', 700: '#0f73cd', 800: '#407ca6', 900: '#6c4675' },
            },
        },
    },
    plugins: [],
};
