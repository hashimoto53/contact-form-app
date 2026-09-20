<!-- お名前 -->
<div class="mb-6 flex flex-col md:flex-row md:items-center">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0">
        お名前 <span class="text-red-500">※</span>
    </label>
    <div class="w-full md:w-2/3 flex gap-4">
        <!-- 左側：姓（苗字） -->
        <div class="w-1/2">
            <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="例: 山田"
                class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none">
            @error('last_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <!-- 右側：名（名前） -->
        <div class="w-1/2">
            <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="例: 太郎"
                class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none">
            @error('first_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- 性別 -->
<div class="mb-6 flex flex-col md:flex-row md:items-center">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0">
        性別 <span class="text-red-500">※</span>
    </label>
    <div class="w-full md:w-2/3 flex items-center gap-6">
        <label class="inline-flex items-center cursor-pointer">
            <input type="radio" name="gender" value="1" {{ old('gender', '1') == '1' ? 'checked' : '' }} class="text-gray-600 focus:ring-0">
            <span class="ml-2">男性</span>
        </label>
        <label class="inline-flex items-center cursor-pointer">
            <input type="radio" name="gender" value="2" {{ old('gender') == '2' ? 'checked' : '' }} class="text-gray-600 focus:ring-0">
            <span class="ml-2">女性</span>
        </label>
        <label class="inline-flex items-center cursor-pointer">
            <input type="radio" name="gender" value="3" {{ old('gender') == '3' ? 'checked' : '' }} class="text-gray-600 focus:ring-0">
            <span class="ml-2">その他</span>
        </label>
    </div>
</div>
@error('gender')
    <p class="text-red-500 text-sm mt-1 md:ml-[33.333%]">{{ $message }}</p>
@enderror

<!-- メールアドレス -->
<div class="mb-6 flex flex-col md:flex-row md:items-center">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0">
        メールアドレス <span class="text-red-500">※</span>
    </label>
    <div class="w-full md:w-2/3">
        <input type="email" name="email" value="{{ old('email') }}" placeholder="例: test@example.com"
            class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none">
        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- 電話番号 -->
<div class="mb-6 flex flex-col md:flex-row md:items-center">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0">
        電話番号 <span class="text-red-500">※</span>
    </label>
    <div class="w-full md:w-2/3">
        <div class="flex items-center gap-2">
            @php
                // 修正で戻ったときやバリデーションエラー時、結合された 'tel' から各パーツを復元します
                $fullTel = old('tel');
                $t1 = old('tel1', $fullTel ? substr($fullTel, 0, 3) : '');
                // 一般的な携帯(11桁)または固定電話の桁数に応じて残りを抽出
                $t2 = old('tel2', $fullTel ? (strlen($fullTel) === 11 ? substr($fullTel, 3, 4) : substr($fullTel, 3, 3)) : '');
                $t3 = old('tel3', $fullTel ? (strlen($fullTel) === 11 ? substr($fullTel, 7, 4) : substr($fullTel, 6, 4)) : '');
            @endphp
            <input type="text" id="tel1" name="tel1" value="{{ $t1 }}" maxlength="4" placeholder="080" class="w-1/3 px-4 py-2 bg-gray-100 border border-transparent rounded text-center focus:bg-white focus:border-gray-400 focus:outline-none">
            <span>-</span>
            <input type="text" id="tel2" name="tel2" value="{{ $t2 }}" maxlength="4" placeholder="1234" class="w-1/3 px-4 py-2 bg-gray-100 border border-transparent rounded text-center focus:bg-white focus:border-gray-400 focus:outline-none">
            <span>-</span>
            <input type="text" id="tel3" name="tel3" value="{{ $t3 }}" maxlength="4" placeholder="5678" class="w-1/3 px-4 py-2 bg-gray-100 border border-transparent rounded text-center focus:bg-white focus:border-gray-400 focus:outline-none">
            <input type="hidden" name="tel" id="tel" value="{{ $fullTel }}">
        </div>
        @error('tel')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- 住所 -->
<div class="mb-6 flex flex-col md:flex-row md:items-center">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0">
        住所 <span class="text-red-500">※</span>
    </label>
    <div class="w-full md:w-2/3">
        <input type="text" name="address" value="{{ old('address') }}" placeholder="例: 東京都渋谷区千駄ヶ谷1-2-3"
            class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none">
        @error('address')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- 建物名 -->
<div class="mb-6 flex flex-col md:flex-row md:items-center">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0">
        建物名
    </label>
    <div class="w-full md:w-2/3">
        <input type="text" name="building" value="{{ old('building') }}" placeholder="例: 千駄ヶ谷マンション305"
            class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none">
    </div>
</div>

<!-- お問い合わせの種類 -->
<div class="mb-6 flex flex-col md:flex-row md:items-center">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0">
        お問い合わせの種類 <span class="text-red-500">※</span>
    </label>
    <div class="w-full md:w-2/3">
        <select name="category_id"
            class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none text-gray-700">
            <option value="">選択してください</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->content }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- タグ -->
<div class="mb-6 flex flex-col md:flex-row md:items-start">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0 pt-2">
        タグ
    </label>
    <div class="w-full md:w-2/3 flex flex-wrap gap-4 pt-2">
        @foreach($tags as $tag)
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                    {{ is_array(old('tag_ids')) && in_array($tag->id, old('tag_ids')) ? 'checked' : '' }}
                    class="rounded text-gray-600 focus:ring-0">
                <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
            </label>
        @endforeach
    </div>
</div>

<!-- お問い合わせ内容 -->
<div class="mb-6 flex flex-col md:flex-row md:items-start">
    <label class="w-full md:w-1/3 text-gray-700 font-medium mb-2 md:mb-0 pt-2">
        お問い合わせ内容 <span class="text-red-500">※</span>
    </label>
    <div class="w-full md:w-2/3">
        <textarea name="detail" rows="5" placeholder="お問い合わせ内容をご記載ください"
            class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none">{{ old('detail') }}</textarea>
        @error('detail')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>