import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                numbers: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary Colors
                navy: {
                    DEFAULT: '#1A2F4B',
                },
                teal: {
                    DEFAULT: '#00BFA6',
                    dark: '#00A893',
                    light: '#E0F7F4',
                },
                // Secondary Colors
                slate: {
                    DEFAULT: '#64748B',
                    50: '#F8FAFC',
                    100: '#F1F5F9',
                    200: '#E2E8F0',
                    300: '#CBD5E1',
                    400: '#94A3B8',
                    500: '#64748B',
                    600: '#475569',
                    700: '#334155',
                    800: '#1E293B',
                    900: '#0F172A',
                },
                // Accent Colors
                success: {
                    DEFAULT: '#10B981',
                },
                warning: {
                    DEFAULT: '#F59E0B',
                },
                error: {
                    DEFAULT: '#EF4444',
                },
                purple: {
                    DEFAULT: '#8B5CF6',
                },
            }
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
