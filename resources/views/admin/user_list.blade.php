<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full leading-normal">
        <thead>
            <tr class="bg-gray-100 text-gray-600 text-left text-xs uppercase font-semibold">
                <th class="px-5 py-3 border-b">이름</th>
                <th class="px-5 py-3 border-b">이메일</th>
                <th class="px-5 py-3 border-b">가입일</th>
                <th class="px-5 py-3 border-b">상태</th>
                <th class="px-5 py-3 border-b text-center">관리</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @foreach($users as $user)
            <tr>
                <td class="px-5 py-5 border-b">{{ $user->name }}</td>
                <td class="px-5 py-5 border-b">{{ $user->email }}</td>
                <td class="px-5 py-5 border-b">{{ $user->created_at->format('Y-m-d') }}</td>
                <td class="px-5 py-5 border-b">
                    <span class="{{ $user->status == 'active' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $user->status }}
                    </span>
                </td>
                <td class="px-5 py-5 border-b text-center">
                    <form action="/admin/user/{{ $user->id }}" method="POST" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-800 font-bold">삭제</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>