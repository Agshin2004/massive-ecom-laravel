<?php

namespace App\DTOs;

use Date;

class PromoDTO
{
    public function __construct(
        public string $code,
        public int $discountPercent,
        public int $discountAmount,
        public Date $validTill,
        public int $usageLimit,
        public int $perUsageLimit,
        public bool $isActive
    ) {}
}
