<?php

namespace App\Enums;

// by annotating type makes this enum backed
enum OrderStatus: string
{
    case WAITS_STORE_ACCEPTANCE = 'waits_store_acceptance';
    case ACCEPTED_BY_STORE = 'accepted_by_store';
    case CANCELLED_BY_STORE = 'cancelled_by_store';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELED_BY_USER = 'cancelled_by_user';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
