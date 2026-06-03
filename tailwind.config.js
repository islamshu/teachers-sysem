import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Tajawal', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: 'rgb(var(--color-primary-50))',
                    100: 'rgb(var(--color-primary-100))',
                    200: 'rgb(var(--color-primary-200))',
                    300: 'rgb(var(--color-primary-300))',
                    400: 'rgb(var(--color-primary-400))',
                    500: 'rgb(var(--color-primary-500))',
                    600: 'rgb(var(--color-primary-600))',
                    700: 'rgb(var(--color-primary-700))',
                    800: 'rgb(var(--color-primary-800))',
                    900: 'rgb(var(--color-primary-900))',
                },
                warm: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                surface: {
                    50: '#faf8f6',
                    100: '#f5f2ed',
                    200: '#e9e4db',
                    300: '#d6cec0',
                    400: '#b8ad99',
                    500: '#9e9078',
                    600: '#887a64',
                    700: '#716550',
                    800: '#5e5444',
                    900: '#4e4639',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.6s ease-out forwards',
                'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                'fade-in-down': 'fadeInDown 0.5s ease-out forwards',
                'scale-in': 'scaleIn 0.4s ease-out forwards',
                'slide-in-right': 'slideInRight 0.5s ease-out forwards',
                'slide-in-left': 'slideInLeft 0.5s ease-out forwards',
                'float': 'float 3s ease-in-out infinite',
                'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                'shimmer': 'shimmer 2s infinite linear',
                'bounce-in': 'bounceIn 0.5s ease-out forwards',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(24px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(24px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(-24px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.7' },
                },
                shimmer: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                },
                bounceIn: {
                    '0%': { opacity: '0', transform: 'scale(0.3)' },
                    '50%': { transform: 'scale(1.05)' },
                    '70%': { transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'pattern-dots': 'radial-gradient(circle, #e9e4db 1px, transparent 1px)',
            },
            backgroundSize: {
                'dots': '20px 20px',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
    ],
};
