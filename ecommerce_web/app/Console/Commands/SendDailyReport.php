<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyAdminReport;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily {--date= : Ngày cụ thể để tạo báo cáo (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi báo cáo doanh thu hàng ngày cho admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateOption = $this->option('date');
        if ($dateOption) {
            try {
                $targetDate = Carbon::createFromFormat('Y-m-d', $dateOption);
            } catch (\Exception $e) {
                $this->error('Định dạng ngày không hợp lệ. Vui lòng sử dụng Y-m-d (ví dụ: 2025-08-27)');
                return 1;
            }
        } else {
            $targetDate = Carbon::yesterday();
        }
        
        $startOfDay = $targetDate->copy()->startOfDay();
        $endOfDay = $targetDate->copy()->endOfDay();
        
        $this->info("Đang tạo báo cáo cho ngày: {$targetDate->format('d/m/Y')}");
        
        $orders = Order::whereBetween('created_at', [$startOfDay, $endOfDay])
                      ->where('status', '!=', 'cancelled')
                      ->get();
        
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        
        $orderDetails = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'created_at' => $order->created_at->format('H:i:s'),
                'customer_name' => $order->user->name ?? 'N/A'
            ];
        });

        $admins = User::where('role', 'admin')->get();
        
        if ($admins->isEmpty()) {
            $this->error('Không tìm thấy admin để gửi báo cáo');
            return 1;
        }
        
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(
                    new DailyAdminReport(
                        $totalOrders,
                        $totalRevenue,
                        $targetDate->format('d/m/Y'),
                        $orderDetails
                    )
                );
                $this->info("Đã gửi báo cáo cho admin: {$admin->email}");
            } catch (\Exception $e) {
                $this->error("Lỗi khi gửi email cho {$admin->email}: " . $e->getMessage());
            }
        }
        
        $this->info("Hoàn thành gửi báo cáo ngày {$targetDate->format('d/m/Y')}");
        $this->info("Tổng đơn hàng: {$totalOrders}");
        $this->info("Tổng doanh thu: " . number_format($totalRevenue) . " VNĐ");
    }
}
