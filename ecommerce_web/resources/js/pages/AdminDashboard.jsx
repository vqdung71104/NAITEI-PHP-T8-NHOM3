import { useState } from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, router } from '@inertiajs/react';
import DashboardOverview from './DashbordOverview';
import CategoryManager from './CategoryManager';
import ProductManager from './ProductManager';
import OrderManager from './OrderManager';
import UserManager from './UserManager';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { CheckCircleIcon, XCircleIcon } from 'lucide-react';

const breadcrumbs = [
  {
    title: 'Admin Dashboard',
    href: '/admin/dashboard',
  },
];

export default function AdminDashboard({ 
  categories = [], 
  products = [], 
  orders = [], 
  statistics = {}, 
  users = [] 
}) {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [localStatistics, setLocalStatistics] = useState(statistics);
  const [localOrders, setLocalOrders] = useState(orders);
  const [processing, setProcessing] = useState({});
  const [alert, setAlert] = useState(null);

  const handleConfirmOrder = (orderId) => {
    setProcessing(prev => ({ ...prev, [orderId]: true }));
    
    router.post(`/admin/orders/${orderId}/confirm`, {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: (page) => {
        // Update local orders state
        setLocalOrders(prevOrders => 
          prevOrders.map(order => 
            order.id === orderId 
              ? { ...order, status: 'processing' }
              : order
          )
        );
        
        setAlert({
          type: 'success',
          message: `Đơn hàng #${orderId} đã được xác nhận thành công!`
        });
        setTimeout(() => setAlert(null), 5000);
      },
      onError: (errors) => {
        setAlert({
          type: 'error',
          message: 'Có lỗi xảy ra khi xác nhận đơn hàng!'
        });
        setTimeout(() => setAlert(null), 5000);
      },
      onFinish: () => {
        setProcessing(prev => ({ ...prev, [orderId]: false }));
      }
    });
  };

  return (
    <AdminLayout breadcrumbs={breadcrumbs}>
      <Head title="Admin Dashboard" />

      {/* Alert thông báo */}
      {alert && (
        <div className="fixed top-4 right-4 z-50 max-w-md mb-4">
          <Alert 
            variant={alert.type === 'error' ? 'destructive' : 'default'}
            className={`${
              alert.type === 'success' 
                ? 'border-green-200 bg-green-50 text-green-800 [&>svg]:text-green-600' 
                : ''
            } shadow-lg`}
          >
            {alert.type === 'success' ? (
              <CheckCircleIcon className="h-4 w-4" />
            ) : (
              <XCircleIcon className="h-4 w-4" />
            )}
            <AlertDescription className="flex items-center justify-between">
              <span className="pr-2">{alert.message}</span>
              <button
                onClick={() => setAlert(null)}
                className="hover:opacity-70 shrink-0 text-lg"
              >
                ×
              </button>
            </AlertDescription>
          </Alert>
        </div>
      )}

      <div className="mb-6">
        <ul className="flex border-b">
          {['dashboard', 'categories', 'products', 'orders', 'users'].map((tab) => (
            <li key={tab} className="mr-1">
              <button
                className={`bg-white inline-block py-2 px-4 font-semibold rounded-t-lg ${
                  activeTab === tab
                    ? 'border-l border-t border-r text-blue-700'
                    : 'text-blue-500 hover:text-blue-800'
                }`}
                onClick={() => setActiveTab(tab)}
              >
                {tab.charAt(0).toUpperCase() + tab.slice(1)}
              </button>
            </li>
          ))}
        </ul>
      </div>

      <div>
        {activeTab === 'dashboard' && (
          <DashboardOverview 
            statistics={localStatistics} 
            categories={categories} 
            products={products} 
            orders={localOrders} 
          />
        )}
        {activeTab === 'categories' && <CategoryManager categories={categories} />}
        {activeTab === 'products' && <ProductManager products={products} categories={categories} />}
        {activeTab === 'orders' && (
          <OrderManager 
            orders={localOrders} 
            onConfirmOrder={handleConfirmOrder}
            processing={processing}
          />
        )}
        {activeTab === 'users' && <UserManager users={users} />}
      </div>
    </AdminLayout>
  );
}