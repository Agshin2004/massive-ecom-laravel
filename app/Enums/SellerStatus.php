<?php

namespace App\Enums;

enum SellerStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromValue($value)
    {
        return match ($value) {
            self::Pending->value => self::Pending,
            self::Approved->value => self::Approved,
            self::Rejected->value => self::Rejected,
            default => throw new \InvalidArgumentException("Invalid seller status: {$value}"),
        };
    }
}
