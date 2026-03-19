<?php
// install/step3.php

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 입력값 검증
    $site_name = trim($_POST['site_name'] ?? '');
    $admin_username = trim($_POST['admin_username'] ?? '');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_password = $_POST['admin_password'] ?? '';
    $admin_password_confirm = $_POST['admin_password_confirm'] ?? '';
    $admin_name = trim($_POST['admin_name'] ?? '');
    
    // 유효성 검사
    if (empty($site_name)) {
        $error = '사이트명을 입력하세요.';
    } elseif (empty($admin_username) || !preg_match('/^[a-zA-Z0-9_-]+$/', $admin_username)) {
        $error = '아이디는 영문, 숫자, -, _만 사용 가능합니다.';
    } elseif (empty($admin_email) || !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error = '올바른 이메일 주소를 입력하세요.';
    } elseif (empty($admin_name)) {
        $error = '이름을 입력하세요.';
    } elseif (strlen($admin_password) < 6) {
        $error = '비밀번호는 최소 6자 이상이어야 합니다.';
    } elseif ($admin_password !== $admin_password_confirm) {
        $error = '비밀번호가 일치하지 않습니다.';
    } else {
        // 모든 검증 통과
        $_SESSION['site_name'] = $site_name;
        $_SESSION['admin_username'] = $admin_username;
        $_SESSION['admin_email'] = $admin_email;
        $_SESSION['admin_password'] = $admin_password;
        $_SESSION['admin_name'] = $admin_name;
        
        header('Location: ?step=4');
        exit;
    }
}
?>

<h2 class="text-2xl font-bold text-gray-900 mb-6">관리자 계정 설정</h2>

<?php if ($error): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
    <p class="text-red-700"><?= htmlspecialchars($error) ?></p>
</div>
<?php endif; ?>

<form method="POST" class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">사이트명</label>
        <input type="text" name="site_name" value="<?= $_POST['site_name'] ?? 'My BBS' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               required>
    </div>

    <div class="pt-4 border-t">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">관리자 정보</h3>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">아이디</label>
        <input type="text" name="admin_username" value="<?= $_POST['admin_username'] ?? '' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               placeholder="admin"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">이름</label>
        <input type="text" name="admin_name" value="<?= $_POST['admin_name'] ?? '' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               placeholder="관리자"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">이메일</label>
        <input type="email" name="admin_email" value="<?= $_POST['admin_email'] ?? '' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               placeholder="admin@example.com"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">비밀번호</label>
        <input type="password" name="admin_password" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               minlength="6"
               required>
        <p class="text-sm text-gray-500 mt-1">최소 6자 이상</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">비밀번호 확인</label>
        <input type="password" name="admin_password_confirm" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               required>
    </div>

    <div class="flex justify-between items-center pt-6 border-t">
        <a href="?step=2" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            ← 이전
        </a>
        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium">
            설치 시작 →
        </button>
    </div>
</form>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="admin_password"]').value;
    const confirm = document.querySelector('input[name="admin_password_confirm"]').value;
    
    if(password !== confirm) {
        e.preventDefault();
        alert('비밀번호가 일치하지 않습니다.');
    }
});
</script>