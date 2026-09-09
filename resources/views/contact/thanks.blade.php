<x-guest-layout>
    <div class="relative min-h-[calc(100vh-80px)] flex items-center justify-center overflow-hidden bg-white">
        <!-- 背景の大きな "Thank you" 文字 -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
            <span class="text-[120px] md:text-[180px] font-serif text-[#f4efe9] font-normal leading-none tracking-wider whitespace-nowrap">
                Thank you
            </span>
        </div>

        <!-- 前面のコンテンツ -->
        <div class="relative z-10 text-center px-4">
            <h1 class="text-xl md:text-2xl font-serif text-[#8b7969] mb-8 font-medium">
                お問い合わせありがとうございました
            </h1>
            
            <a href="{{ route('contact.index') }}"
                class="inline-block px-10 py-3 bg-[#8b7969] hover:bg-[#7a6a5b] text-white text-sm font-medium rounded transition duration-200">
                HOME
            </a>
        </div>
    </div>
</x-guest-layout>