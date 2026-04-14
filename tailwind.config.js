const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/**.blade.php',
        './node_modules/preline/preline.js',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#E14C04',
                secondary: '#1C315E',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                'fade-in-right': {
                    '0%': {
                        opacity: '0',
                        transform: 'translateX(20px)'
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateX(0)'
                    },
                }
            },
            animation: {
                'fade-in-right': 'fade-in-right 0.8s ease-out forwards',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('preline/plugin')
    ],
};
