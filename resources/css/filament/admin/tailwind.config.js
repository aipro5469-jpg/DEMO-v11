import preset from "../../../../vendor/filament/filament/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "../../../../app/Filament/Admin/**/*.php",
        "../../../../resources/views/filament/admin/**/*.blade.php",
        "../../../../vendor/filament/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                jabali: {
                    blue: "#0F4C81",
                    gold: "#D4AF37",
                    lightBlue: "#E6F0F9",
                    darkBlue: "#0A355C",
                },
            },
            animation: {
                "fade-in": "fadeIn 0.5s ease-out",
                "slide-up": "slideUp 0.5s ease-out",
            },
            keyframes: {
                fadeIn: {
                    "0%": { opacity: "0" },
                    "100%": { opacity: "1" },
                },
                slideUp: {
                    "0%": { transform: "translateY(20px)", opacity: "0" },
                    "100%": { transform: "translateY(0)", opacity: "1" },
                },
            },
        },
    },
};
