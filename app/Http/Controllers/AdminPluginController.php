<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminPluginController extends Controller
{
    /** 시스템 플러그인 — 삭제 불가 */
    private const SYSTEM_PLUGINS = ['smarteditor2', 'smtp-mailer'];

    private string $pluginRoot;

    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'role:admin']);
        $this->pluginRoot = resource_path('plugins');
    }

    /** GET /admin/plugins */
    public function index()
    {
        $active  = $this->activePlugins();
        $plugins = $this->scanPlugins($active);

        return view('admin.plugins', compact('plugins'));
    }

    /** POST /admin/plugins/{name}/activate */
    public function activate(string $name)
    {
        $this->assertExists($name);

        $active = $this->activePlugins();
        if (!in_array($name, $active)) {
            $active[] = $name;
            $this->saveActivePlugins($active);
        }

        return back()->with('success', "'{$name}' 플러그인이 활성화되었습니다.");
    }

    /** POST /admin/plugins/{name}/deactivate */
    public function deactivate(string $name)
    {
        $active = array_filter($this->activePlugins(), fn($p) => $p !== $name);
        $this->saveActivePlugins(array_values($active));

        return back()->with('success', "'{$name}' 플러그인이 비활성화되었습니다.");
    }

    /** DELETE /admin/plugins/{name} */
    public function destroy(string $name)
    {
        if (in_array($name, self::SYSTEM_PLUGINS)) {
            return back()->with('error', "시스템 플러그인은 삭제할 수 없습니다.");
        }

        $this->assertExists($name);

        // 활성 목록에서도 제거
        $active = array_filter($this->activePlugins(), fn($p) => $p !== $name);
        $this->saveActivePlugins(array_values($active));

        File::deleteDirectory($this->pluginRoot . '/' . $name);

        return back()->with('success', "'{$name}' 플러그인이 삭제되었습니다.");
    }

    /** GET /admin/plugins/{name}/settings */
    public function settings(string $name)
    {
        $this->assertExists($name);

        $settingsView = $this->pluginRoot . '/' . $name . '/settings.blade.php';
        abort_unless(File::exists($settingsView), 404, '이 플러그인에는 설정 페이지가 없습니다.');

        // 플러그인별 설정값 로드
        $settings = $this->pluginSettings($name);
        $meta     = $this->readMeta($this->pluginRoot . '/' . $name);

        // settings.blade.php를 플러그인 디렉토리에서 동적으로 로드
        $view = view()->file($settingsView, compact('settings', 'meta', 'name'));
        return $view;
    }

    /** POST /admin/plugins/{name}/settings */
    public function settingsUpdate(string $name, Request $request)
    {
        $this->assertExists($name);

        $data = $request->except(['_token', '_method']);

        // 비밀번호 필드가 비어있으면 기존값 유지
        if (isset($data['smtp_password']) && $data['smtp_password'] === '') {
            $existing = $this->pluginSettings($name);
            if (!empty($existing['smtp_password'])) {
                $data['smtp_password'] = $existing['smtp_password'];
            } else {
                unset($data['smtp_password']);
            }
        }

        Setting::set("plugin_{$name}_settings", json_encode($data));

        return back()->with('success', '설정이 저장되었습니다.');
    }

    // ── helpers ──────────────────────────────────────────

    private function pluginSettings(string $name): array
    {
        $json = Setting::get("plugin_{$name}_settings", '{}');
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    private function activePlugins(): array
    {
        $json = Setting::get('active_plugins', '[]');
        $list = json_decode($json, true);
        return is_array($list) ? $list : [];
    }

    private function saveActivePlugins(array $list): void
    {
        Setting::set('active_plugins', json_encode(array_values($list)));
    }

    private function assertExists(string $name): void
    {
        $path = $this->pluginRoot . '/' . $name;
        abort_unless(
            File::isDirectory($path) && preg_match('/^[\w\-\.]+$/', $name),
            404,
            '플러그인을 찾을 수 없습니다.'
        );
    }

    private function scanPlugins(array $active): array
    {
        if (!File::isDirectory($this->pluginRoot)) {
            return [];
        }

        $plugins = [];

        foreach (File::directories($this->pluginRoot) as $dir) {
            $name = basename($dir);
            $meta = $this->readMeta($dir);

            $plugins[] = [
                'name'         => $name,
                'label'        => $meta['name']        ?? $name,
                'description'  => $meta['description'] ?? '',
                'version'      => $meta['version']     ?? '',
                'author'       => $meta['author']      ?? '',
                'homepage'     => $meta['homepage']    ?? '',
                'active'       => in_array($name, $active),
                'system'       => in_array($name, self::SYSTEM_PLUGINS),
                'has_settings' => ($meta['has_settings'] ?? false) && File::exists($dir . '/settings.blade.php'),
                'size'         => $this->dirSize($dir),
            ];
        }

        // 시스템 플러그인 먼저, 이후 가나다순
        usort($plugins, fn($a, $b) =>
            ($b['system'] <=> $a['system']) ?: strcmp($a['name'], $b['name'])
        );

        return $plugins;
    }

    /** package.json → plugin.json 순으로 메타데이터 읽기 */
    private function readMeta(string $dir): array
    {
        foreach (['plugin.json', 'package.json'] as $file) {
            $path = $dir . '/' . $file;
            if (File::exists($path)) {
                $data = json_decode(File::get($path), true);
                if (is_array($data)) {
                    // author가 배열(people 객체)인 경우 문자열로 변환
                    if (isset($data['author']) && is_array($data['author'])) {
                        $a = $data['author'];
                        $data['author'] = trim(($a['name'] ?? '') . ' ' . ($a['email'] ?? ''));
                    }
                    return $data;
                }
            }
        }
        return [];
    }

    /** 디렉토리 크기 (MB 단위 문자열) */
    private function dirSize(string $dir): string
    {
        $output = shell_exec('du -sb ' . escapeshellarg($dir) . ' 2>/dev/null');
        if ($output && preg_match('/^(\d+)/', $output, $m)) {
            $size = (int) $m[1];
            if ($size >= 1048576) return round($size / 1048576, 1) . ' MB';
            if ($size >= 1024)    return round($size / 1024, 1) . ' KB';
            return $size . ' B';
        }
        return '—';
    }
}
