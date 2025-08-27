<?php

namespace App\Services;

use App\Models\Address;

class ShippingFeeService
{
    public function calculate(float $orderAmount, Address $address): int
    {
        // Free ship nếu giá trị đơn hàng > 1.000.000
        if ($orderAmount > 1000000) {
            return 0;
        }

        // Nếu country là Việt Nam
        if (strtolower($address->country) === 'việt nam' || strtolower($address->country) === 'viet nam' || strtolower($address->country) === 'vietnam') {
            // Nếu city là Hà Nội
            if (strtolower($address->city) === 'hà nội' || strtolower($address->city) === 'ha noi' || strtolower($address->city) === 'hanoi') {
                return 30000;
            }
            return 40000;
        }

        // Ngoài Việt Nam
        return 100000;
    }
}
