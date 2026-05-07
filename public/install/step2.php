<?php
// install/step2.php
defined('INSTALL_RUNNING') or die('직접 접근이 금지되어 있습니다.');

$error = '';
$formData = [
    'db_host'     => $_SESSION['db_host'] ?? 'localhost',
    'db_port'     => $_SESSION['db_port'] ?? '3306',
    'db_database' => $_SESSION['db_database'] ?? '',
    'db_username' => $_SESSION['db_username'] ?? '',
    'db_password' => $_SESSION['db_password'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host     = trim($_POST['db_host'] ?? '');
    $port     = trim($_POST['db_port'] ?? '3306');
    $database = trim($_POST['db_database'] ?? '');
    $username = trim($_POST['db_username'] ?? '');
    $password = $_POST['db_password'] ?? '';

    $formData = compact('host', 'port', 'database', 'username', 'password');
    $formData = [
        'db_host'     => $host,
        'db_port'     => $port,
        'db_database' => $database,
        'db_username' => $username,
        'db_password' => $password,
    ];

    if (empty($host) || empty($database) || empty($username)) {
        $error = '호스트, 데이터베이스명, 사용자명은 필수입니다.';
    } else {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);
            // DB 버전 확인
            $dbVersion = $pdo->query('SELECT VERSION()')->fetchColumn();

            // 세션 저장
            $_SESSION['db_host']     = $host;
            $_SESSION['db_port']     = $port;
            $_SESSION['db_database'] = $database;
            $_SESSION['db_username'] = $username;
            $_SESSION['db_password'] = $password;
            $_SESSION['db_version']  = $dbVersion;

            header('Location: ?step=3');
            exit;
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            // 상세 오류 중 민감 정보 일부 제거
            $msg = preg_replace('/\[.*?\]/', '', $msg);
            $error = '데이터베이스 연결 실패: ' . $msg;
        }
    }
}
?>

<h2 class="text-2xl font-bold text-gray-900 mb-2">데이터베이스 설정</h2>
<p class="text-gray-500 mb-6 text-sm">MySQL 데이터베이스가 미리 생성되어 있어야 합니다.</p>

<?php if ($error): ?>
<div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
    <p class="text-red-700 text-sm"><?= htmlspecialchars($error) ?></p>
</div>
<?php endif; ?>

<form method="POST" class="space-y-5">
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">DB 호스트 <span class="text-red-500">*</span></label>
            <input type="text" name="db_host"
                   value="<?= htmlspecialchars($formData['db_host']) ?>"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                   placeholder="localhost" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">포트 <span class="text-red-500">*</span></label>
            <input type="text" name="db_port"
                   value="<?= htmlspecialchars($formData['db_port']) ?>"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                   required>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">데이터베이스명 <span class="text-red-500">*</span></label>
        <input type="text" name="db_database"
               value="<?= htmlspecialchars($formData['db_database']) ?>"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
               placeholder="laraboard" required>
        <p class="text-xs text-gray-400 mt-1">미리 생성되어 있어야 합니다. (문자셋: utf8mb4)</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">DB 사용자명 <span class="text-red-500">*</span></label>
        <input type="text" name="db_username"
               value="<?= htmlspecialchars($formData['db_username']) ?>"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
               required autocomplete="username">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">DB 비밀번호</label>
        <input type="password" name="db_password"
               value="<?= htmlspecialchars($formData['db_password']) ?>"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
               autocomplete="current-password">
    </div>

    <div class="flex justify-between items-center pt-4 border-t">
        <a href="?step=1" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
            ← 이전
        </a>
        <button type="submit" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium text-sm">
            연결 테스트 & 다음 →
        </button>
    </div>
</form>
