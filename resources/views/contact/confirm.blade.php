<x-guest-layout>
    <div class="bg-white min-h-screen">
        <div class="max-w-3xl mx-auto px-8 py-12">
            <h1 class="text-2xl font-serif text-[#6b5744] text-center mb-10">Confirm</h1>

            <form action="{{ route('contact.store') }}" method="post">
                @csrf
                
                <!-- 送信用の隠しデータ -->
                <input type="hidden" name="first_name" value="{{ $inputs['first_name'] }}">
                <input type="hidden" name="last_name" value="{{ $inputs['last_name'] }}">
                <input type="hidden" name="gender" value="{{ $inputs['gender'] }}">
                <input type="hidden" name="email" value="{{ $inputs['email'] }}">
                <input type="hidden" name="tel" value="{{ $inputs['tel'] }}">
                <input type="hidden" name="address" value="{{ $inputs['address'] }}">
                <input type="hidden" name="building" value="{{ $inputs['building'] ?? '' }}">
                <input type="hidden" name="category_id" value="{{ $inputs['category_id'] }}">
                <input type="hidden" name="detail" value="{{ $inputs['detail'] }}">
                @if(!empty($inputs['tag_ids']))
                    @foreach($inputs['tag_ids'] as $tag_id)
                        <input type="hidden" name="tag_ids[]" value="{{ $tag_id }}">
                    @endforeach
                @endif

                <table class="w-full text-left border-collapse mb-10">
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700 w-1/3">お名前</th>
                        <td class="py-4 text-gray-800">{{ $inputs['first_name'] }} {{ $inputs['last_name'] }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700">性別</th>
                        <td class="py-4 text-gray-800">
                            @if($inputs['gender'] == '1') 男性
                            @elseif($inputs['gender'] == '2') 女性
                            @else その他
                            @endif
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700">メールアドレス</th>
                        <td class="py-4 text-gray-800">{{ $inputs['email'] }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700">電話番号</th>
                        <td class="py-4 text-gray-800">{{ $inputs['tel'] }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700">住所</th>
                        <td class="py-4 text-gray-800">{{ $inputs['address'] }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700">建物名</th>
                        <td class="py-4 text-gray-800">{{ $inputs['building'] ?? '' }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700">お問い合わせの種類</th>
                        <td class="py-4 text-gray-800">{{ $category->content ?? '' }}</td>
                    </tr>
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700">タグ</th>
                        <td class="py-4 text-gray-800">
                            {{ $selectedTags->pluck('name')->implode(', ') }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <th class="py-4 font-medium text-gray-700">お問い合わせ内容</th>
                        <td class="py-4 text-gray-800 whitespace-pre-wrap">{{ $inputs['detail'] }}</td>
                    </tr>
                </table>

                <div class="flex justify-center gap-6">
                    <!-- 「送信」ボタン -->
                    <button type="submit"
                        class="px-16 py-3 bg-[#7d7470] hover:bg-[#6b5f57] border border-transparent rounded font-medium text-white transition">
                        送信
                    </button>
                    
                    <!-- 【要件修正】ただ戻るのではなく、データを保持してコントローラーに送るためのボタンに変更 -->
                    <button type="submit" name="back"
                        class="px-8 py-3 text-gray-500 hover:text-gray-700 underline font-medium transition">
                        修正
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
