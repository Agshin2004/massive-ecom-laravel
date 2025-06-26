<?php

namespace App\Enums;

enum Role: string
{
    case User = 'user';
    case Seller = 'seller';
    case Admin = 'admin';

    public static function values(): array
    {
        // rReturn an array of all Role enum string values (['user', 'seller', 'admin'])
        // self::cases() return object with enum case name and value ([name => User, value => user])
        // getting value data from value and returning it
        return array_column(self::cases(), 'value');
    }
}
