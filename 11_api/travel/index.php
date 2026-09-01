<?php
require_once "env.php";
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>観光スポット紹介ガイド</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Google Maps モーダル */
    #map {
      height: 400px;
      width: 100%;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50">

  <!-- ===== ヘッダー ===== -->
  <header class="bg-white border-b border-slate-200 px-6 py-4 shadow-sm">
    <div class="mx-auto max-w-4xl flex items-center gap-3">
      <span class="text-2xl">🗺️</span>
      <div>
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Mock API</p>
        <h1 class="text-xl font-bold text-slate-900">観光スポット紹介ガイド</h1>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-4xl px-6 py-10 space-y-8">

    <!-- ===== 入力フォーム ===== -->
    <section class="rounded-2xl bg-white p-8 shadow-sm border border-slate-200">
      <h2 class="text-lg font-bold text-slate-800 mb-6">旅行先を入力してください</h2>

      <div class="grid gap-5 sm:grid-cols-2">
        <!-- 都市名 -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1" for="city">
            都市名
          </label>
          <input
            id="city"
            type="text"
            placeholder="例：京都、パリ、ニューヨーク"
            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-900
                  focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
        </div>

        <!-- 旅行スタイル -->
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1" for="style">
            旅行スタイル
          </label>
          <select
            id="style"
            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-900
                  focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            <option value="">選択してください</option>
            <option value="歴史・文化を楽しみたい">🏛️ 歴史・文化</option>
            <option value="自然・アウトドアを楽しみたい">🌿 自然・アウトドア</option>
            <option value="グルメ・食を楽しみたい">🍜 グルメ・食</option>
            <option value="ショッピングを楽しみたい">🛍️ ショッピング</option>
            <option value="アート・美術館を楽しみたい">🎨 アート・美術館</option>
            <option value="家族・子どもと楽しみたい">👨‍👩‍👧 家族・子ども向け</option>
          </select>
        </div>
      </div>

      <!-- 送信ボタン -->
      <button
        id="btn-search"
        class="mt-6 w-full rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white
              transition hover:bg-blue-700 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
        おすすめスポットを探す
      </button>
    </section>

    <!-- ===== ローディング ===== -->
    <div id="loading" class="hidden text-center py-10">
      <div class="inline-block h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"></div>
      <p class="mt-4 text-sm text-slate-500">サンプルデータから観光スポットを探しています…</p>
    </div>

    <!-- ===== エラー ===== -->
    <div id="error" class="hidden rounded-lg bg-red-50 border border-red-200 px-6 py-4 text-sm text-red-700"></div>

    <!-- ===== 結果エリア ===== -->
    <section id="result" class="hidden space-y-4">
      <h2 id="result-title" class="text-lg font-bold text-slate-800"></h2>
      <div id="cards" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>
    </section>

  </main>

  <!-- ===== Google Maps モーダル ===== -->
  <div
    id="modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden">
      <!-- モーダルヘッダー -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
        <h3 id="modal-title" class="font-bold text-slate-900 text-base"></h3>
        <button id="modal-close" class="text-slate-400 hover:text-slate-700 text-2xl leading-none">&times;</button>
      </div>
      <!-- 地図 -->
      <div id="map"></div>
      <!-- 住所 -->
      <div class="px-6 py-3 bg-slate-50 text-sm text-slate-600">
        <span class="font-semibold">住所：</span>
        <span id="modal-address"></span>
      </div>
    </div>
  </div>

  <!-- ===== JavaScript ===== -->
  <script>
    // ============================================================
    // 定数
    // ============================================================
    const MAPS_API_KEY = "<?= GOOGLE_MAPS_API_KEY ?>";

    // ============================================================
    // 要素取得
    // ============================================================
    const btnSearch = document.getElementById('btn-search');
    const loading = document.getElementById('loading');
    const errorBox = document.getElementById('error');
    const result = document.getElementById('result');
    const resultTitle = document.getElementById('result-title');
    const cards = document.getElementById('cards');
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modal-title');
    const modalAddr = document.getElementById('modal-address');
    const modalClose = document.getElementById('modal-close');

    // ============================================================
    // 検索ボタン
    // ============================================================
    btnSearch.addEventListener('click', async () => {
      const city = document.getElementById('city').value.trim();
      const style = document.getElementById('style').value;

      // --- バリデーション ---
      if (!city || !style) {
        showError('都市名と旅行スタイルを選択してください。');
        return;
      }

      // --- UI 初期化 ---
      showLoading(true);
      hideError();
      result.classList.add('hidden');

      // --- API リクエスト（FormData で POST）---
      try {
        const form = new FormData();
        form.append('city', city);
        form.append('style', style);

        const res = await fetch('api.php', {
          method: 'POST',
          body: form
        });
        const data = await res.json();

        if (!res.ok || data.error) {
          showError(data.error ?? 'エラーが発生しました。');
          return;
        }

        renderCards(data);

      } catch (e) {
        showError('通信エラーが発生しました。ページを再読み込みしてください。');
      } finally {
        showLoading(false);
      }
    });

    // ============================================================
    // カード描画
    // ============================================================
    function renderCards(data) {
      cards.innerHTML = '';
      resultTitle.textContent = `📍 ${data.city} のおすすめスポット`;

      data.spots.forEach(spot => {
        const card = document.createElement('div');
        card.className = `
          rounded-xl border border-slate-200 bg-white p-5 shadow-sm
          flex flex-col gap-3 transition hover:shadow-md hover:-translate-y-0.5
        `;
        card.innerHTML = `
          <div class="flex items-start justify-between gap-2">
            <h3 class="font-bold text-slate-900 leading-snug">${escHtml(spot.name)}</h3>
            <span class="shrink-0 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-600">
              ${escHtml(spot.category)}
            </span>
          </div>
          <p class="text-sm leading-6 text-slate-600">${escHtml(spot.description)}</p>
          <p class="text-xs text-slate-400">📍 ${escHtml(spot.address)}</p>
          <button
            class="map-btn mt-auto rounded-lg border border-blue-300 bg-blue-50 px-4 py-2
                  text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
            data-name="${escHtml(spot.name)}"
            data-address="${escHtml(spot.address)}"
          >
            🗺️ 地図を見る
          </button>
        `;
        cards.appendChild(card);
      });

      // 地図ボタンにイベント設定
      document.querySelectorAll('.map-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          openMap(btn.dataset.name, btn.dataset.address);
        });
      });

      result.classList.remove('hidden');
    }

    // ============================================================
    // Google Maps モーダル
    // ============================================================
    let map = null;

    // Google Maps API を動的に読み込む
    function loadMapsAPI() {
      return new Promise(resolve => {
        if (window.google?.maps) {
          resolve();
          return;
        }
        window.__mapsReady = resolve;
        const s = document.createElement('script');
        s.src = `https://maps.googleapis.com/maps/api/js?key=${MAPS_API_KEY}&callback=__mapsReady`;
        s.async = true;
        document.head.appendChild(s);
      });
    }

    async function openMap(name, address) {
      modalTitle.textContent = name;
      modalAddr.textContent = address;
      modal.classList.remove('hidden');
      modal.classList.add('flex');

      await loadMapsAPI();

      // Geocoding API で住所 → 座標変換
      const geocoder = new google.maps.Geocoder();
      geocoder.geocode({
        address
      }, (results, status) => {
        if (status !== 'OK' || !results[0]) {
          document.getElementById('map').innerHTML =
            '<p class="p-6 text-sm text-slate-500">地図を表示できませんでした。</p>';
          return;
        }
        const location = results[0].geometry.location;

        // 地図を初期化（再利用）
        if (!map) {
          map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: location,
          });
        } else {
          map.setCenter(location);
          map.setZoom(15);
        }

        // マーカーを設置
        new google.maps.Marker({
          position: location,
          map,
          title: name
        });
      });
    }

    // モーダルを閉じる
    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', e => {
      if (e.target === modal) closeModal();
    });

    function closeModal() {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    // ============================================================
    // ユーティリティ
    // ============================================================
    function showLoading(flag) {
      loading.classList.toggle('hidden', !flag);
      btnSearch.disabled = flag;
    }

    function showError(msg) {
      errorBox.textContent = msg;
      errorBox.classList.remove('hidden');
    }

    function hideError() {
      errorBox.classList.add('hidden');
    }

    function escHtml(str) {
      return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
  </script>

</body>

</html>
