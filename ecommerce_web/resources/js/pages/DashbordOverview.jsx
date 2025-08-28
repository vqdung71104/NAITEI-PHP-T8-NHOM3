import { Card, CardContent } from "@/components/ui/card";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid, Legend, PieChart, Pie, Cell } from 'recharts';
import { useTranslation } from 'react-i18next';

export default function DashboardOverview({ categories, products, orders, statistics }) {
  const { t } = useTranslation();
  
  // Thống kê categories
  const categoryData = categories.map((c) => ({
    name: c.name,
    productCount: products.filter((p) => p.category_id === c.id).length,
  }));

  // Thống kê trạng thái orders
  const orderStatusData = [
    { name: 'Pending', value: orders.filter((o) => o.status === 'pending').length },
    { name: 'Processing', value: orders.filter((o) => o.status === 'processing').length },
    { name: 'Completed', value: orders.filter((o) => o.status === 'completed').length },
    { name: 'Cancelled', value: orders.filter((o) => o.status === 'cancelled').length },
  ];

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042'];

  return (
    // <div className="space-y-6">
    //   <div className="p-4">
    //     <h2 className="text-2xl font-bold mb-6 dark:text-white">Dashboard Overview</h2>
   
    //     {/* Cards thống kê chính */}
    //     <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    //         <h3 className="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">Tổng số đơn hàng</h3>
    //         <p className="text-3xl font-bold text-blue-600 dark:text-blue-400">{statistics.total_orders || 0}</p>
    //         <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
    //           {statistics.recent_orders || 0} đơn trong 7 ngày qua
    //         </p>
    //       </div>
   
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    //         <h3 className="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">Tổng doanh thu</h3>
    //         <p className="text-3xl font-bold text-green-600 dark:text-green-400">
    //           {(statistics.total_revenue || 0).toLocaleString('vi-VN')} ₫
    //         </p>
    //       </div>
   
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    //         <h3 className="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">Tổng sản phẩm</h3>
    //         <p className="text-3xl font-bold text-purple-600 dark:text-purple-400">{statistics.total_products || 0}</p>
    //         <p className="text-sm text-red-500 dark:text-red-400 mt-1">
    //           {statistics.low_stock_products || 0} sản phẩm sắp hết hàng
    //         </p>
    //       </div>

    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    //         <h3 className="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">Danh mục</h3>
    //         <p className="text-3xl font-bold text-orange-600 dark:text-orange-400">{statistics.total_categories || 0}</p>
    //       </div>
    //     </div>

    //     {/* Cards thống kê đơn hàng theo trạng thái */}
    //     <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
    //         <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">Đang chờ</h4>
    //         <p className="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{statistics.pending_orders || 0}</p>
    //       </div>
         
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
    //         <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">Đang xử lý</h4>
    //         <p className="text-2xl font-bold text-blue-600 dark:text-blue-400">{statistics.processing_orders || 0}</p>
    //       </div>
         
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
    //         <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">Hoàn thành</h4>
    //         <p className="text-2xl font-bold text-green-600 dark:text-green-400">{statistics.completed_orders || 0}</p>
    //       </div>
         
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
    //         <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">Đã hủy</h4>
    //         <p className="text-2xl font-bold text-red-600 dark:text-red-400">{statistics.cancelled_orders || 0}</p>
    //       </div>

    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
    //         <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">Trả hàng</h4>
    //         <p className="text-2xl font-bold text-purple-600 dark:text-purple-400">{statistics.return_orders || 0}</p>
    //       </div>
    //     </div>

    //     {/* Biểu đồ và thống kê chi tiết */}
    //     <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    //       {/* Biểu đồ trạng thái đơn hàng */}
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    //         <h3 className="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Thống kê đơn hàng theo trạng thái</h3>
    //         <ResponsiveContainer width="100%" height={300}>
    //           <BarChart data={[
    //             { name: "Pending", value: statistics.pending_orders || 0, fill: "#EAB308" },
    //             { name: "Processing", value: statistics.processing_orders || 0, fill: "#3B82F6" },
    //             { name: "Completed", value: statistics.completed_orders || 0, fill: "#10B981" },
    //             { name: "Cancelled", value: statistics.cancelled_orders || 0, fill: "#EF4444" },
    //             { name: "Return", value: statistics.return_orders || 0, fill: "#8B5CF6" }
    //           ]}>
    //             <CartesianGrid strokeDasharray="3 3" />
    //             <XAxis dataKey="name" />
    //             <YAxis allowDecimals={false} />
    //             <Tooltip />
    //             <Legend />
    //             <Bar dataKey="value" name="Số lượng" />
    //           </BarChart>
    //         </ResponsiveContainer>
    //       </div>

    //       {/* Top danh mục bán chạy */}
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    //         <h3 className="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Top danh mục bán chạy</h3>
    //         <div className="space-y-3">
    //           {(statistics.category_stats || []).slice(0, 5).map((category, index) => (
    //             <div key={category.id} className="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
    //               <span className="font-medium text-gray-700 dark:text-gray-300">
    //                 #{index + 1} {category.name}
    //               </span>
    //             </div>
    //           ))}
    //         </div>
    //       </div>
    //     </div>

    //     {/* Doanh thu theo tháng */}
    //     {statistics.monthly_revenue && statistics.monthly_revenue.length > 0 && (
    //       <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    //         <h3 className="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Doanh thu 12 tháng gần đây</h3>
    //         <ResponsiveContainer width="100%" height={400}>
    //           <BarChart data={statistics.monthly_revenue}>
    //             <CartesianGrid strokeDasharray="3 3" />
    //             <XAxis dataKey="month" />
    //             <YAxis />
    //             <Tooltip
    //               formatter={(value) => [(value || 0).toLocaleString('vi-VN') + ' ₫', 'Doanh thu']}
    //             />
    //             <Legend />
    //             <Bar dataKey="revenue" fill="#10B981" name="Doanh thu (₫)" />
    //           </BarChart>
    //         </ResponsiveContainer>
    //       </div>
    //     )}

    //     {/* Biểu đồ số lượng sản phẩm trong từng Category và Order Status */}
    //     <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
    //       <Card>
    //         <CardContent>
    //           <h2 className="text-lg font-semibold mb-4">Products per Category</h2>
    //           <ResponsiveContainer width="100%" height={300}>
    //             <BarChart data={categoryData}>
    //               <CartesianGrid strokeDasharray="3 3" />
    //               <XAxis dataKey="name" />
    //               <YAxis allowDecimals={false} />
    //               <Tooltip />
    //               <Legend />
    //               <Bar dataKey="productCount" fill="#8884d8" />
    //             </BarChart>
    //           </ResponsiveContainer>
    //         </CardContent>
    //       </Card>
          
    //       {/* Biểu đồ trạng thái đơn hàng */}
    //       <Card>
    //         <CardContent>
    //           <h2 className="text-lg font-semibold mb-4">Order Status</h2>
    //           <ResponsiveContainer width="100%" height={300}>
    //             <PieChart>
    //               <Pie 
    //                 data={orderStatusData} 
    //                 cx="50%" 
    //                 cy="50%" 
    //                 labelLine={false} 
    //                 outerRadius={100} 
    //                 fill="#8884d8" 
    //                 dataKey="value" 
    //                 label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
    //               >
    //                 {orderStatusData.map((entry, index) => (
    //                   <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
    //                 ))}
    //               </Pie>
    //               <Tooltip />
    //               <Legend />
    //             </PieChart>
    //           </ResponsiveContainer>
    //         </CardContent>
    //       </Card>
    //     </div>
    //   </div>
    // </div>
    <div className="space-y-6">
      <div className="p-4">
        <h2 className="text-2xl font-bold mb-6 dark:text-white">{t('admin.dashboard.title')}</h2>
   
        {/* Main statistics cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">{t('admin.dashboard.total_orders')}</h3>
            <p className="text-3xl font-bold text-blue-600 dark:text-blue-400">{statistics.total_orders || 0}</p>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              {statistics.recent_orders || 0} {t('admin.dashboard.recent_orders')}
            </p>
          </div>
   
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">{t('admin.dashboard.total_revenue')}</h3>
            <p className="text-3xl font-bold text-green-600 dark:text-green-400">
              {(statistics.total_revenue || 0).toLocaleString('vi-VN')} ₫
            </p>
          </div>
   
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">{t('admin.dashboard.total_products')}</h3>
            <p className="text-3xl font-bold text-purple-600 dark:text-purple-400">{statistics.total_products || 0}</p>
            <p className="text-sm text-red-500 dark:text-red-400 mt-1">
              {statistics.low_stock_products || 0} {t('admin.dashboard.low_stock_products')}
            </p>
          </div>

          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">{t('admin.dashboard.categories')}</h3>
            <p className="text-3xl font-bold text-orange-600 dark:text-orange-400">{statistics.total_categories || 0}</p>
          </div>
        </div>

        {/* Order status statistics cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">{t('admin.dashboard.pending')}</h4>
            <p className="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{statistics.pending_orders || 0}</p>
          </div>
         
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">{t('admin.dashboard.processing')}</h4>
            <p className="text-2xl font-bold text-blue-600 dark:text-blue-400">{statistics.processing_orders || 0}</p>
          </div>
         
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">{t('admin.dashboard.completed')}</h4>
            <p className="text-2xl font-bold text-green-600 dark:text-green-400">{statistics.completed_orders || 0}</p>
          </div>
         
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">{t('admin.dashboard.cancelled')}</h4>
            <p className="text-2xl font-bold text-red-600 dark:text-red-400">{statistics.cancelled_orders || 0}</p>
          </div>

          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h4 className="text-sm font-medium mb-1 text-gray-600 dark:text-gray-400">{t('admin.dashboard.returns')}</h4>
            <p className="text-2xl font-bold text-purple-600 dark:text-purple-400">{statistics.return_orders || 0}</p>
          </div>
        </div>

        {/* Charts and detailed statistics */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          {/* Order status chart */}
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">{t('admin.dashboard.order_status_stats')}</h3>
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={[
                { name: t('admin.dashboard.pending'), value: statistics.pending_orders || 0, fill: "#EAB308" },
                { name: t('admin.dashboard.processing'), value: statistics.processing_orders || 0, fill: "#3B82F6" },
                { name: t('admin.dashboard.completed'), value: statistics.completed_orders || 0, fill: "#10B981" },
                { name: t('admin.dashboard.cancelled'), value: statistics.cancelled_orders || 0, fill: "#EF4444" },
                { name: t('admin.dashboard.returns'), value: statistics.return_orders || 0, fill: "#8B5CF6" }
              ]}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="name" />
                <YAxis allowDecimals={false} />
                <Tooltip />
                <Legend />
                <Bar dataKey="value" name={t('admin.dashboard.product_count')} />
              </BarChart>
            </ResponsiveContainer>
          </div>

          {/* Top selling categories */}
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">{t('admin.dashboard.top_categories')}</h3>
            <div className="space-y-3">
              {(statistics.category_stats || []).slice(0, 5).map((category, index) => (
                <div key={category.id} className="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <span className="font-medium text-gray-700 dark:text-gray-300">
                    #{index + 1} {category.name}
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Monthly revenue */}
        {statistics.monthly_revenue && statistics.monthly_revenue.length > 0 && (
          <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">{t('admin.dashboard.monthly_revenue')}</h3>
            <ResponsiveContainer width="100%" height={400}>
              <BarChart data={statistics.monthly_revenue}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="month" />
                <YAxis />
                <Tooltip
                  formatter={(value) => [(value || 0).toLocaleString('vi-VN') + ' ₫', t('admin.dashboard.revenue')]}
                />
                <Legend />
                <Bar dataKey="revenue" fill="#10B981" name={`${t('admin.dashboard.revenue')} (₫)`} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        )}

        {/* Products per category and order status charts */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card>
            <CardContent>
              <h2 className="text-lg font-semibold mb-4">{t('admin.dashboard.products_per_category')}</h2>
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={categoryData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" />
                  <YAxis allowDecimals={false} />
                  <Tooltip />
                  <Legend />
                  <Bar dataKey="productCount" fill="#8884d8" />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
          
          {/* Order status pie chart */}
          <Card>
            <CardContent>
              <h2 className="text-lg font-semibold mb-4">{t('admin.dashboard.order_status')}</h2>
              <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                  <Pie 
                    data={orderStatusData} 
                    cx="50%" 
                    cy="50%" 
                    labelLine={false} 
                    outerRadius={100} 
                    fill="#8884d8" 
                    dataKey="value" 
                    label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                  >
                    {orderStatusData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                  </Pie>
                  <Tooltip />
                  <Legend />
                </PieChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}