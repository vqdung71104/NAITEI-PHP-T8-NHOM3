<?php

namespace App\Listeners;

use App\Events\OrderConfirmed as OrderConfirmedEvent;
use App\Mail\OrderConfirmed as OrderConfirmedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail 
{
    // use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderConfirmedEvent $event): void
    {
        // Gửi email xác nhận đơn hàng
        Mail::to($event->order->user->email)
            ->send(new OrderConfirmedMail($event->order));

        // Log thông tin gửi email (tùy chọn)
        \Log::info('Order confirmation email sent', [
            'order_id' => $event->order->id,
            'email' => $event->order->user->email,
            'sent_at' => now()
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(OrderConfirmedEvent $event, $exception)
    {
        \Log::error('Failed to send order confirmation email', [
            'order_id' => $event->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}