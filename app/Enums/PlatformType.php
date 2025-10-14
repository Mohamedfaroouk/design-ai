<?php

namespace App\Enums;

enum PlatformType: string
{
    case SALLA = 'salla';
    case ZID = 'zid';
    case WORDPRESS = 'wordpress';

    public function label(): string
    {
        return match($this) {
            self::SALLA => 'Salla',
            self::ZID => 'Zid',
            self::WORDPRESS => 'WordPress',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
