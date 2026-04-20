<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    public $timestamps = false;

    public function castValue(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            default => $this->value,
        };
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting ? $setting->castValue() : $default;
    }

    public static function putValue(string $key, mixed $value, string $type = 'string'): self
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            [
                'type' => $type,
                'value' => match ($type) {
                    'boolean' => $value ? '1' : '0',
                    default => (string) $value,
                },
            ],
        );
    }
}
