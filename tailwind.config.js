import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Token warna PMO Workspace — turunan dari brand #33B0E6 + putih
                pmo: {
                    50: '#EFF9FD',
                    100: '#D9F0FB',
                    200: '#B3E1F7',
                    300: '#7DCBF0',
                    400: '#4CBBEA',
                    500: '#33B0E6', // brand utama
                    600: '#2492C4',
                    700: '#1D739B',
                    800: '#1A5D7D',
                    900: '#164C67',
                },
                ink: {
                    50: '#F4F6F8',
                    100: '#E6EAEE',
                    300: '#AEB9C4',
                    500: '#5B6B7A',
                    700: '#33414F',
                    900: '#101B27', // teks utama / sidebar gelap
                },
                surface: '#FFFFFF',
                canvas: '#F5F9FB', // background halaman, dari pmo-50 diredupkan
                status: {
                    success: '#1E9E6B',
                    successBg: '#E7F7F0',
                    warning: '#C98A1B',
                    warningBg: '#FBF1DD',
                    danger: '#D14343',
                    dangerBg: '#FBEAEA',
                    neutral: '#5B6B7A',
                    neutralBg: '#EEF1F4',
                },
            },
            boxShadow: {
                card: '0 1px 2px rgba(16, 27, 39, 0.06), 0 1px 12px rgba(16, 27, 39, 0.04)',
            },
            borderRadius: {
                xl2: '1.25rem',
            },
        },
    },

    plugins: [forms],
};
