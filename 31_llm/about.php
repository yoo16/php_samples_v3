<?php
$google_ai_studio_url = "https://aistudio.google.com/";
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama API ガイド</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 p-6">
    <main class="mx-auto max-w-5xl space-y-6">

        <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-sky-600">Gemini API</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">What's Photo</h1>
            </div>
            <nav class="flex gap-3 text-sm font-semibold">
                <a href="index.php" class="px-4 py-2 text-sky-700 hover:border-sky-300 hover:text-sky-700">Top</a>
                <a href="about.php" class="px-4 py-2 text-sky-700 hover:border-sky-300 hover:text-sky-700">About</a>
                <a href="chat.php" class="px-4 py-2 text-sky-700 hover:border-sky-300 hover:text-sky-700">Chat</a>
                <a href="whats_photo.php" class="px-4 py-2 text-sky-700 hover:border-sky-300 hover:text-sky-700">What's Photo</a>
                <a href="translate.php" class="px-4 py-2 text-sky-700 hover:border-sky-300 hover:text-sky-700">Translate</a>
                <a href="https://aistudio.google.com/" target="_blank" rel="noopener noreferrer" class="rounded-lg bg-sky-600 px-4 py-2 text-white hover:bg-sky-700">AI Studio を開く</a>
            </nav>
        </header>

        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-xl font-semibold text-slate-900">Gemini API とは</h2>
            <p class="mt-3 leading-7 text-slate-600">
                Gemini API は、Google が開発した生成 AI モデル「Gemini」を自分のアプリケーションから利用するためのインターフェースです。<br>
                HTTP リクエストまたは各言語の SDK を通じて Gemini モデルを呼び出し、テキスト生成・画像解析・会話など様々なタスクを実行できます。
            </p>
            <div class="mt-5 rounded-lg border border-sky-100 bg-sky-50 p-4 text-sm leading-7 text-sky-900">
                <span class="font-semibold">利用できる主なモデル：</span> Gemini 2.0 Flash（高速・無料枠あり）、Gemini 1.5 Pro（高精度）など。
                Google AI Studio の無料枠で今すぐ試せます。
            </div>
        </section>

        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-xl font-semibold text-slate-900">Gemini API の利用法</h2>
            <p class="mt-3 leading-7 text-slate-600">大きく 3 つのステップで利用を開始できます。</p>
            <ol class="mt-5 space-y-4">
                <li class="flex gap-4 rounded-lg border border-slate-200 p-5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">1</span>
                    <div>
                        <h3 class="font-semibold text-slate-900">API キーを取得する</h3>
                        <p class="mt-2 leading-7 text-slate-600">
                            <a href="<?= $google_ai_studio_url; ?>" class="text-sky-600">Google AI Studio</a>
                            にログインして API キーを発行します。このページ下部の「API キー作成手順」に詳しい手順を掲載しています。
                        </p>
                    </div>
                </li>
                <li class="flex gap-4 rounded-lg border border-slate-200 p-5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">2</span>
                    <div>
                        <h3 class="font-semibold text-slate-900">API キーをアプリに設定する</h3>
                        <p class="mt-2 leading-7 text-slate-600">
                            取得した API キーをアプリケーションの設定ファイルに記述します。このプロジェクトでは <code class="rounded bg-slate-100 px-1 py-0.5 font-mono text-sm">env.php</code> に設定します。
                        </p>
                        <div class="mt-3 rounded-lg bg-slate-900 p-4 text-sm text-slate-100">
                            <code>const GEMINI_API_KEY = '取得した API キーを貼り付ける';</code>
                        </div>
                    </div>
                </li>
                <li class="flex gap-4 rounded-lg border border-slate-200 p-5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">3</span>
                    <div>
                        <h3 class="font-semibold text-slate-900">SDK または REST API でリクエストを送る</h3>
                        <p class="mt-2 leading-7 text-slate-600">
                            Python / Node.js / Go など各言語向けの公式 SDK、または HTTP リクエスト（REST API）で Gemini を呼び出せます。
                            PHP 向けの SDK は現時点では提供されていないため、HTTP クライアントを使って REST API を呼び出す形になります。
                        </p>
                        <div class="mt-3 grid gap-2 sm:grid-cols-3">
                            <div class="rounded border border-slate-200 bg-slate-50 p-3 text-center text-sm font-semibold text-slate-700">Node.js SDK</div>
                            <div class="rounded border border-slate-200 bg-slate-50 p-3 text-center text-sm font-semibold text-slate-700">Python SDK</div>
                            <div class="rounded border border-slate-200 bg-slate-50 p-3 text-center text-sm font-semibold text-slate-700">REST API</div>
                        </div>
                        <p class="mt-3 leading-7 text-slate-600">
                            リクエストを送ると Gemini からテキスト等のレスポンスが返ってくるので、それをアプリ側で受け取って表示・処理します。
                        </p>
                    </div>
                </li>
            </ol>
        </section>

        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-xl font-semibold text-slate-900">API キー作成手順</h2>
            <p class="mt-2 text-slate-600">Google AI Studio のアカウント準備から、プロジェクト作成、API キー作成までの流れです。</p>
        </section>

        <section class="rounded-lg border border-amber-200 bg-amber-50 p-6">
            <div class="text-sm text-amber-900">
                Gemini API の無料枠は利用制限があり、利用できなくなる可能性もあります。<br>
                再利用するには時間を空けるか、Google AI Studio でクレジットカードを登録して無料枠を拡張してください。
            </div>
        </section>

        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-xl font-semibold text-slate-900">事前準備</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-sky-600">1</p>
                    <h3 class="mt-1 font-semibold text-slate-900">Google アカウント</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        <a href="<?= $google_ai_studio_url; ?>" class="text-sky-600">Google AI Studio</a>
                        にログインするための Google アカウントを用意します。
                    </p>
                </div>
                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-sky-600">2</p>
                    <h3 class="mt-1 font-semibold text-slate-900">利用規約の確認</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">初回利用時は Google AI Studio の利用規約に同意します。</p>
                </div>
                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-sky-600">3</p>
                    <h3 class="mt-1 font-semibold text-slate-900">API キーの保管場所</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">作成したキーを貼り付ける `env.php` など、秘密情報を管理する場所を決めておきます。</p>
                </div>
            </div>
        </section>

        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-xl font-semibold text-slate-900">作成手順</h2>
            <ol class="mt-5 space-y-5">
                <li class="rounded-lg border border-slate-200 p-5">
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">1</span>
                        <div>
                            <h3 class="font-semibold text-slate-900">Google AI Studio にアクセス</h3>
                            <p class="mt-2 leading-7 text-slate-600">
                                ブラウザで
                                <a href="https://aistudio.google.com/" target="_blank" rel="noopener noreferrer" class="font-semibold text-sky-600 underline">Google AI Studio</a>
                                を開き、Get Started から Google アカウントでログインします。初回は画面の案内に従って利用規約に同意します。
                            </p>
                            <p>
                                <img src="images/google_ai_studio_site_top.png" alt="">
                            </p>
                        </div>
                    </div>
                </li>

                <li class="rounded-lg border border-slate-200 p-5">
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">2</span>
                        <div>
                            <h3 class="font-semibold text-slate-900">API キー画面を開く</h3>
                            <p class="mt-2 leading-7 text-slate-600">
                                左側メニューまたは上部メニューから API キー画面を開きます。直接開く場合は
                                <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer" class="font-semibold text-sky-600 underline">Get API Key</a>
                                にアクセスします。
                            </p>
                            <p>
                                <img src="images/google_ai_studio_menu_api_key.png" width="200">
                            </p>
                        </div>
                    </div>
                </li>

                <li class="rounded-lg border border-slate-200 p-5">
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">3</span>
                        <div>
                            <h3 class="font-semibold text-slate-900">API キー作成</h3>
                            <p class="mt-2 leading-7 text-slate-600">
                                「API Keyを作成」をクリックします。
                            </p>
                            <p>
                                <img src="images/google_ai_studio_api_key_1.png" width="600">
                            </p>
                        </div>
                    </div>
                </li>

                <li class="rounded-lg border border-slate-200 p-5">
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">4</span>
                        <div>
                            <h3 class="font-semibold text-slate-900">プロジェクトを作成または選択</h3>
                            <p class="mt-2 leading-7 text-slate-600">
                                API キー作成時に Google Cloud プロジェクトを選択します。既存プロジェクトを使うか、新しいプロジェクトを作成します。
                            </p>
                            <p class="mt-2">
                                <img src="images/google_ai_studio_api_key_2.png" width="400">
                            </p>
                            <p class="mt-2 leading-7 text-slate-600">
                                新規プロジェクトの場合は、プロジェクト名を入力して「作成」をクリックします。
                            </p>
                            <p class="mt-2">
                                <img src="images/google_ai_studio_api_key_3.png" width="400">
                            </p>
                        </div>
                    </div>
                </li>

                <li class="rounded-lg border border-slate-200 p-5">
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">5</span>
                        <div>
                            <h3 class="font-semibold text-slate-900">API キーを作成</h3>
                            <p class="mt-2 leading-7 text-slate-600">
                                「キーを作成」をクリックし、キーを作成します。作成後に表示されるキー文字列をコピーします。
                            </p>
                            <p class="mt-2">
                                <img src="images/google_ai_studio_api_key_4.png" width="400">
                            </p>
                        </div>
                    </div>
                </li>

                <li class="rounded-lg border border-slate-200 p-5">
                    <div class="flex gap-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-600 text-sm font-bold text-white">6</span>
                        <div>
                            <h3 class="font-semibold text-slate-900">プロジェクトに設定</h3>
                            <p class="mt-2 leading-7 text-slate-600">
                                このアプリでは `gemini_api/env.php` に API キーを設定します。
                            </p>
                            <div class="mt-4 rounded-lg bg-slate-900 p-4 text-sm text-slate-100">
                                <code>
                                    const GEMINI_API_KEY = 'ここにコピーした API キーを貼り付けます';
                                </code>
                            </div>
                        </div>
                    </div>
                </li>
            </ol>
        </section>

        <section class="rounded-lg border border-amber-200 bg-amber-50 p-6">
            <h2 class="text-lg font-semibold text-amber-900">注意点</h2>
            <ul class="mt-3 list-disc space-y-2 pl-5 leading-7 text-amber-900">
                <li>API キーはパスワードと同じように扱い、GitHub などへ公開しないでください。</li>
                <li>キーが漏れた可能性がある場合は、Google AI Studio または Google Cloud 側でキーを削除・再作成します。</li>
                <li>本番利用では、必要に応じて請求設定、利用上限、API キー制限を確認してください。</li>
            </ul>
        </section>

        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold text-slate-900">参考リンク</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <a href="https://ai.google.dev/gemini-api/docs/api-key?hl=ja" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-slate-200 p-4 text-sm font-semibold text-sky-600 hover:border-sky-300 hover:bg-sky-50">Gemini API キーの公式ドキュメント</a>
                <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-slate-200 p-4 text-sm font-semibold text-sky-600 hover:border-sky-300 hover:bg-sky-50">Google AI Studio API keys</a>
            </div>
        </section>
    </main>
</body>

</html>