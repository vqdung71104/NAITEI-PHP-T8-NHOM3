import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { Link } from '@inertiajs/react'; // Import Link từ Inertia
import { useEffect, useState, useMemo } from 'react';
import dayjs from 'dayjs';

export default function AdminLayout({ children, breadcrumbs, ...props }) {
  const [hasNew, setHasNew] = useState(false);
  // THAY ĐỔI: Thay vì latestOrder (object), dùng newOrders (array) để lưu 20 đơn mới nhất
  const [newOrders, setNewOrders] = useState([]);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    if (!window?.Echo) return;

    const channel = window.Echo.private('admin.notifications')
      .listen('.OrderCreated', (e) => {
        // e.order từ Event hoặc e.notification.data nếu dùng Notification class
        const data = e?.order ?? e?.notification?.data ?? null;
        if (data) {
          // THAY ĐỔI: Thêm data vào đầu mảng, tránh duplicate bằng cách check ID
          setNewOrders((prevOrders) => {
            if (prevOrders.some((order) => order.id === data.id)) {
              return prevOrders; // Đã tồn tại, không thêm
            }
            const updated = [data, ...prevOrders].slice(0, 20); // Giữ chỉ 20 cái mới nhất
            return updated;
          });
          setHasNew(true);
        }
      });

    return () => {
      try { channel?.stopListening('.OrderCreated'); } catch {}
    };
  }, []);

  return (
    <AppLayoutTemplate
      breadcrumbs={breadcrumbs}
      {...props}
      sidebarItems={[]}
      hideBranding={true}
    >
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div className="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center py-4">
              <div className="flex items-center">
                <button className="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                  </svg>
                </button>
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                  Admin Dashboard
                </h1>
              </div>

              <div className="flex items-center space-x-4">
                <span className="text-sm text-gray-500 dark:text-gray-400">
                  Welcome, Admin
                </span>

                {/* Notification button (ở giữa Welcome và View Site) */}
                <div className="relative">
                  <button
                    onClick={() => { setOpen((v) => !v); setHasNew(false); }}
                    className={`text-sm px-3 py-1 rounded-md border transition
                      ${hasNew
                        ? 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-700'
                        : 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600'
                      }`}
                  >
                    Notification {hasNew ? '•' : ''}
                  </button>

                  {/* Dropdown thông báo đơn mới */}
                  {open && (
                    <div className="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg p-4 z-50 max-h-96 overflow-y-auto">
                      <div className="font-semibold mb-2 text-gray-900 dark:text-gray-100">NEW ORDERS</div>
                      {/* THAY ĐỔI: Map qua mảng newOrders để hiển thị list */}
                      {newOrders.map((order, index) => (
                        <div key={order.id} className={`text-sm text-gray-700 dark:text-gray-300 space-y-1 ${index < newOrders.length - 1 ? 'border-b border-gray-200 dark:border-gray-700 pb-2 mb-2' : ''}`}>
                          <div><span className="font-medium">ID:</span> {order.id}</div>
                          <div><span className="font-medium">Total price:</span> {order.total_price}</div>
                          <div><span className="font-medium">Status:</span> {order.status}</div>
                          <div>
                            <span className="font-medium">Created at:</span>{' '}
                            {dayjs(order.created_at).format('DD/MM/YYYY HH:mm:ss')}
                          </div>
                        </div>
                      ))}
                      <div className="mt-3 text-right">
                        <button
                          onClick={() => setOpen(false)}
                          className="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                          Close
                        </button>
                      </div>
                    </div>
                  )}
                </div>

                <a
                  href={route('home')}
                  className="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  View Site
                </a>

                <Link
                  href={route('logout')}
                  method="post"
                  as="button"
                  className="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                >
                  Logout
                </Link>
              </div>
            </div>
          </div>
        </div>

        <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
          {children}
        </main>
      </div>
    </AppLayoutTemplate>
  );
}