<?php
// install/step1.php

$requirements = [
    'PHP 버전 >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
    'PDO Extension' => extension_loaded('pdo'),
    'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
    'Mbstring Extension' => extension_loaded('mbstring'),
    'OpenSSL Extension' => extension_loaded('openssl'),
    'Tokenizer Extension' => extension_loaded('tokenizer'),
    'JSON Extension' => extension_loaded('json'),
];

$permissions = [
    '../storage' => is_writable('../storage'),
    '../bootstrap/cache' => is_writable('../bootstrap/cache'),
];

$allOk = !in_array(false, $requirements) && !in_array(false, $permissions);
?>

<h2 class="text-2xl font-bold text-gray-900 mb-6">환경 확인</h2>

<!-- Requirements -->
<div class="mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">서버 요구사항</h3>
    <div class="space-y-2">
        <?php foreach($requirements as $name => $status): ?>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <span class="text-gray-700"><?= $name ?></span>
            <?php if($status): ?>
            <span class="flex items-center text-green-600 font-medium">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                통과
            </span>
            <?php else: ?>
            <span class="flex items-center text-red-600 font-medium">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                실패
            </span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Permissions -->
<div class="mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">디렉토리 권한</h3>
    <div class="space-y-2">
        <?php foreach($permissions as $path => $status): ?>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <span class="text-gray-700 font-mono text-sm"><?= $path ?></span>
            <?php if($status): ?>
            <span class="flex items-center text-green-600 font-medium">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                쓰기 가능
            </span>
            <?php else: ?>
            <span class="flex items-center text-red-600 font-medium">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                권한 없음
            </span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Next Button -->
<div class="flex justify-end pt-6 border-t">
    <?php if($allOk): ?>
    <a href="?step=2" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium">
        다음 단계 →
    </a>
    <?php else: ?>
    <div class="text-red-600 font-medium">
        모든 요구사항을 충족해야 설치를 진행할 수 있습니다.
    </div>
    <?php endif; ?>
</div>