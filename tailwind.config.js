const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/**.blade.php',
        './node_modules/preline/dist/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    light: '#a5dcf3',
                    DEFAULT: '#7bd1f2',
                    dark: '#43b8d8',
                },
                secondary: {
                    light: '#ff8f64',
                    DEFAULT: '#f48356',
                    dark: '#da4d18',
                },
                background: {
                    light: '#FFFFFF',
                    DEFAULT: '#FFFAF7',
                    dark: '#FFF1EA',
                },
            },
        },
    },

    darkMode: 'class',

    plugins: [
        require('@tailwindcss/forms'),
        require('preline/plugin')
    ],
};
