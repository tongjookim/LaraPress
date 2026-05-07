<?php
// install/step1.php
defined('INSTALL_RUNNING') or die('직접 접근이 금지되어 있습니다.');

$rootDir = dirname(dirname(__DIR__));

$requirements = [
    'PHP >= 8.1' => [
        'ok' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'detail' => 'PHP ' . PHP_VERSION,
    ],
    'PDO Extension' => [
        'ok' => extension_loaded('pdo'),
        'detail' => '',
    ],
    'PDO MySQL Extension' => [
        'ok' => extension_loaded('pdo_mysql'),
        'detail' => '',
    ],
    'Mbstring Extension' => [
        'ok' => extension_loaded('mbstring'),
        'detail' => '',
    ],
    'OpenSSL Extension' => [
        'ok' => extension_loaded('openssl'),
        'detail' => '',
    ],
    'cURL Extension' => [
        'ok' => extension_loaded('curl'),
        'detail' => '',
    ],
    'Tokenizer Extension' => [
        'ok' => extension_loaded('tokenizer'),
        'detail' => '',
    ],
    'JSON Extension' => [
        'ok' => extension_loaded('json'),
        'detail' => '',
    ],
    'exec() 함수 허용' => [
        'ok' => function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions')))),
        'detail' => 'artisan 명령 실행에 필요',
    ],
];

$permissions = [
    'storage/' => [
        'ok' => is_writable($rootDir . '/storage'),
        'path' => 'storage/',
    ],
    'bootstrap/cache/' => [
        'ok' => is_writable($rootDir . '/bootstrap/cache'),
        'path' => 'bootstrap/cache/',
    ],
    '프로젝트 루트 (.env 생성)' => [
        'ok' => is_writable($rootDir) || is_writable($rootDir . '/.env') || !file_exists($rootDir . '/.env'),
        'path' => '프로젝트 루트',
    ],
];

// 실제로 쓰기 가능한지 테스트
$envWritable = @file_put_contents($rootDir . '/.env.writetest', 'test') !== false;
if ($envWritable) {
    @unlink($rootDir . '/.env.writetest');
}
$permissions['프로젝트 루트 (.env 생성)']['ok'] = $envWritable;

$allOk = !in_array(false, array_column($requirements, 'ok')) && !in_array(false, array_column($permissions, 'ok'));
?>

<h2 class="text-2xl font-bold text-gray-900 mb-2">환경 확인</h2>
<p class="text-gray-500 mb-6 text-sm">서버 요구사항과 디렉토리 권한을 확인합니다.</p>

<!-- 서버 요구사항 -->
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">서버 요구사항</h3>
    <div class="space-y-2">
        <?php foreach ($requirements as $name => $info): ?>
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
            <div>
                <span class="text-gray-800 font-medium text-sm"><?= htmlspecialchars($name) ?></span>
                <?php if ($info['detail']): ?>
                <span class="text-gray-400 text-xs ml-2"><?= htmlspecialchars($info['detail']) ?></span>
                <?php endif; ?>
            </div>
            <?php if ($info['ok']): ?>
            <span class="flex items-center text-green-600 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
                확인
            </span>
            <?php else: ?>
            <span class="flex items-center text-red-500 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                미충족
            </span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 디렉토리 권한 -->
<div class="mb-8">
    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">디렉토리 권한</h3>
    <div class="space-y-2">
        <?php foreach ($permissions as $name => $info): ?>
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
            <span class="text-gray-800 font-mono text-sm"><?= htmlspecialchars($name) ?></span>
            <?php if ($info['ok']): ?>
            <span class="flex items-center text-green-600 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
                쓰기 가능
            </span>
            <?php else: ?>
            <span class="flex items-center text-red-500 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                권한 없음
            </span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if (!$allOk): ?>
<div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
    <p class="text-amber-800 text-sm font-medium">⚠ 모든 요구사항을 충족해야 설치를 진행할 수 있습니다.</p>
    <p class="text-amber-700 text-xs mt-1">서버 관리자에게 문의하거나 PHP 설정을 확인하세요.</p>
</div>
<?php endif; ?>

<div class="flex justify-end pt-4 border-t">
    <?php if ($allOk): ?>
    <a href="?step=2" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium">
        다음 단계 →
    </a>
    <?php else: ?>
    <button onclick="location.reload()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
        다시 확인
    </button>
    <?php endif; ?>
</div>
