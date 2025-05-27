<?php

namespace App\Enums;

enum Role: string
{
    case User = 'user';
    case Moderator = 'moderator';
    case Admin = 'admin';
}
