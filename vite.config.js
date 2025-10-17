import { defineConfig } from "vite";
import leaf from "@leafphp/vite-plugin";

export default defineConfig({
    plugins: [
        leaf({
            input: [
                "assets/scripts/app.js",
                "assets/styles/app.css"
            ],
            refresh: true,
        }),
    ],
});