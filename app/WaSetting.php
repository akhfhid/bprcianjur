<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaSetting extends Model
{
    protected $table = 'wa_settings';

    protected $fillable = [
        'cabang_order',
        'delay_per_person',
        'delay_per_cabang',
    ];

    /**
     * Get the singleton settings row, creating it if not exists.
     */
    public static function getSetting(): self
    {
        $setting = static::first();

        if (!$setting) {
            $setting = static::create([
                'cabang_order'      => null,
                'delay_per_person'  => (int) env('WA_THROTTLE_SECONDS', 10),
                'delay_per_cabang'  => 0,
            ]);
        }

        return $setting;
    }

    /**
     * Get cabang_order as a PHP array of IDs.
     */
    public function getCabangOrderArray(): array
    {
        if (empty($this->cabang_order)) {
            return [];
        }

        $decoded = json_decode($this->cabang_order, true);

        return is_array($decoded) ? $decoded : [];
    }
}
