<?php
// install/step3.php
defined('INSTALL_RUNNING') or die('직접 접근이 금지되어 있습니다.');

$error = '';

// 사이트 URL 자동 감지
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
// /install/... 경로 제거
$detectedUrl = rtrim($protocol . '://' . $host, '/');

$formData = [
    'site_name'       => $_SESSION['site_name'] ?? 'Laraboard',
    'site_url'        => $_SESSION['site_url'] ?? $detectedUrl,
    'admin_username'  => $_SESSION['admin_username'] ?? '',
    'admin_name'      => $_SESSION['admin_name'] ?? '',
    'admin_email'     => $_SESSION['admin_email'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name    = trim($_POST['site_name'] ?? '');
    $site_url     = rtrim(trim($_POST['site_url'] ?? ''), '/');
    $admin_username = trim($_POST['admin_username'] ?? '');
    $admin_email  = trim($_POST['admin_email'] ?? '');
    $admin_password = $_POST['admin_password'] ?? '';
    $admin_password_confirm = $_POST['admin_password_confirm'] ?? '';
    $admin_name   = trim($_POST['admin_name'] ?? '');

    $formData = [
        'site_name'      => $site_name,
        'site_url'       => $site_url,
        'admin_username' => $admin_username,
        'admin_name'     => $admin_name,
        'admin_email'    => $admin_email,
    ];

    if (empty($site_name)) {
        $error = '사이트명을 입력하세요.';
    } elseif (empty($site_url) || !filter_var($site_url, FILTER_VALIDATE_URL)) {
        $error = '올바른 사이트 URL을 입력하세요. (예: https://example.com)';
    } elseif (empty($admin_username) || !preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $admin_username)) {
        $error = '아이디는 영문, 숫자, -, _만 사용 가능하며 3~20자여야 합니다.';
    } elseif (empty($admin_email) || !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error = '올바른 이메일 주소를 입력하세요.';
    } elseif (empty($admin_name)) {
        $error = '이름을 입력하세요.';
    } elseif (strlen($admin_password) < 8) {
        $error = '비밀번호는 최소 8자 이상이어야 합니다.';
    } elseif ($admin_password !== $admin_password_confirm) {
        $error = '비밀번호가 일치하지 않습니다.';
    } else {
        $_SESSION['site_name']       = $site_name;
        $_SESSION['site_url']        = $site_url;
        $_SESSION['admin_username']  = $admin_username;
        $_SESSION['admin_email']     = $admin_email;
        $_SESSION['admin_password']  = $admin_password;
        $_SESSION['admin_name']      = $admin_name;

        header('Location: ?step=4');
        exit;
    }
}
?>

<h2 class="text-2xl font-bold text-gray-900 mb-2">사이트 설정</h2>
<p class="text-gray-500 mb-6 text-sm">사이트 기본 정보와 관리자 계정을 설정합니다.</p>

<?php if ($error): ?>
<div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
    <p class="text-red-700 text-sm"><?= htmlspecialchars($error) ?></p>
</div>
<?php endif; ?>

<form method="POST" id="setupForm" class="space-y-5">

    <!-- 사이트 정보 -->
    <div class="pb-4">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">사이트 정보</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">사이트명 <span class="text-red-500">*</span></label>
                <input type="text" name="site_name"
                       value="<?= htmlspecialchars($formData['site_name']) ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                       placeholder="내 사이트" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">사이트 URL <span class="text-red-500">*</span></label>
                <input type="url" name="site_url"
                       value="<?= htmlspecialchars($formData['site_url']) ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                       placeholder="https://example.com" required>
                <p class="text-xs text-gray-400 mt-1">끝에 슬래시(/) 없이 입력 (예: https://example.com)</p>
            </div>
        </div>
    </div>

    <!-- 관리자 계정 -->
    <div class="pt-4 border-t">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">관리자 계정</h3>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">아이디 <span class="text-red-500">*</span></label>
                    <input type="text" name="admin_username"
                           value="<?= htmlspecialchars($formData['admin_username']) ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                           placeholder="admin" required autocomplete="username">
                    <p class="text-xs text-gray-400 mt-1">영문, 숫자, -, _ (3~20자)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">이름 <span class="text-red-500">*</span></label>
                    <input type="text" name="admin_name"
                           value="<?= htmlspecialchars($formData['admin_name']) ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                           placeholder="관리자" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">이메일 <span class="text-red-500">*</span></label>
                <input type="email" name="admin_email"
                       value="<?= htmlspecialchars($formData['admin_email']) ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                       placeholder="admin@example.com" required autocomplete="email">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">비밀번호 <span class="text-red-500">*</span></label>
                    <input type="password" name="admin_password" id="pw1"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                           minlength="8" required autocomplete="new-password">
                    <p class="text-xs text-gray-400 mt-1">최소 8자 이상</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">비밀번호 확인 <span class="text-red-500">*</span></label>
                    <input type="password" name="admin_password_confirm" id="pw2"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                           required autocomplete="new-password">
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center pt-4 border-t">
        <a href="?step=2" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
            ← 이전
        </a>
        <button type="submit" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium text-sm">
            설치 시작 →
        </button>
    </div>
</form>

<script>
document.getElementById('setupForm').addEventListener('submit', function (e) {
    const pw1 = document.getElementById('pw1').value;
    const pw2 = document.getElementById('pw2').value;
    if (pw1 !== pw2) {
        e.preventDefault();
        alert('비밀번호가 일치하지 않습니다.');
        document.getElementById('pw2').focus();
    }
});
</script>
