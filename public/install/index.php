<?php
// install/index.php
ob_start();
session_start();

$installedLock = __DIR__ . '/installed.lock';
$rootDir = dirname(dirname(__DIR__));

function parseEnvFile(string $path): array
{
    $env = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$key, $val] = explode('=', $line, 2);
        $val = trim($val);
        if (strlen($val) >= 2 && $val[0] === '"' && $val[-1] === '"') {
            $val = stripslashes(substr($val, 1, -1));
        }
        $env[trim($key)] = $val;
    }
    return $env;
}

function isAlreadyConfigured(string $rootDir): bool
{
    $envFile = $rootDir . '/.env';
    if (!file_exists($envFile)) {
        return false;
    }

    $env = parseEnvFile($envFile);

    if (empty($env['APP_KEY']) || empty($env['DB_HOST']) || empty($env['DB_DATABASE'])) {
        return false;
    }

    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $env['DB_HOST'],
            $env['DB_PORT'] ?? '3306',
            $env['DB_DATABASE']
        );
        $pdo = new PDO($dsn, $env['DB_USERNAME'] ?? '', $env['DB_PASSWORD'] ?? '', [
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT    => 3,
        ]);
        $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
        return $stmt && $stmt->rowCount() > 0;
    } catch (\Exception $e) {
        return false;
    }
}

// installed.lock 파일이 있을 때만 완료 처리 (isAlreadyConfigured는 DB 간헐적 응답으로 마법사를 차단하므로 제외)
$alreadyInstalled = file_exists($installedLock);

// 재설치 모드
if (isset($_GET['reset'])) {
    $_SESSION = [];
    session_destroy();
    session_start();
    @unlink($installedLock);
    @unlink($rootDir . '/.env');
    @unlink($rootDir . '/bootstrap/cache/config.php');
    @unlink($rootDir . '/bootstrap/cache/routes-v7.php');
    @unlink($rootDir . '/bootstrap/cache/services.php');
    header('Location: index.php?step=1');
    exit;
}

// 이미 설치된 경우
if ($alreadyInstalled) {
    ?>
    <!DOCTYPE html>
    <html lang="ko">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>이미 설치됨 - Laraboard</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">이미 설치되었습니다</h1>
                <p class="text-gray-600">Laraboard가 이미 설치되어 있습니다.</p>
            </div>
            <div class="space-y-3">
                <a href="../" class="block w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-center font-medium">
                    사이트로 이동
                </a>
                <a href="../admin" class="block w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-center font-medium">
                    관리자 페이지
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$step = max(1, min(4, (int)($_GET['step'] ?? 1)));

// 단계 이동 가드: 세션 데이터 없이 앞 단계로 접근 시 되돌리기
if ($step >= 3 && !isset($_SESSION['db_host'])) {
    header('Location: index.php?step=2');
    exit;
}
if ($step >= 4 && !isset($_SESSION['site_name'])) {
    header('Location: index.php?step=3');
    exit;
}

$stepLabels = ['환경 확인', 'DB 설정', '사이트 설정', '설치 실행'];
define('INSTALL_RUNNING', true);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laraboard 설치</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Noto Sans KR', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-50 min-h-screen py-10">
    <div class="max-w-2xl mx-auto px-4">

        <!-- 헤더 -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-2">
                Laraboard 설치
            </h1>
            <p class="text-gray-500">단계별로 설치를 진행합니다</p>
        </div>

        <!-- 진행 단계 -->
        <div class="mb-8">
            <div class="flex items-start">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="flex-1 <?= $i < 4 ? 'pr-2' : '' ?>">
                    <div class="flex items-center">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0
                            <?= $step > $i ? 'bg-purple-600 text-white' : ($step === $i ? 'bg-purple-600 text-white ring-4 ring-purple-100' : 'bg-gray-200 text-gray-500') ?>">
                            <?php if ($step > $i): ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <?php else: ?>
                            <?= $i ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($i < 4): ?>
                        <div class="flex-1 h-0.5 mx-1 <?= $step > $i ? 'bg-purple-600' : 'bg-gray-200' ?>"></div>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs mt-2 <?= $step === $i ? 'text-purple-700 font-semibold' : 'text-gray-400' ?>">
                        <?= $stepLabels[$i - 1] ?>
                    </p>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- 단계 내용 -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <?php
            switch ($step) {
                case 1: include __DIR__ . '/step1.php'; break;
                case 2: include __DIR__ . '/step2.php'; break;
                case 3: include __DIR__ . '/step3.php'; break;
                case 4: include __DIR__ . '/step4.php'; break;
            }
            ?>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">Laraboard &copy; <?= date('Y') ?></p>
    </div>
</body>
</html>
