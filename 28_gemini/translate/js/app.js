const startButton = document.getElementById('startButton');
const resultElement = document.getElementById('result');
const statusElement = document.getElementById('status');
const fromLangSelect = document.getElementById('fromLang');
const toLangSelect = document.getElementById('toLang');
const chatHistoryElement = document.getElementById('chatHistory');
const emptyHistoryElement = document.getElementById('emptyHistory');

var historyList = [];
// SpeechRecognition APIが利用可能かチェック
SpeechRecognition = webkitSpeechRecognition || SpeechRecognition;
const recognition = new SpeechRecognition();
recognition.interimResults = false;

recognition.onstart = () => {
    statusElement.textContent = "音声認識中...";
};

recognition.onresult = (event) => {
    console.log('onresult')
    var text = event.results[0][0].transcript;
    resultElement.value = text;
    addOrigin(text);
    translate(text, fromLangSelect.value, toLangSelect.value);
}

recognition.onend = () => {
    console.log('音声認識が終了しました');
    statusElement.textContent = "";
};

recognition.onerror = (event) => {
    statusElement.textContent = `エラーが発生しました: ${event.error}`;
};

// ボタンがクリックされたとき、音声認識を開始
const startSpeech = () => {
    console.log("Lang: ", fromLangSelect.value);
    recognition.lang = fromLangSelect.value;
    recognition.start(); // 音声認識を開始
}

/**
 * 翻訳イベント
 */
const handleTranslate = () => {
    var text = resultElement.value;
    if (!text) return;

    addOrigin(text);
    // addTranslation(text);
    translate(text, fromLangSelect.value, toLangSelect.value);
}

/**
 * 翻訳
 */
const translate = async (origin, fromLang, toLang) => {
    statusElement.textContent = "翻訳中...";
    startButton.disabled = true;
    startButton.classList.add('opacity-60', 'cursor-not-allowed');

    const data = { origin, fromLang, toLang }
    try {
        // 翻訳APIのURLを設定 --- env.jsの設定 ---
        const TRANSLATION_URI = `api/translate.php`;
        // APIに翻訳リクエストを送信:POST & JSON形式
        const response = await fetch(TRANSLATION_URI, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`Network error: ${response.status}`);
        }

        const result = await response.json();
        console.log(result);

        if (result.status === 'error') {
            throw new Error(result.message || 'Translation error');
        }

        statusElement.textContent = "";
        renderTranslation(result);

    } catch (error) {
        console.error('Fetch error:', error);
        statusElement.textContent = "翻訳中にエラーが発生しました。";
    } finally {
        startButton.disabled = false;
        startButton.classList.remove('opacity-60', 'cursor-not-allowed');
    }
};

// 翻訳結果を表示
const renderTranslation = (translationData) => {
    addTranslation(translationData);
    speakTranslation(translationData.translate); // 翻訳結果を読み上げる
};

const addOrigin = (text, lang) => {
    emptyHistoryElement?.remove();

    // 翻訳前の吹き出しを作成（左側）
    const originalMessageDiv = document.createElement('div');
    originalMessageDiv.classList.add('flex', 'justify-start');

    const originalBubble = document.createElement('div');
    originalBubble.classList.add('max-w-[82%]', 'rounded-lg', 'bg-sky-600', 'p-4', 'text-left', 'leading-7', 'text-white', 'shadow-sm');
    originalBubble.textContent = text;

    originalMessageDiv.appendChild(originalBubble);
    chatHistoryElement.appendChild(originalMessageDiv);
}

// 翻訳履歴を追加
const addTranslation = (result) => {
    const translationMessageDiv = document.createElement('div');
    translationMessageDiv.classList.add('flex', 'justify-end', 'items-start', 'gap-2');

    // 吹き出し本体
    const translationBubble = document.createElement('div');
    translationBubble.classList.add('max-w-[82%]', 'rounded-lg', 'border', 'border-slate-200', 'bg-white', 'p-4', 'text-left', 'leading-7', 'text-slate-800', 'shadow-sm');
    const translationText = result.translate ? result.translate : "Translation error.";
    translationBubble.textContent = translationText;

    // 再生ボタン
    const playButton = document.createElement('button');
    playButton.textContent = '再生';
    playButton.classList.add('rounded-lg', 'border', 'border-slate-200', 'bg-white', 'px-3', 'py-2', 'text-sm', 'font-semibold', 'text-slate-700', 'shadow-sm', 'transition', 'hover:border-sky-300', 'hover:text-sky-700');
    playButton.title = '翻訳結果を読み上げ';

    // クリック時に読み上げる
    playButton.addEventListener('click', () => {
        speakTranslation(translationText);
    });

    // DOM追加
    translationMessageDiv.appendChild(translationBubble);
    translationMessageDiv.appendChild(playButton);
    chatHistoryElement.appendChild(translationMessageDiv);
};

const playText = () => {
    if (lastTranslation) {
        speakTranslation(lastTranslation); // 最後の翻訳結果を読み上げ
    } else {
        console.log('再生する翻訳結果がありません');
    }
}

// 翻訳結果を音声で読み上げ
const speakTranslation = (text) => {
    console.log('speakTranslation', text, toLangSelect.value)
    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = toLangSelect.value;
    synth.speak(utterance);

    synth.addEventListener('voiceschanged', () => {
        console.log('voice changed')
        const voice = speechSynthesis.getVoices();
    });
};

/**
 * 言語を入れ替える
 */
const swapLanguages = () => {
    const fromLang = fromLangSelect.value;
    const toLang = toLangSelect.value;

    // 入れ替える
    fromLangSelect.value = toLang;
    toLangSelect.value = fromLang;
};

/**
 * キーボード操作
 */
document.addEventListener('keydown', (event) => {
    // 音声入力
    if (event.ctrlKey && event.code === 'KeyI') {
        event.preventDefault();
        startSpeech();
    }
    // 言語を入れ替える
    if (event.ctrlKey && event.code === 'KeyL') {
        event.preventDefault();
        swapLanguages();
    }
});

startButton.addEventListener('click', handleTranslate);
