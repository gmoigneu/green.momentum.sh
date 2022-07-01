import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
        vue({
            template: {
                compilerOptions: {
                    // ...
                },
                transformAssetUrls: {
                    // default tags
                    tags: {
                        video: ['src', 'poster'],
                        source: ['src'],
                        img: ['src'],
                        image: ['xlink:href', 'href'],
                        use: ['xlink:href', 'href']
                    }
                }
            }
        })
    ],
});
