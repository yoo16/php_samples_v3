## フォント
### config.php
```php
$config = [
    'font_dir' => __DIR__ . '/../fonts',
    'font' => [
        'noto_sans_jp' => [
            'R' => 'NotoSansJP-Regular.ttf', // 通常体のファイル名
            'B' => 'NotoSansJP-Bold.ttf', // 太字ファイルがあれば指定
            'I' => 'NotoSansJP-Regular.ttf', // 斜体ファイルがあれば指定
        ],
    ],
    'default_font' => 'noto_sans_jp',
];
```

### フォント一覧
```tree
Helvetica-Bold.afm.json                                                                  
Helvetica.afm.json                                                                       
NotoSansJP-Black.ttf                                                                     
NotoSansJP-Bold.ttf                                                                      
NotoSansJP-ExtraBold.ttf                                                                 
NotoSansJP-ExtraLight.ttf                                                                
NotoSansJP-Light.ttf                                                                     
NotoSansJP-Medium.ttf                                                                    
NotoSansJP-Regular.ttf                                                                   
NotoSansJP-SemiBold.ttf                                                                  
NotoSansJP-Thin.ttf                                                                      
Times-Bold.afm.json                                                                      
Times-Roman.afm.json 
```