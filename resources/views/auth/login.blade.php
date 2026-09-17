<x-guest-layout>
    <div class="bg-white min-h-screen flex flex-col justify-center items-center px-6 py-12">
        <div class="w-full max-w-md bg-white border border-[#ddd8d3] rounded p-8">
            
            <!-- タイトル（Laravelロゴを排除し、アプリに合わせたデザインに変更） -->
            <h1 class="text-2xl font-serif text-[#6b5744] text-center mb-8">Login</h1>

            <!-- セッションステータス（ログイン失敗時などのメッセージ） -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <!-- メールアドレス -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 font-medium mb-2">メールアドレス</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none text-gray-700">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- パスワード -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-medium mb-2">パスワード</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-2 bg-gray-100 border border-transparent rounded focus:bg-white focus:border-gray-400 focus:outline-none text-gray-700">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- ログイン状態を保存するチェックボックス -->
                <div class="block mb-6">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded text-gray-600 focus:ring-0">
                        <span class="ml-2 text-sm text-gray-600">ログイン状態を保存する</span>
                    </label>
                </div>

                <!-- ボタンエリア -->
                <div class="flex justify-center mt-8">
                    <button type="submit"
                        class="px-16 py-3 bg-[#7d7470] hover:bg-[#6b5f57] border border-transparent rounded font-medium text-white transition w-full text-center">
                        ログイン
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
