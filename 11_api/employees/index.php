<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>社員データベース API</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50">

  <!-- ===== ヘッダー ===== -->
  <header class="bg-white border-b border-slate-200 px-6 py-4 shadow-sm">
    <div class="mx-auto max-w-5xl flex items-center gap-3">
      <span class="text-2xl">🗄️</span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-600">MySQL API</p>
        <h1 class="text-xl font-bold text-slate-900">社員データベース API</h1>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-5xl px-6 py-10 space-y-6">

    <!-- ===== データフロー説明 ===== -->
    <div class="rounded-xl bg-indigo-50 border border-indigo-200 px-6 py-4">
      <p class="text-xs font-semibold text-indigo-500 uppercase tracking-widest mb-2">データフロー</p>
      <div class="flex items-center gap-2 text-sm font-mono text-indigo-800 flex-wrap">
        <span class="bg-indigo-100 rounded px-2 py-1">MySQL DB</span>
        <span class="text-indigo-400">→</span>
        <span class="bg-indigo-100 rounded px-2 py-1">api.php (PDO)</span>
        <span class="text-indigo-400">→</span>
        <span class="bg-indigo-100 rounded px-2 py-1">json_encode()</span>
        <span class="text-indigo-400">→</span>
        <span class="bg-indigo-100 rounded px-2 py-1">fetch()</span>
        <span class="text-indigo-400">→</span>
        <span class="bg-indigo-100 rounded px-2 py-1">このページ</span>
      </div>
    </div>

    <!-- ===== APIリクエスト設定 ===== -->
    <section class="rounded-2xl bg-white p-8 shadow-sm border border-slate-200">
      <h2 class="text-lg font-bold text-slate-800 mb-6">APIリクエスト設定</h2>

      <div class="space-y-5">

        <!-- 取得データ選択 -->
        <div>
          <p class="text-sm font-semibold text-slate-700 mb-2">取得データ</p>
          <div class="flex gap-3 flex-wrap">
            <label class="type-label">
              <input type="radio" name="type" value="employees" checked class="sr-only">
              <span>👤 社員 (employees)</span>
            </label>
            <label class="type-label">
              <input type="radio" name="type" value="departments" class="sr-only">
              <span>🏢 部署 (departments)</span>
            </label>
            <label class="type-label">
              <input type="radio" name="type" value="companies" class="sr-only">
              <span>🏭 会社 (companies)</span>
            </label>
          </div>
        </div>

        <!-- 会社フィルタ -->
        <div id="filter-company">
          <label class="block text-sm font-semibold text-slate-700 mb-1" for="company_id">
            会社フィルタ <span class="text-slate-400 font-normal">（省略可）</span>
          </label>
          <select id="company_id"
            class="w-full sm:w-80 rounded-lg border border-slate-300 px-4 py-2.5 text-sm
                   focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            <option value="">全社員</option>
          </select>
        </div>

        <!-- リクエストURLプレビュー -->
        <div>
          <p class="text-sm font-semibold text-slate-700 mb-1">リクエスト URL</p>
          <div class="flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-3 font-mono text-sm">
            <span class="text-emerald-400 shrink-0">GET</span>
            <span id="url-preview" class="text-slate-100 break-all">api.php?type=employees</span>
          </div>
        </div>

        <!-- 送信ボタン -->
        <button id="btn-send"
          class="rounded-lg bg-indigo-600 px-8 py-3 text-sm font-bold text-white
                 transition hover:bg-indigo-700 active:scale-95 disabled:opacity-50">
          リクエスト送信
        </button>
      </div>
    </section>

    <!-- ===== ローディング ===== -->
    <div id="loading" class="hidden text-center py-8">
      <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-indigo-200 border-t-indigo-600"></div>
      <p class="mt-3 text-sm text-slate-500">データを取得中…</p>
    </div>

    <!-- ===== エラー ===== -->
    <div id="error" class="hidden rounded-lg bg-red-50 border border-red-200 px-6 py-4 text-sm text-red-700"></div>

    <!-- ===== 結果エリア ===== -->
    <div id="result" class="hidden space-y-6">

      <!-- テーブル -->
      <section class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
          <h2 class="font-bold text-slate-800">レスポンス結果</h2>
          <span id="result-count" class="text-sm text-slate-500"></span>
        </div>
        <div class="overflow-x-auto">
          <table id="result-table" class="w-full text-sm">
            <thead id="result-thead" class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"></thead>
            <tbody id="result-tbody" class="divide-y divide-slate-100"></tbody>
          </table>
        </div>
      </section>

      <!-- JSON表示 -->
      <section class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
          <h2 class="font-bold text-slate-800">Raw JSON レスポンス</h2>
          <p class="text-xs text-slate-400 mt-0.5">json_encode() の出力そのまま</p>
        </div>
        <pre id="json-output"
          class="overflow-x-auto p-6 text-xs leading-relaxed font-mono text-slate-700 bg-slate-900 text-slate-100 max-h-96"></pre>
      </section>
    </div>

  </main>

  <!-- ===== スタイル（ラジオボタン） ===== -->
  <style>
    .type-label span {
      display: inline-block;
      cursor: pointer;
      border-radius: 0.5rem;
      border: 2px solid #e2e8f0;
      background: #f8fafc;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #475569;
      transition: all 0.15s;
    }
    .type-label input:checked + span {
      border-color: #6366f1;
      background: #eef2ff;
      color: #4338ca;
    }
    #result-table th {
      padding: 0.625rem 1rem;
    }
    #result-table td {
      padding: 0.625rem 1rem;
      color: #334155;
      white-space: nowrap;
    }
    #result-tbody tr:hover {
      background: #f8fafc;
    }
  </style>

  <!-- ===== JavaScript ===== -->
  <script>
    // ============================================================
    // 要素
    // ============================================================
    const btnSend     = document.getElementById('btn-send');
    const loading     = document.getElementById('loading');
    const errorBox    = document.getElementById('error');
    const result      = document.getElementById('result');
    const resultCount = document.getElementById('result-count');
    const urlPreview  = document.getElementById('url-preview');
    const thead       = document.getElementById('result-thead');
    const tbody       = document.getElementById('result-tbody');
    const jsonOutput  = document.getElementById('json-output');
    const filterComp  = document.getElementById('filter-company');
    const compSel     = document.getElementById('company_id');

    // ============================================================
    // ページロード時: 会社一覧を取得してフィルタを設定
    // ============================================================
    (async () => {
      try {
        const res  = await fetch('api.php?type=companies');
        const json = await res.json();
        json.data.forEach(c => {
          const opt = document.createElement('option');
          opt.value       = c.id;
          opt.textContent = c.name;
          compSel.appendChild(opt);
        });
      } catch (_) {}
    })();

    // ============================================================
    // URL プレビューを動的に更新
    // ============================================================
    function buildUrl() {
      const type      = document.querySelector('input[name="type"]:checked').value;
      const companyId = compSel.value;

      let url = `api.php?type=${type}`;
      if (companyId && type !== 'companies') url += `&company_id=${companyId}`;
      return url;
    }

    function refreshPreview() {
      urlPreview.textContent = buildUrl();
      // 会社フィルタは companies では不要
      const type = document.querySelector('input[name="type"]:checked').value;
      filterComp.style.opacity = (type === 'companies') ? '0.4' : '1';
    }

    document.querySelectorAll('input[name="type"]').forEach(r => r.addEventListener('change', refreshPreview));
    compSel.addEventListener('change', refreshPreview);

    // ============================================================
    // リクエスト送信
    // ============================================================
    btnSend.addEventListener('click', async () => {
      const url = buildUrl();
      showLoading(true);
      hideError();
      result.classList.add('hidden');

      try {
        const res  = await fetch(url);
        const json = await res.json();

        if (json.error) { showError(json.error); return; }

        renderTable(json.data);
        resultCount.textContent = `${json.count} 件`;
        jsonOutput.textContent  = JSON.stringify(json, null, 2);
        result.classList.remove('hidden');

      } catch (e) {
        showError('通信エラーが発生しました。');
      } finally {
        showLoading(false);
      }
    });

    // ============================================================
    // テーブル描画
    // ============================================================
    const NUMBER_COLS = ['salary', 'budget'];

    function renderTable(data) {
      thead.innerHTML = '';
      tbody.innerHTML = '';

      if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="99" class="px-6 py-8 text-center text-slate-400 text-sm">データがありません</td></tr>';
        return;
      }

      const keys = Object.keys(data[0]);

      // ヘッダー
      const tr = document.createElement('tr');
      keys.forEach(k => {
        const th = document.createElement('th');
        th.textContent = k;
        tr.appendChild(th);
      });
      thead.appendChild(tr);

      // 行
      data.forEach(row => {
        const tr = document.createElement('tr');
        keys.forEach(k => {
          const td = document.createElement('td');
          td.textContent = NUMBER_COLS.includes(k)
            ? '¥' + Number(row[k]).toLocaleString('ja-JP')
            : (row[k] ?? '');
          tr.appendChild(td);
        });
        tbody.appendChild(tr);
      });
    }

    // ============================================================
    // ユーティリティ
    // ============================================================
    function showLoading(flag) {
      loading.classList.toggle('hidden', !flag);
      btnSend.disabled = flag;
    }

    function showError(msg) {
      errorBox.textContent = msg;
      errorBox.classList.remove('hidden');
    }

    function hideError() {
      errorBox.classList.add('hidden');
    }
  </script>

</body>

</html>
