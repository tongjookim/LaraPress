<?php
// install/step2.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['db_host'] = $_POST['db_host'];
    $_SESSION['db_port'] = $_POST['db_port'];
    $_SESSION['db_database'] = $_POST['db_database'];
    $_SESSION['db_username'] = $_POST['db_username'];
    $_SESSION['db_password'] = $_POST['db_password'];
    
    // DB 연결 테스트
    try {
        $dsn = "mysql:host={$_POST['db_host']};port={$_POST['db_port']};dbname={$_POST['db_database']}";
        $pdo = new PDO($dsn, $_POST['db_username'], $_POST['db_password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        header('Location: ?step=3');
        exit;
    } catch(PDOException $e) {
        $error = "데이터베이스 연결 실패: " . $e->getMessage();
    }
}
?>

<h2 class="text-2xl font-bold text-gray-900 mb-6">데이터베이스 설정</h2>

<?php if(isset($error)): ?>
<div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
    <p class="text-red-700"><?= $error ?></p>
</div>
<?php endif; ?>

<form method="POST" class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">DB 호스트</label>
        <input type="text" name="db_host" value="<?= $_POST['db_host'] ?? 'localhost' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">DB 포트</label>
        <input type="text" name="db_port" value="<?= $_POST['db_port'] ?? '3306' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">데이터베이스명</label>
        <input type="text" name="db_database" value="<?= $_POST['db_database'] ?? '' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               placeholder="laravel_bbs"
               required>
        <p class="text-sm text-gray-500 mt-1">미리 생성되어 있어야 합니다.</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">DB 사용자명</label>
        <input type="text" name="db_username" value="<?= $_POST['db_username'] ?? '' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">DB 비밀번호</label>
        <input type="password" name="db_password" value="<?= $_POST['db_password'] ?? '' ?>" 
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
    </div>

    <div class="flex justify-between items-center pt-6 border-t">
        <a href="?step=1" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            ← 이전
        </a>
        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium">
            연결 테스트 & 다음 →
        </button>
    </div>
</form>