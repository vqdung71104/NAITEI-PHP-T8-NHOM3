<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Services\ShippingFeeService;

class ShippingController extends Controller
{
    protected $shippingFeeService;

    public function __construct(ShippingFeeService $shippingFeeService)
    {
        $this->shippingFeeService = $shippingFeeService;
    }

    public function calculate(Request $request)
    {
        $orderAmount = $request->order_amount ?? 0;

        // Nếu chọn địa chỉ cũ
        if ($request->has('address_id')) {
            $address = Address::findOrFail($request->address_id);
            $shipping = $this->shippingFeeService->calculate($orderAmount, $address);
            return response()->json(['shipping' => $shipping]);
        }

        // Nếu nhập địa chỉ mới
        if ($request->has('address')) {
            $data = $request->address;
            $address = new Address([
                'country' => $data['country'] ?? 'Vietnam',
                'city' => $data['city'] ?? '',
            ]);
            $shipping = $this->shippingFeeService->calculate($orderAmount, $address);
            return response()->json(['shipping' => $shipping]);
        }

        return response()->json(['shipping' => 0]);
    }
}
