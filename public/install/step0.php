<?php
// install/step0.php
defined('INSTALL_RUNNING') or die('직접 접근이 금지되어 있습니다.');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agree'])) {
    $_SESSION['license_agreed'] = true;
    header('Location: index.php?step=1');
    exit;
}
?>

<h2 class="text-2xl font-bold text-gray-900 mb-2">라이선스 동의</h2>
<p class="text-gray-500 mb-6 text-sm">설치를 진행하기 전에 아래 MIT 라이선스 내용을 확인하고 동의해 주세요.</p>

<div class="mb-6">
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 h-64 overflow-y-auto font-mono text-xs text-gray-700 leading-relaxed whitespace-pre-wrap">MIT License

Copyright (c) <?= date('Y') ?> 수완뉴스 (The Suwan News Company)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.</div>
</div>

<form method="post">
    <label class="flex items-start gap-3 cursor-pointer mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition">
        <input type="checkbox" name="agree" id="agreeCheck"
            class="mt-0.5 w-4 h-4 accent-purple-600 cursor-pointer flex-shrink-0">
        <span class="text-sm text-gray-700">
            위 MIT 라이선스 내용을 읽었으며 이에 동의합니다.
        </span>
    </label>

    <div class="flex justify-end pt-4 border-t">
        <button type="submit" id="nextBtn" disabled
            class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg font-medium transition opacity-40 cursor-not-allowed">
            다음 단계 →
        </button>
    </div>
</form>

<script>
document.getElementById('agreeCheck').addEventListener('change', function () {
    const btn = document.getElementById('nextBtn');
    if (this.checked) {
        btn.disabled = false;
        btn.classList.remove('opacity-40', 'cursor-not-allowed');
        btn.classList.add('hover:shadow-lg');
    } else {
        btn.disabled = true;
        btn.classList.add('opacity-40', 'cursor-not-allowed');
        btn.classList.remove('hover:shadow-lg');
    }
});
</script>
