<?php
class Lang
{
    public static $languages = [
        ['key' => 'ja', 'code' => 'ja-JP', 'name' => 'Japanese'],
        ['key' => 'en', 'code' => 'en-US', 'name' => 'English'],
        ['key' => 'fr', 'code' => 'fr-FR', 'name' => 'French'],
        ['key' => 'de', 'code' => 'de-DE', 'name' => 'German'],
        ['key' => 'es', 'code' => 'es-ES', 'name' => 'Spanish'],
        ['key' => 'zh', 'code' => 'zh-CN', 'name' => 'Chinese'],
        ['key' => 'ko', 'code' => 'ko-KR', 'name' => 'Korean'],
        ['key' => 'vi', 'code' => 'vi-VN', 'name' => 'Vietnamese'],
        ['key' => 'it', 'code' => 'it-IT', 'name' => 'Italian'],
        ['key' => 'pt', 'code' => 'pt-PT', 'name' => 'Portuguese'],
        ['key' => 'ne', 'code' => 'ne-NP', 'name' => 'Nepali'],
        ['key' => 'bn', 'code' => 'bn-BD', 'name' => 'Bangla'],
    ];

    public static function getByCode(string $code): string
    {
        foreach (self::$languages as $language) {
            if ($language['code'] === $code) {
                return $language['name'];
            }
        }
        return "";
    }
}
