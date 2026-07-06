// ============================================================
// app.js - フォーム送信 → PHP API → 結果を CSR で描画
// ============================================================

const form = document.getElementById('plan-form');
const submitBtn = document.getElementById('submit-btn');
const loading = document.getElementById('loading');
const errorBox = document.getElementById('error');
const result = document.getElementById('result');

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  // 表示をリセット
  errorBox.classList.add('hidden');
  result.classList.add('hidden');
  result.innerHTML = '';
  loading.classList.remove('hidden');
  submitBtn.disabled = true;

  // フォームの値を取得
  const payload = {
    departure_date: document.getElementById('departure_date').value,
    days: Number(document.getElementById('days').value),
    from: document.getElementById('from').value.trim(),
    to: document.getElementById('to').value.trim(),
  };

  try {
    // PHP API にリクエスト
    const res = await fetch('api/create_plan.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (!res.ok || data.error) {
      throw new Error(data.error ?? 'プランの取得に失敗しました');
    }

    renderPlan(data);
  } catch (err) {
    errorBox.textContent = err.message;
    errorBox.classList.remove('hidden');
  } finally {
    loading.classList.add('hidden');
    submitBtn.disabled = false;
  }
});

// XSS対策のエスケープ
function esc(str) {
  return String(str ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;');
}

// 旅行プランを描画する
function renderPlan(plan) {
  let html = `
    <div class="rounded-2xl bg-white p-8 shadow-sm border border-slate-200">
      <h2 class="text-2xl font-bold text-slate-900 mb-2">${esc(plan.title)}</h2>
      <p class="text-slate-600">${esc(plan.summary)}</p>
    </div>
  `;

  // 日ごとのスケジュール
  for (const day of plan.days ?? []) {
    const rows = (day.schedule ?? [])
      .map(
        (item) => `
        <li class="flex gap-4 py-3 border-b border-slate-100 last:border-0">
          <span class="w-16 shrink-0 font-mono text-sm text-blue-600 pt-0.5">${esc(item.time)}</span>
          <div>
            <p class="font-semibold text-slate-800">${esc(item.place)}</p>
            <p class="text-sm text-slate-500">${esc(item.activity)}</p>
          </div>
        </li>`
      )
      .join('');

    html += `
      <div class="rounded-2xl bg-white p-8 shadow-sm border border-slate-200">
        <div class="flex items-baseline gap-3 mb-4">
          <span class="rounded-full bg-blue-600 px-3 py-1 text-sm font-bold text-white">Day ${esc(day.day)}</span>
          <span class="text-sm text-slate-500">${esc(day.date)}</span>
          <h3 class="font-bold text-slate-800">${esc(day.theme)}</h3>
        </div>
        <ul>${rows}</ul>
      </div>
    `;
  }

  // アドバイス
  if ((plan.tips ?? []).length > 0) {
    const tips = plan.tips.map((t) => `<li class="flex gap-2"><span>💡</span><span>${esc(t)}</span></li>`).join('');
    html += `
      <div class="rounded-2xl bg-amber-50 p-8 border border-amber-200">
        <h3 class="font-bold text-amber-800 mb-3">旅のアドバイス</h3>
        <ul class="space-y-2 text-amber-900 text-sm">${tips}</ul>
      </div>
    `;
  }

  result.innerHTML = html;
  result.classList.remove('hidden');
}
