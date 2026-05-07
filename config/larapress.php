<?php

return [

    /*
     * true  → 루트(/) 접속 시 랜딩 페이지(welcome.blade.php) 표시
     * false → 루트(/) 접속 시 CMS 홈으로 바로 리다이렉트
     */
    'show_landing_page' => env('SHOW_LANDING_PAGE', false),

];
