<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'               => 'required|max:100',
            'email'              => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'bio'                => 'nullable|max:1000',
            'password'           => 'nullable|min:6|confirmed',
            'profile_image_file' => 'nullable|file|image|max:2048',
            'social_facebook'    => 'nullable|url|max:300',
            'social_x'           => 'nullable|url|max:300',
            'social_instagram'   => 'nullable|url|max:300',
            'social_linkedin'    => 'nullable|url|max:300',
            'social_website'     => 'nullable|url|max:300',
            'social_blog'        => 'nullable|url|max:300',
            'social_pixabay'     => 'nullable|url|max:300',
            'social_wikipedia'   => 'nullable|url|max:300',
            'social_email'       => 'nullable|email|max:200',
        ], [
            'email.unique'    => '이미 사용 중인 이메일입니다.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->bio   = $validated['bio'] ?? '';

        $socialFields = ['social_facebook','social_x','social_instagram','social_linkedin',
                         'social_website','social_blog','social_pixabay','social_wikipedia','social_email'];
        foreach ($socialFields as $field) {
            $user->$field = $validated[$field] ?? null;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_image_file')) {
            $file = $request->file('profile_image_file');
            $path = 'profiles/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('uploads')->put($path, file_get_contents($file->getRealPath()));
            $user->profile_image = '/uploads/' . $path;
        } elseif ($request->input('clear_profile_image')) {
            $user->profile_image = null;
        }

        $user->save();

        return back()->with('success', '프로필이 업데이트되었습니다.');
    }
}
