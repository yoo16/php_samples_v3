<?php
require_once 'components/sections.php';
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP サンプル ダッシュボード</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="antialiased min-h-screen bg-slate-50 text-slate-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <header class="text-center mb-16">
            <div class="inline-block px-4 py-1.5 mb-4 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-widest">
                PHP 8.x 学習用サンプル
            </div>
            <h1 class="font-outfit text-5xl font-extrabold mb-4 tracking-tight text-slate-900">
                PHP Samples <span class="text-indigo-600">v3</span>
            </h1>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg leading-relaxed">
                PHPの基本文法からモダンな開発手法を学ぶための実践的なコード集です。
            </p>
        </header>

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($sections as $section): ?>
                <?php if (!$section['public']) continue; ?>
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 font-bold font-outfit text-lg">
                            <?= substr($section['id'], 0, 2) ?>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900 leading-tight">
                                <?= $section['label'] ?>
                            </h2>
                            <p class="text-slate-400 text-xs uppercase tracking-widest mt-0.5">
                                <?= $section['id'] ?>
                            </p>
                        </div>
                    </div>

                    <ul class="space-y-3">
                        <?php if (empty($section['files'])): ?>
                            <li class="text-slate-300 text-sm italic">サンプルファイルが見つかりません。</li>
                        <?php else: ?>
                            <?php foreach ($section['files'] as $file): ?>
                                <li class="group/item">
                                    <div class="flex flex-col gap-1.5 p-3 rounded-xl bg-slate-50 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-all">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-slate-700 group-hover/item:text-indigo-700 truncate">
                                                <?= $file['label'] ?>
                                            </span>
                                            <a href="<?= $section['id'] . '/' . $file['name'] ?>"
                                                target="_blank"
                                                class="text-xs bg-white px-2 py-1 rounded shadow-sm border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors font-semibold">
                                                デモ
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer -->
        <footer class="mt-20 pt-8 border-t border-slate-200 text-center">
            <p class="text-slate-400 text-sm font-medium">
                &copy; 2026 PHP Samples Learning Project. PHP & Tailwind CSS で構築。
            </p>
        </footer>
    </div>
</body>

</html>