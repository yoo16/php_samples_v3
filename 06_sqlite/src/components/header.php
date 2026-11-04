<header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-2 group">
            <span class="grid place-items-center w-9 h-9 rounded-xl bg-indigo-600 text-white shadow-sm group-hover:scale-105 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h1.5l1-2h9l1 2H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3.5"/></svg>
            </span>
            <span class="text-xl font-extrabold tracking-tight text-slate-900">MyPix</span>
            <span class="hidden md:inline ml-2 text-sm text-slate-400">みんなで写真をシェアしよう</span>
        </a>
        <nav class="flex items-center gap-1 sm:gap-2">
            <a href="index.php" class="px-3 py-2 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 hover:text-slate-900 transition">ホーム</a>
            <form action="reset.php" method="post" class="inline"
                onsubmit="return confirm('DBを初期化します。すべての投稿と画像が削除されます。よろしいですか？')">
                <button type="submit" class="px-3 py-2 text-sm font-medium text-slate-400 rounded-lg hover:bg-red-50 hover:text-red-600 transition">DB初期化</button>
            </form>
            <a href="post.php" class="ml-1 inline-flex items-center gap-1 px-4 py-2 text-sm font-semibold text-white rounded-full bg-indigo-600 hover:bg-indigo-700 shadow-sm hover:shadow-md transition">
                <span class="text-lg leading-none">＋</span>投稿する
            </a>
        </nav>
    </div>
</header>
