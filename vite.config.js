import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            buildDirectory: 'build',
            refresh: true,
        }),
      
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '@': '/resources/js',
        }
    },
    build: {
        chunkSizeWarningLimit: 1600,
        minify: true,
    },

    server: {
        proxy: {
          '/api': {
            target: 'https://alerto.adzu.edu.ph',
            changeOrigin: true,
            rewrite: (path) => path.replace(/^\/api/, '')
          }
        }
      }

});