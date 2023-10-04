import { Config } from "tailwindcss";
import tailwindcssAnimate from "tailwindcss-animate";

const config: Config = {
    darkMode: ["class"],
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.tsx",
    ],
    theme: {
        container: {
            center: true,
            padding: "2rem",
            screens: {
                "2xl": "1400px",
            },
        },
        extend: {
            colors: {
                "celtic-blue": "#185ADC",
                "metalic-blue": "#185ADC",
                "arylide-yellow": "#EDCA5E",
                "pure-white": "#ffffff",
                "off-white": "#F9F9F9",
                "light-carbon": "#F0F0F0",
                "cloud-gray": "#D2D2D2",
                "dark-gray": "#999D9E",
                "heavy-carbon": "#545557",
            },
            fontSize: {
                "headline-1": [
                    "49px",
                    {
                        lineHeight: "58px",
                        fontWeight: 500,
                        letterSpacing: "-2.8%",
                    },
                ],
                "headline-2": [
                    "42px",
                    {
                        lineHeight: "52px",
                        fontWeight: 500,
                        letterSpacing: "-4%",
                    },
                ],
                "headline-3": [
                    "31px",
                    {
                        lineHeight: "40px",
                        fontWeight: 500,
                        letterSpacing: "-3%",
                    },
                ],
                "headline-4": [
                    "18px",
                    {
                        lineHeight: "26px",
                        fontWeight: 500,
                        letterSpacing: "-3%",
                    },
                ],
                subtitle: [
                    "19px",
                    {
                        lineHeight: "28px",
                        fontWeight: 500,
                        letterSpacing: "-4%",
                    },
                ],
                overline: [
                    "3px",
                    {
                        lineHeight: "19px",
                        fontWeight: 500,
                        letterSpacing: "3%",
                    },
                ],
                "body-1": [
                    "11px",
                    {
                        lineHeight: "19px",
                        fontWeight: 400,
                        letterSpacing: "-3%",
                    },
                ],
                "body-2": [
                    "9px",
                    {
                        lineHeight: "16px",
                        fontWeight: 400,
                        letterSpacing: "0%",
                    },
                ],
            },
            keyframes: {
                "accordion-down": {
                    from: { height: "0" },
                    to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--radix-accordion-content-height)" },
                    to: { height: "0" },
                },
            },
            animation: {
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
            },
        },
    },
    plugins: [tailwindcssAnimate],
};

export default config;
