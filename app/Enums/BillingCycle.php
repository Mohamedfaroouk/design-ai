<?php

namespace App\Enums;

enum BillingCycle: string
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
    case LIFETIME = 'lifetime';

    public function label(): string
    {
        return match($this) {
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
            self::LIFETIME => 'Lifetime',
        };
    }

    public function months(): int
    {
        return match($this) {
            self::MONTHLY => 1,
            self::YEARLY => 12,
            self::LIFETIME => 9999,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
