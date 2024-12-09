import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'nft-primary': '#6366f1',
                'nft-secondary': '#818cf8',
                'nft-accent': '#4f46e5',
                'nft-neutral': '#1f2937',
            },
        },
    },

    daisyui: {
        styled: true,
        themes: [
            {
                florenceegi: {
                    "primary": "#6366f1",
                    "secondary": "#818cf8",
                    "accent": "#4f46e5",
                    "neutral": "#1f2937",
                    "base-100": "#ffffff",
                    "info": "#3abff8",
                    "success": "#36d399",
                    "warning": "#fbbd23",
                    "error": "#f87272",
                },
            },
        ],
        base: true,
        utils: true,
        logs: true,
        rtl: false,
        prefix: "",
    },

    plugins: [
        typography, forms, require("daisyui"),
        require('@tailwindcss/typography'),
    ],

    corePlugins: {
        preflight: true,
    },

    safelist: [
        'input-primary',
        'input-secondary',
        'input-accent',
        'input-info',
        'input-success',
        'input-warning',
        'input-error',
        'select-primary',
        'select-secondary',
        'select-accent',
        'select-info',
        'select-success',
        'select-warning',
        'select-error'
    ],
};
