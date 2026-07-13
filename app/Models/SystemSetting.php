<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }
        $value = $setting->value;
        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    public static function set(string $key, mixed $value): void
    {
        $stored = is_array($value) ? json_encode($value) : $value;
        static::updateOrCreate(['key' => $key], ['value' => $stored]);
    }

    public static function getFileDataUrl(string $settingKey, string $storagePath): ?string
    {
        $filename = self::get($settingKey);
        if (! $filename || ! Storage::disk('local')->exists("{$storagePath}/{$filename}")) {
            return null;
        }
        $path = Storage::disk('local')->path("{$storagePath}/{$filename}");
        $mime = mime_content_type($path) ?: 'image/png';
        $data = file_get_contents($path);

        return 'data:'.$mime.';base64,'.base64_encode($data);
    }
}
