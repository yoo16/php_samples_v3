<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>簡易旅行プランナー</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50">

  <!-- ===== ヘッダー ===== -->
  <header class="bg-white border-b border-slate-200 px-6 py-4 shadow-sm">
    <div class="mx-auto max-w-4xl flex items-center gap-3">
      <span class="text-2xl">✈️</span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Gemini API</p>
        <h1 class="text-xl font-bold text-slate-900">簡易旅行プランナー</h1>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-4xl px-6 py-10 space-y-8">

    <!-- ===== 入力フォーム ===== -->
    <section class="rounded-2xl bg-white p-8 shadow-sm border border-slate-200">
      <h2 class="text-lg font-bold text-slate-800 mb-6">旅行の条件を入力してください</h2>

      <form id="plan-form" class="grid gap-5 sm:grid-cols-2">
        <!-- 出発日 -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1" for="departure_date">出発日</label>
          <input id="departure_date" name="departure_date" type="date" required
            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>

        <!-- 日数 -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1" for="days">日数</label>
          <select id="days" name="days"
            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
            <?php for ($i = 1; $i <= 7; $i++): ?>
              <option value="<?= $i ?>"><?= $i ?>日間</option>
            <?php endfor; ?>
          </select>
        </div>

        <!-- 出発場所 -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1" for="from">出発場所</label>
          <input id="from" name="from" type="text" required placeholder="例：東京"
            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>

        <!-- 到着場所 -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1" for="to">到着場所</label>
          <input id="to" name="to" type="text" required placeholder="例：京都"
            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>

        <!-- 送信ボタン -->
        <div class="sm:col-span-2">
          <button id="submit-btn" type="submit"
            class="w-full rounded-lg bg-blue-600 px-6 py-3 font-bold text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
            プランを作成する
          </button>
        </div>
      </form>
    </section>

    <!-- ===== ローディング ===== -->
    <div id="loading" class="hidden text-center py-8">
      <p class="text-slate-500 animate-pulse">✨ Gemini がプランを作成中です…（30秒ほどかかる場合があります）</p>
    </div>

    <!-- ===== エラー表示 ===== -->
    <div id="error" class="hidden rounded-lg bg-red-50 border border-red-200 px-6 py-4 text-red-700"></div>

    <!-- ===== 結果表示（JSでレンダリング） ===== -->
    <section id="result" class="hidden space-y-6"></section>

  </main>

  <script src="js/app.js"></script>
</body>

</html>
