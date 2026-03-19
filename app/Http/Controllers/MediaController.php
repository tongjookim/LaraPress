<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    private const ALLOWED_MIME = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /**
     * AJAX 파일 업로드
     * POST /upload/image
     */
    public function upload(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'max:10240']]);

        $file = $request->file('file');

        if (!in_array($file->getMimeType(), self::ALLOWED_MIME)) {
            return response()->json(['error' => '허용되지 않는 파일 형식입니다.'], 422);
        }

        $date     = now()->format('Y/m');
        $filename = $date . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        Storage::disk('uploads')->put($filename, file_get_contents($file->getRealPath()));

        $url = '/uploads/' . $filename;

        $media = Media::create([
            'user_id'       => auth()->id(),
            'original_name' => $file->getClientOriginalName(),
            'filename'      => basename($filename),
            'path'          => $filename,
            'url'           => $url,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'title'         => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
        ]);

        return response()->json($this->mediaToArray($media));
    }

    /**
     * 미디어 라이브러리 목록
     * GET /admin/media          → HTML 뷰
     * GET /admin/media?format=json → JSON (모달 AJAX 용)
     */
    public function index(Request $request)
    {
        $query = Media::with('user')->orderBy('created_at', 'desc');

        if ($type = $request->input('type')) {
            $type === 'image'
                ? $query->where('mime_type', 'like', 'image/%')
                : $query->where('mime_type', 'not like', 'image/%');
        }

        if ($search = $request->input('search')) {
            $query->where('original_name', 'like', "%{$search}%");
        }

        if ($request->input('format') === 'json' || $request->expectsJson()) {
            $media = $query->paginate(24)->withQueryString();
            return response()->json([
                'data'         => $media->getCollection()->map(fn($m) => $this->mediaToArray($m))->values(),
                'current_page' => $media->currentPage(),
                'last_page'    => $media->lastPage(),
                'total'        => $media->total(),
            ]);
        }

        $media = $query->paginate(24)->withQueryString();
        return view('admin.media', compact('media'));
    }

    /**
     * 미디어 피커 AJAX (role:editor 불필요 — 기사 작성자도 호출)
     * GET /admin/media-picker?format=json&type=image&search=...&page=1
     */
    public function pickerData(Request $request)
    {
        $query = Media::with('user')->orderBy('created_at', 'desc');

        if ($type = $request->input('type')) {
            $type === 'image'
                ? $query->where('mime_type', 'like', 'image/%')
                : $query->where('mime_type', 'not like', 'image/%');
        }

        if ($search = $request->input('search')) {
            $query->where('original_name', 'like', "%{$search}%");
        }

        $media = $query->paginate(24)->withQueryString();

        return response()->json([
            'data'         => $media->getCollection()->map(fn($m) => $this->mediaToArray($m))->values(),
            'current_page' => $media->currentPage(),
            'last_page'    => $media->lastPage(),
            'total'        => $media->total(),
        ]);
    }

    /**
     * 단일 미디어 상세
     * GET /admin/media/{id}         → HTML 편집 페이지
     * GET /admin/media/{id}?format=json → JSON
     */
    public function show(Request $request, $id)
    {
        $media = Media::with('user')->findOrFail($id);

        if ($request->input('format') === 'json' || $request->expectsJson()) {
            return response()->json($this->mediaToArray($media));
        }

        return view('admin.media-edit', compact('media'));
    }

    /**
     * 미디어 메타데이터 업데이트 (JSON)
     * PUT /admin/media/{id}
     */
    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $validated = $request->validate([
            'alt_text'    => 'nullable|max:255',
            'title'       => 'nullable|max:255',
            'caption'     => 'nullable|max:1000',
            'description' => 'nullable|max:2000',
        ]);

        $media->update($validated);

        return response()->json(['ok' => true, 'media' => $this->mediaToArray($media)]);
    }

    /**
     * 미디어 삭제
     * DELETE /admin/media/{id}
     */
    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        Storage::disk('uploads')->delete($media->path);
        $media->delete();

        return response()->json(['ok' => true]);
    }

    /** Media 모델 → JSON 배열 */
    private function mediaToArray(Media $m): array
    {
        return [
            'id'            => $m->id,
            'url'           => $m->url,
            'original_name' => $m->original_name,
            'filename'      => $m->filename,
            'mime_type'     => $m->mime_type,
            'size'          => $m->formattedSize(),
            'size_raw'      => $m->size,
            'is_image'      => $m->isImage(),
            'alt_text'      => $m->alt_text ?? '',
            'title'         => $m->title ?? '',
            'caption'       => $m->caption ?? '',
            'description'   => $m->description ?? '',
            'uploaded_by'   => $m->user->name ?? '알 수 없음',
            'created_at'    => $m->created_at->format('Y년 m월 d일 H:i'),
        ];
    }
}
