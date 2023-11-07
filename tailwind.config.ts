import { Config } from 'tailwindcss';
import tailwindcssAnimate from 'tailwindcss-animate';

const config: Config = {
    darkMode: ['class'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],
    theme: {
        container: {
            center: true,
            padding: '2rem',
            screens: {
                '2xl': '1400px',
            },
        },
        extend: {
            colors: {
                'metalic-blue': '#185ADC',
                'arylide-yellow': '#EDCA5E',
                'jasper-orange': '#DC8A4A',
                'yinmn-blue': '#314c8b',
                'picton-blue': '#43B3E5',
                'ghost-white': '#F9F9F9',
                'light-carbon': '#F0F0F0',
                'cloud-gray': '#D2D2D2',
                'dark-gray': '#999D9E',
                'heavy-carbon': '#545557',
                gunmetal: '#2E3436',
            },
            fontFamily: {
                outfit: ['outfit'],
            },
            fontSize: {
                'headline-1': [
                    '49px',
                    {
                        fontWeight: 500,
                        letterSpacing: '-2.8%',
                        lineHeight: '58px',
                    },
                ],
                'headline-2': [
                    '42px',
                    {
                        fontWeight: 500,
                        letterSpacing: '-4%',
                        lineHeight: '52px',
                    },
                ],
                'headline-3': [
                    '31px',
                    {
                        fontWeight: 500,
                        letterSpacing: '-3%',
                        lineHeight: '40px',
                    },
                ],
                'headline-4': [
                    '18px',
                    {
                        fontWeight: 500,
                        letterSpacing: '-3%',
                        lineHeight: '26px',
                    },
                ],
                subtitle: [
                    '19px',
                    {
                        fontWeight: 500,
                        letterSpacing: '-4%',
                        lineHeight: '28px',
                    },
                ],
                overline: [
                    '3px',
                    {
                        fontWeight: 500,
                        letterSpacing: '3%',
                        lineHeight: '19px',
                    },
                ],
                'body-1': [
                    '11px',
                    {
                        fontWeight: 300,
                        letterSpacing: '-3%',
                        lineHeight: '19px',
                    },
                ],
                'body-2': [
                    '9px',
                    {
                        fontWeight: 300,
                        letterSpacing: '0%',
                        lineHeight: '16px',
                    },
                ],
            },
            keyframes: {
                'accordion-down': {
                    from: { height: '0' },
                    to: { height: 'var(--radix-accordion-content-height)' },
                },
                'accordion-up': {
                    from: { height: 'var(--radix-accordion-content-height)' },
                    to: { height: '0' },
                },
            },
            animation: {
                'accordion-down': 'accordion-down 0.2s ease-out',
                'accordion-up': 'accordion-up 0.2s ease-out',
            },
        },
    },
    plugins: [tailwindcssAnimate],
};

export default config;
