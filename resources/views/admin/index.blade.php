<x-app-layout>
    <x-slot name="header">
        <form action="/logout" method="post">
            @csrf
            <button
                class="px-5 py-1.5 border border-[#ddd8d3] text-[#c4bab0] bg-white rounded hover:bg-gray-50 transition lowercase text-sm">logout</button>
        </form>
    </x-slot>

    <div class="min-h-screen bg-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Adminタイトル -->
            <h2 class="text-center text-2xl font-serif text-amber-900 mb-6">Admin</h2>

            <!-- 削除成功時のメッセージ表示 -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 検索フォーム -->
            <div class="mb-4">
                <form class="flex flex-wrap items-center gap-3" action="/admin" method="get">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="fullname" value="{{ request('fullname') }}"
                            placeholder="名前を入力してください"
                            class="w-full px-4 py-2 bg-white border border-[#ddd8d3] rounded text-gray-700 placeholder-[#c4bab0] focus:outline-none focus:border-amber-500" />
                    </div>
                    <div class="min-w-[100px]">
                        <select name="gender"
                            class="w-full px-4 py-2 bg-white border border-[#ddd8d3] rounded text-[#9a938c] focus:outline-none focus:border-amber-500">
                            <option value="all" {{ request('gender') == 'all' || !request('gender') ? 'selected' : '' }}>性別</option>
                            <option value="1" {{ request('gender') == '1' ? 'selected' : '' }}>男性</option>
                            <option value="2" {{ request('gender') == '2' ? 'selected' : '' }}>女性</option>
                            <option value="3" {{ request('gender') == '3' ? 'selected' : '' }}>その他</option>
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <select name="category_id"
                            class="w-full px-4 py-2 bg-white border border-[#ddd8d3] rounded text-[#9a938c] focus:outline-none focus:border-amber-500">
                            <option value="">お問い合わせの種類</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->content }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[130px]">
                        <input type="date" name="date" value="{{ request('date') }}"
                            placeholder="年/月/日"
                            class="w-full px-4 py-2 bg-white border border-[#ddd8d3] rounded text-[#9a938c] focus:outline-none focus:border-amber-500" />
                    </div>
                    <div>
                        <button type="submit" class="px-6 py-2 bg-[#82746a] text-white rounded hover:bg-[#6b5f57]">
                            検索
                        </button>
                    </div>
                    <div>
                        <a href="/admin"
                            class="px-6 py-2 bg-[#e8ddd2] text-[#9a938c] rounded hover:bg-[#ddd2c7] inline-block">
                            リセット
                        </a>
                    </div>
                    <div>
                        <a href="/contacts/export?{{ http_build_query(request()->query()) }}"
                            class="px-6 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 inline-block">
                            エクスポート
                        </a>
                    </div>
                    <!-- ページネーション -->
                    <div class="flex items-center">
                        {{ $contacts->links() }}
                    </div>
                </form>
            </div>

            <!-- テーブル -->
            <div class="bg-white rounded overflow-hidden border border-gray-200">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[#a89e94]">
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">お名前</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">性別</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">メールアドレス</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">お問い合わせの種類</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">タグ</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($contacts as $contact)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $contact->formatted_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @php
                                        $genderLabels = [1 => '男性', 2 => '女性', 3 => 'その他'];
                                    @endphp
                                    {{ $genderLabels[$contact->gender] ?? '' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $contact->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $contact->category->content ?? '' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @if(isset($contact->tags))
                                        @foreach ($contact->tags as $tag)
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1">{{ $tag->name }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <button type="button" onclick="openModal({{ $contact->id }})"
                                        class="text-amber-600 hover:text-amber-800 focus:outline-none font-medium">詳細</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">データがありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
       <!-- 詳細表示用モーダル -->
    <div id="contactModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6 relative">
                <button type="button" onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold focus:outline-none">&times;</button>
                <h3 class="text-xl font-serif text-amber-900 border-b pb-3 mb-4 text-center">お問い合わせ詳細</h3>
                <div class="space-y-4 text-sm text-gray-700">
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">お名前</span><span id="modal-name" class="col-span-2"></span></div>
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">性別</span><span id="modal-gender" class="col-span-2"></span></div>
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">メールアドレス</span><span id="modal-email" class="col-span-2 break-all"></span></div>
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">電話番号</span><span id="modal-tel" class="col-span-2"></span></div>
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">住所</span><span id="modal-address" class="col-span-2"></span></div>
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">建物名</span><span id="modal-building" class="col-span-2"></span></div>
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">お問い合わせの種類</span><span id="modal-category" class="col-span-2"></span></div>
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">タグ</span><span id="modal-tags" class="col-span-2"></span></div>
                    <div class="grid grid-cols-3 border-b pb-2"><span class="font-medium text-gray-500">お問い合わせ内容</span><span id="modal-detail" class="col-span-2 whitespace-pre-wrap bg-gray-50 p-2 rounded border"></span></div>
                </div>
                <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                    <form id="delete-form" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('本当にこのお問い合わせデータを削除しますか？')" 
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition font-medium focus:outline-none">
                            削除
                        </button>
                    </form>
                    <button type="button" onclick="closeModal()" 
                        class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded text-sm transition font-medium focus:outline-none">
                        閉じる
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById('contactModal');
            fetch(`/admin/contacts/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modal-name').textContent = data.formatted_name;
                    document.getElementById('modal-gender').textContent = data.gender_label;
                    document.getElementById('modal-email').textContent = data.email;
                    document.getElementById('modal-tel').textContent = data.tel;
                    document.getElementById('modal-address').textContent = data.address;
                    document.getElementById('modal-building').textContent = data.building || '—';
                    document.getElementById('modal-category').textContent = data.category ? data.category.content : '—';
                    document.getElementById('modal-detail').textContent = data.detail;

                    const tagsContainer = document.getElementById('modal-tags');
                    tagsContainer.innerHTML = '';
                    if (data.tags && data.tags.length > 0) {
                        data.tags.forEach(tag => {
                            const span = document.createElement('span');
                            span.className = 'inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1';
                            span.textContent = tag.name;
                            tagsContainer.appendChild(span);
                        });
                    } else {
                        tagsContainer.textContent = '—';
                    }

                    document.getElementById('delete-form').action = `/admin/contacts/${id}`;
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    alert('データの取得に失敗しました。');
                    console.error('Error:', error);
                });
        }

        function closeModal() {
            const modal = document.getElementById('contactModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    </script>
</x-app-layout>