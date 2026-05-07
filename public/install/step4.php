<?php
// install/step4.php
defined('INSTALL_RUNNING') or die('직접 접근이 금지되어 있습니다.');

if (!isset($_SESSION['db_host'], $_SESSION['site_name'], $_SESSION['admin_username'])) {
    header('Location: ?step=1');
    exit;
}

$rootDir  = realpath(dirname(dirname(__DIR__)));
$artisan  = $rootDir . '/artisan';

// PHP_BINARY under php-fpm points to the fpm binary, not the CLI.
// Find the matching CLI binary by PHP version, then fall back to /usr/bin/php.
$php = PHP_BINARY;
if (str_contains($php, 'fpm')) {
    $version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    foreach ([
        '/usr/bin/php' . $version,
        '/usr/local/bin/php' . $version,
        '/usr/bin/php',
        '/usr/local/bin/php',
    ] as $candidate) {
        if (is_executable($candidate)) {
            $php = $candidate;
            break;
        }
    }
}

$installSteps = [];
$error  = null;
$success = false;

// ─── 헬퍼: artisan 명령 실행 ───────────────────────────────────────────────
function runArtisan(string $php, string $artisan, string $command): array
{
    $cmd = escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' ' . $command . ' 2>&1';
    $output = [];
    $code = 0;
    exec($cmd, $output, $code);
    return ['code' => $code, 'output' => $output];
}

// ─── 헬퍼: .env 내용 생성 ─────────────────────────────────────────────────
function envQuote(string $val): string
{
    if (preg_match('/[\s#"\'\\\\]/', $val)) {
        return '"' . addcslashes($val, '"\\') . '"';
    }
    return $val;
}

function buildEnvContent(): string
{
    $appKey       = 'base64:' . base64_encode(random_bytes(32));
    $siteUrl      = rtrim($_SESSION['site_url'], '/');
    $appName      = envQuote($_SESSION['site_name']);
    $mailFromName = envQuote($_SESSION['site_name']);
    $dbHost       = $_SESSION['db_host'];
    $dbPort       = $_SESSION['db_port'];
    $dbDb         = $_SESSION['db_database'];
    $dbUser       = $_SESSION['db_username'];
    $dbPass       = envQuote($_SESSION['db_password']);
    $domain       = parse_url($siteUrl, PHP_URL_HOST) ?? 'localhost';

    return <<<ENV
APP_NAME={$appName}
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$siteUrl}
SHOW_LANDING_PAGE=false

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$dbHost}
DB_PORT={$dbPort}
DB_DATABASE={$dbDb}
DB_USERNAME={$dbUser}
DB_PASSWORD={$dbPass}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=sendmail
MAIL_SENDMAIL_PATH="/usr/sbin/sendmail -bs -i"
MAIL_FROM_ADDRESS=noreply@{$domain}
MAIL_FROM_NAME={$mailFromName}
ENV;
}

