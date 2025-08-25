import { useState } from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head } from '@inertiajs/react';
import DashboardOverview from './DashbordOverview';
import CategoryManager from './CategoryManager';
import ProductManager from './ProductManager';
import OrderManager from './OrderManager';
import UserManager from './UserManager';

const breadcrumbs = [
  {
    title: 'Admin Dashboard',
    href: '/admin/dashboard',
  },
];

export default function AdminDashboard({ categories = [], products = [], orders = [], statistics = {}, users = [] }) {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [localStatistics, setLocalStatistics] = useState(statistics);

  return (
    <AdminLayout breadcrumbs={breadcrumbs}>
      <Head title="Admin Dashboard" />

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
          <DashboardOverview statistics={localStatistics} categories={categories} products={products} orders={orders} />
        )}
        {activeTab === 'categories' && <CategoryManager categories={categories} />}
        {activeTab === 'products' && <ProductManager products={products} categories={categories} />}
        {activeTab === 'orders' && <OrderManager orders={orders} />}
        {activeTab === 'users' && <UserManager users={users} />}
      </div>
    </AdminLayout>
  );
}
