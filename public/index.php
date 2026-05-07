<?php
/**
 * Laraboard - 루트 진입점 통합 스크립트
 * * 이 파일은 공통 오토로드와 부트스트랩을 로드하여
 * 라라벨 애플리케이션을 실행합니다.
 */

define('LARAVEL_START', microtime(true));

// 1. Composer 오토로드 파일 확인 및 로드
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    die('Error: vendor/autoload.php 파일을 찾을 수 없습니다. <br>터미널에서 "composer install"을 실행하세요.');
}

require __DIR__.'/../vendor/autoload.php';

// 2. 라라벨 애플리케이션 부트스트랩
if (!file_exists(__DIR__.'/../bootstrap/app.php')) {
    die('Error: bootstrap/app.php 파일을 찾을 수 없습니다.');
}

$app = require_once __DIR__.'/../bootstrap/app.php';

// 3. HTTP 커널을 통한 요청 처리
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// 4. 응답 전송 및 종료
$response->send();

$kernel->terminate($request, $response);
