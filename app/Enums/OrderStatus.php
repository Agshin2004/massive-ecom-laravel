<?php

namespace App\Enums;

enum OrderStatus
{
    case WAITS_STORE_ACCEPTANCE = 'waits_store_acceptance';
    case ACCEPTED_BY_STORE = 'accepted_by_store';
    case CANCELLED_BY_STORE = 'cancelled_by_store';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
