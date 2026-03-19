// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/js/app.js',
                'resources/views/skin/board/basic/style.css', // basic 스킨용 CSS
                'resources/views/skin/layout/swn-style/style.css',
            ],
            // public/build 대신 루트의 build 폴더를 사용하도록 명시
            buildDirectory: 'build', 
            refresh: true,
        }),
    ],
    build: {
        // 실제 파일이 생성되는 물리적 위치 지정
        outDir: 'build',
    }
});
