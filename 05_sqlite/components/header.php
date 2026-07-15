<header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-10">
    <div>
        <a href="index.php" class="text-2xl font-bold text-indigo-600">MyPix</a>
        <span class="ml-2 text-sm text-gray-500">みんなで写真をシェアしよう 📷</span>
    </div>
    <nav class="flex items-center space-x-4">
        <a href="index.php" class="text-gray-700 hover:text-indigo-600">ホーム</a>
        <form action="reset.php" method="post" class="inline"
            onsubmit="return confirm('DBを初期化します。すべての投稿と画像が削除されます。よろしいですか？')">
            <button type="submit" class="text-gray-500 hover:text-red-600">DB初期化</button>
        </form>
        <a href="post.php" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">投稿する</a>
    </nav>
</header>