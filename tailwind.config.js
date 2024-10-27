const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    light: '#a5dcf3',
                    DEFAULT: '#7ad1f1',
                    dark: '#43b8d8',
                },
                secondary: {
                    light: '#ff8f64',
                    DEFAULT: '#fa6933',
                    dark: '#da4d18',
                },
                background: {
                    light: '#fffaf7',
                    DEFAULT: '#FFF1EA',
                    dark: '#d3baac',
                },
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
