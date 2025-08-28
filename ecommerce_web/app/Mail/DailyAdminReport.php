<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyAdminReport extends Mailable
{
    use Queueable, SerializesModels;

    public $totalOrders;
    public $totalRevenue;
    public $reportDate;
    public $orderDetails;

    /**
     * Create a new message instance.
     */
    public function __construct($totalOrders, $totalRevenue, $reportDate, $orderDetails = [])
    {
        $this->totalOrders = $totalOrders;
        $this->totalRevenue = $totalRevenue;
        $this->reportDate = $reportDate;
        $this->orderDetails = $orderDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Báo cáo doanh thu hàng ngày - ' . $this->reportDate,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-admin-report',
            with: [
                'totalOrders' => $this->totalOrders,
                'totalRevenue' => $this->totalRevenue,
                'reportDate' => $this->reportDate,
                'orderDetails' => $this->orderDetails,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