// ─── 설치 실행 ─────────────────────────────────────────────────────────────
try {

    // 1. .env 파일 생성
    $envContent = buildEnvContent();
    if (file_put_contents($rootDir . '/.env', $envContent) === false) {
        throw new \RuntimeException('.env 파일을 생성할 수 없습니다. 디렉토리 쓰기 권한을 확인하세요.');
    }
    $installSteps[] = ['ok', '.env 파일 생성 완료'];

    // 2. 부트스트랩 캐시 초기화 (이전 캐시가 새 .env 읽는 것을 막지 않도록)
    @unlink($rootDir . '/bootstrap/cache/config.php');
    @unlink($rootDir . '/bootstrap/cache/routes-v7.php');
    @unlink($rootDir . '/bootstrap/cache/services.php');
    $installSteps[] = ['ok', '설정 캐시 초기화 완료'];

    // 3. php artisan migrate --force
    $migrate = runArtisan($php, $artisan, 'migrate --force');
    if ($migrate['code'] !== 0) {
        $detail = implode("\n", array_slice($migrate['output'], 0, 20));
        throw new \RuntimeException("데이터베이스 마이그레이션 실패:\n{$detail}");
    }
    $installSteps[] = ['ok', '데이터베이스 마이그레이션 완료 (' . count($migrate['output']) . '줄 출력)'];

    // 4. 관리자 계정 생성 (마이그레이션 완료 후 PDO로 직접 삽입)
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $_SESSION['db_host'],
        $_SESSION['db_port'],
        $_SESSION['db_database']
    );
    $pdo = new PDO($dsn, $_SESSION['db_username'], $_SESSION['db_password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Laravel 기본값과 동일한 bcrypt cost=12
    $hashedPassword = password_hash($_SESSION['admin_password'], PASSWORD_BCRYPT, ['cost' => 12]);

    $pdo->prepare(
        "INSERT IGNORE INTO users (username, email, password, name, role, is_active, email_verified_at, created_at, updated_at)
         VALUES (?, ?, ?, ?, 'admin', 1, NOW(), NOW(), NOW())"
    )->execute([
        $_SESSION['admin_username'],
        $_SESSION['admin_email'],
        $hashedPassword,
        $_SESSION['admin_name'],
    ]);
    $installSteps[] = ['ok', '관리자 계정 생성 완료'];

    // 5. 기본 사이트 설정 삽입
    $defaultSettings = [
        ['site_name',    $_SESSION['site_name']],
        ['layout_skin',  'basic'],
    ];
    $settingStmt = $pdo->prepare(
        "INSERT IGNORE INTO settings (`key`, value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())"
    );
    foreach ($defaultSettings as [$key, $val]) {
        $settingStmt->execute([$key, $val]);
    }
    $installSteps[] = ['ok', '기본 사이트 설정 완료'];

    // 6. 기본 게시판 생성
    $defaultBoards = [
        ['notice', '공지사항', 0],
        ['free',   '자유게시판', 1],
    ];
    $boardStmt = $pdo->prepare(
        "INSERT IGNORE INTO boards (board_id, board_name, `order`, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())"
    );
    foreach ($defaultBoards as [$boardId, $boardName, $order]) {
        $boardStmt->execute([$boardId, $boardName, $order]);
    }
    $installSteps[] = ['ok', '기본 게시판 생성 완료 (공지사항, 자유게시판)'];

    // 7. php artisan storage:link (실패해도 설치 중단하지 않음)
    $link = runArtisan($php, $artisan, 'storage:link');
    if ($link['code'] === 0) {
        $installSteps[] = ['ok', '스토리지 링크 생성 완료'];
    } else {
        $installSteps[] = ['warn', '스토리지 링크 생성 생략 (이미 존재하거나 해당 없음)'];
    }

    // 8. 설치 완료 잠금 파일 생성
    file_put_contents(__DIR__ . '/installed.lock', date('Y-m-d H:i:s') . "\n설치 완료: " . $_SESSION['site_url']);
    $installSteps[] = ['ok', '설치 완료 잠금 파일 생성'];

    $success = true;
    session_destroy();

} catch (\Throwable $e) {
    $error = $e->getMessage();
}
?>

<?php if ($success): ?>
<!-- 설치 완료 -->
<div class="text-center py-4">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <h2 class="text-2xl font-bold text-gray-900 mb-2">설치가 완료되었습니다!</h2>
    <p class="text-gray-500 text-sm mb-8">Laraboard가 성공적으로 설치되었습니다.</p>

    <!-- 수행된 단계 목록 -->
    <div class="text-left bg-gray-50 rounded-xl p-4 mb-6 space-y-2">
        <?php foreach ($installSteps as [$status, $msg]): ?>
        <div class="flex items-start gap-2 text-sm">
            <?php if ($status === 'ok'): ?>
            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-gray-700"><?= htmlspecialchars($msg) ?></span>
            <?php else: ?>
            <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"></path>
            </svg>
            <span class="text-gray-500"><?= htmlspecialchars($msg) ?></span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 보안 안내 -->
    <div class="text-left bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
        <p class="text-amber-800 font-semibold text-sm mb-2">⚠ 보안 조치 (설치 후 필수)</p>
        <ul class="text-amber-700 text-sm space-y-1 list-disc list-inside">
            <li><code class="bg-white px-1 rounded">install/</code> 폴더를 삭제하거나 접근을 차단하세요.</li>
            <li><code class="bg-white px-1 rounded">.env</code> 파일의 퍼미션을 640으로 설정하세요.</li>
        </ul>
    </div>

    <div class="flex gap-3 justify-center">
        <a href="../" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium">
            사이트로 이동 →
        </a>
        <a href="../admin" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
            관리자 페이지
        </a>
    </div>
</div>

<?php else: ?>
<!-- 설치 실패 -->
<div class="text-center py-4">
    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </div>
    <h2 class="text-2xl font-bold text-gray-900 mb-4">설치 중 오류 발생</h2>

    <!-- 진행된 단계 -->
    <?php if ($installSteps): ?>
    <div class="text-left bg-gray-50 rounded-xl p-4 mb-4 space-y-2">
        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">진행된 단계</p>
        <?php foreach ($installSteps as [$status, $msg]): ?>
        <div class="flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-gray-600"><?= htmlspecialchars($msg) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 오류 메시지 -->
    <div class="text-left bg-red-50 border-l-4 border-red-400 rounded-r-lg p-4 mb-8">
        <p class="text-sm font-semibold text-red-700 mb-1">오류 내용</p>
        <pre class="text-xs text-red-600 whitespace-pre-wrap break-words font-mono"><?= htmlspecialchars($error) ?></pre>
    </div>

    <div class="flex gap-3 justify-center">
        <a href="?step=2" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
            DB 설정 수정
        </a>
        <a href="?step=4" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium text-sm">
            다시 시도
        </a>
    </div>
</div>
<?php endif; ?>
