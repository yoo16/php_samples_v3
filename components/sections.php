<?php
// PHP Samples Project Index
// 各セクションとファイルの定義（データ化）
$sections = [
    [
        'id' => '21_composer',
        'label' => 'Composer (パッケージ管理)',
        'public' => true,
        'files' => [
            [
                'name' => 'env_sample/',
                'label' => '.env サンプル'
            ],
            [
                'name' => 'qrcode/qr_form.php',
                'label' => 'QRコード生成フォーム'
            ],
        ]
    ],
    [
        'id' => '22_mail',
        'label' => 'メール送信',
        'public' => true,
        'files' => [
            [
                'name' => 'index.php',
                'label' => 'お問い合わせフォーム'
            ],
        ]
    ],
    [
        'id' => '23_gd',
        'label' => '画像処理 (GD)',
        'public' => true,
        'files' => [
            [
                'name' => 'mosic.php',
                'label' => 'モザイク処理'
            ],
        ]
    ],
    [
        'id' => '24_sqlite',
        'label' => 'SQLite',
        'public' => true,
        'files' => [
            [
                'name' => 'index.php',
                'label' => '写真投稿アプリ (MyPix)'
            ],
        ]
    ],
    [
        'id' => '25_fabric',
        'label' => 'Fabric.js (Canvas編集)',
        'public' => true,
        'files' => [
            [
                'name' => 'flyer/editor.php',
                'label' => 'フライヤーエディタ'
            ],
        ]
    ],
    [
        'id' => '26_pdf',
        'label' => 'PDF生成',
        'public' => true,
        'files' => [
            [
                'name' => 'basic/index.php',
                'label' => 'PDF生成の基本 (mPDF / Dompdf)'
            ],
            [
                'name' => 'business_card/app.php',
                'label' => '名刺作成'
            ],
            [
                'name' => 'flyer/editor.php',
                'label' => 'フライヤーPDF'
            ],
        ]
    ],
    [
        'id' => '27_markdown',
        'label' => 'Markdown変換',
        'public' => true,
        'files' => [
            [
                'name' => 'markdown_to_html.php',
                'label' => 'MarkdownからHTMLへ変換'
            ],
        ]
    ],
    [
        'id' => '28_gemini',
        'label' => 'Gemini API (生成AI)',
        'public' => true,
        'files' => [
            [
                'name' => 'index.php',
                'label' => 'トップページ'
            ],
            [
                'name' => 'chat/',
                'label' => 'AIチャット'
            ],
            [
                'name' => 'photo/',
                'label' => '写真解析'
            ],
            [
                'name' => 'translate/',
                'label' => 'AI翻訳'
            ],
            [
                'name' => 'travel/',
                'label' => '旅行プラン生成'
            ],
        ]
    ],
    [
        'id' => '29_oauth',
        'label' => 'OAuth認証',
        'public' => true,
        'files' => [
            [
                'name' => 'login.php',
                'label' => 'ソーシャルログイン'
            ],
        ]
    ],
    [
        'id' => '30_api',
        'label' => 'Web API',
        'public' => true,
        'files' => [
            [
                'name' => 'employees/index.php',
                'label' => '社員管理API'
            ],
            [
                'name' => 'travel/index.php',
                'label' => '旅行API'
            ],
        ]
    ],
    [
        'id' => '31_travel_plan',
        'label' => '簡易旅行プランナー',
        'public' => true,
        'files' => [
            [
                'name' => 'index.php',
                'label' => '旅行プラン生成 (Gemini API)'
            ],
        ]
    ],
];
