import { useState, useEffect} from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

// Breadcrumbs sẽ được tạo trong component để sử dụng translation

export default function AdminDashboard({
  categories = [],
  products = [],
  orders = [],
  pagination = {},
  statistics = {}
}) {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [currentPage, setCurrentPage] = useState(pagination.current_page || 1);
  const [editingCategory, setEditingCategory] = useState(null);
  const [editingProduct, setEditingProduct] = useState(null);
  const itemsPerPage = pagination.per_page || 10;

  const { t, i18n } = useTranslation();
  const { props } = usePage();
  const { locale, _token } = props;

  // Breadcrumbs với translation
  const breadcrumbs = [
    {
      title: t('admin.title'),
      href: '/admin/dashboard',
    },
  ];
  
  async function changeLang(lang) {
    // 1) Đổi ngay trên frontend
    i18n.changeLanguage(lang);
  
    // 2) Gọi API để lưu vào session backend
    await fetch('/lang', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': _token ?? '',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ lang }),
      credentials: 'same-origin',
    });
  }

  // Forms
  const categoryForm = useForm({
    name: '',
    description: ''
  });

  const productForm = useForm({
    name: '',
    description: '',
    price: '',
    stock: '',
    category_id: '',
    image_url: '',
    author: '',
  });

  const orderForm = useForm({
    status: ''
  });

  const paginatedProducts = products.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );

  // Category handlers
  const handleCategorySubmit = (e) => {
    e.preventDefault();
    if (editingCategory) {
      categoryForm.put(route('admin.categories.update', editingCategory.id), {
        onSuccess: () => {
          setEditingCategory(null);
          categoryForm.reset();
        }
      });
    } else {
      categoryForm.post(route('admin.categories.store'), {
        onSuccess: () => {
          categoryForm.reset();
        }
      });
    }
  };

  const handleEditCategory = (category) => {
    setEditingCategory(category);
    categoryForm.setData({
      name: category.name,
      description: category.description || ''
    });
  };

  const handleDeleteCategory = (categoryId) => {
    if (confirm(t('admin.confirmDeleteCategory'))) {
      categoryForm.delete(route('admin.categories.destroy', categoryId));
    }
  };

  // Product handlers
  const handleProductSubmit = (e) => {
    e.preventDefault();
    if (editingProduct) {
      productForm.put(route('admin.products.update', editingProduct.id), {
        onSuccess: () => {
          setEditingProduct(null);
          productForm.reset();
        }
      });
    } else {
      productForm.post(route('admin.products.store'), {
        onSuccess: () => {
          productForm.reset();
        }
      });
    }
  };

  const handleEditProduct = (product) => {
    setEditingProduct(product);
    productForm.setData({
      name: product.name,
      description: product.description || '',
      price: product.price,
      stock: product.stock,
      category_id: product.category_id,
      image_url: product.image_url || '',
      author: product.author || ''
    });
  };

  const handleDeleteProduct = (productId) => {
    if (confirm(t('admin.confirmDeleteProduct'))) {
      productForm.delete(route('admin.products.destroy', productId));
    }
  };

  // Order handlers
  const [filteredOrders, setFilteredOrders] = useState(orders);
  const [editingOrder, setEditingOrder] = useState(null);

  // Đồng bộ filteredOrders với orders props khi có thay đổi
  useEffect(() => {
    setFilteredOrders(orders);
  }, [orders]);
  
  // Hàm xử lý nút Find
  const handleFindOrders = () => {
    let result = orders;
  
    if (filters.status) {
      result = result.filter(
        (order) => order.status.toLowerCase() === filters.status.toLowerCase()
      );
    }
  
    if (filters.startDate && filters.endDate) {
      const startDate = new Date(filters.startDate);
      const endDate = new Date(filters.endDate);
      endDate.setHours(23, 59, 59, 999); // Đặt thời gian về cuối ngày
      
      result = result.filter((order) => {
        const orderDate = new Date(order.created_at);
        return orderDate >= startDate && orderDate <= endDate;
      });
    }
    
    setFilteredOrders(result);
  };
  
  // Hàm format lại ngày giờ
  const formatDateTime = (dateString) => {
    const date = new Date(dateString);
    if (isNaN(date)) return dateString;
    return date.toLocaleString(); 
  };

  // Hàm xử lý khi ngày bắt đầu thay đổi
  const handleStartDateChange = (date) => {
    setFilters(prev => ({ 
      ...prev, 
      startDate: date,
      // Nếu ngày kết thúc nhỏ hơn ngày bắt đầu mới, cập nhật ngày kết thúc
      endDate: prev.endDate && new Date(prev.endDate) < new Date(date) ? date : prev.endDate
    }));
  };

  // Hàm bắt đầu chỉnh sửa trạng thái
  const handleStartEditOrder = (order) => {
    setEditingOrder(order.id);
    setSelectedStatus(prev => ({
      ...prev,
      [order.id]: order.status
    }));

    // Chỉ cần set status vào form để chuẩn bị update
    orderForm.setData({
      status: order.status
    });
  };

  // Khi thay đổi status trong select
  const handleStatusChange = (orderId, newStatus) => {
    setSelectedStatus(prev => ({
      ...prev,
      [orderId]: newStatus
    }));

    // Cập nhật vào form data
    orderForm.setData('status', newStatus);
  };

  // Lưu trạng thái mới
  const handleSaveStatus = (orderId) => {
    const newStatus = selectedStatus[orderId];

    if (!newStatus) {
      alert('Please select a status');
      return;
    }

    // Gửi status với Inertia form
    orderForm.setData('status', newStatus);
    orderForm.put(route('admin.orders.update', orderId), {
      onSuccess: () => {
        // Redirect sẽ được xử lý tự động bởi Inertia
        setEditingOrder(null);
        setSelectedStatus(prev => {
          const newState = { ...prev };
          delete newState[orderId];
          return newState;
        });
      },
      onError: (errors) => {
        console.error('Error updating order status:', errors);
        alert('Failed to update order status');
      }
    });
  };

  // Hàm hủy chỉnh sửa
  const handleCancelEdit = (orderId) => {
    setEditingOrder(null);
    setSelectedStatus(prev => {
      const newState = { ...prev };
      delete newState[orderId];
      return newState;
    });
  };

  return (
    <AdminLayout breadcrumbs={breadcrumbs}>
      <Head title={t('admin.title')} />
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
        {/* Tab Navigation */}
        <div className="flex border-b border-gray-200 dark:border-gray-700">
          {['dashboard', 'categories', 'products', 'orders'].map((tab) => (
            <button
              key={tab}
              className={`px-4 py-2 font-medium text-sm ${
                activeTab === tab
                  ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
                  : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
              }`}
              onClick={() => setActiveTab(tab)}
            >
              {t(`admin.tabs.${tab}`)}
            </button>
          ))}
        </div>

        {/* Dashboard Tab */}
        
        {activeTab === 'dashboard' && (
          <div className="grid gap-4 md:grid-cols-2">
            <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
              <h3 className="text-lg font-medium mb-4">{t('admin.dashboard.salesOverview')}</h3>
              <div className="h-64 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                <p>{t('admin.dashboard.salesChartPlaceholder')}</p>
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
              <h3 className="text-lg font-medium mb-4">{t('admin.dashboard.productPerformance')}</h3>
              <div className="h-64 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                <p>{t('admin.dashboard.productChartPlaceholder')}</p>
              </div>

              {/* Doanh thu theo tháng */}
              {statistics.monthly_revenue && statistics.monthly_revenue.length > 0 && (
                <div className="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                  <h3 className="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Doanh thu 12 tháng gần đây</h3>
                  <ResponsiveContainer width="100%" height={400}>
                    <BarChart data={statistics.monthly_revenue}>
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="month" />
                      <YAxis />
                      <Tooltip 
                        formatter={(value) => [(value || 0).toLocaleString('vi-VN') + ' ₫', 'Doanh thu']}
                      />
                      <Legend />
                      <Bar dataKey="revenue" fill="#10B981" name="Doanh thu (₫)" />
                    </BarChart>
                  </ResponsiveContainer>
                </div>
              )}
            </div>
          </div>
        )}

        {/* Categories Tab */}
        {activeTab === 'categories' && (
          <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div className="p-6">
              <h3 className="text-lg font-medium mb-4">{t('admin.categories.title')}</h3>
              
              {/* Category Form */}
              <form onSubmit={handleCategorySubmit} className="flex gap-2 mb-4">
                <input
                  type="text"
                  className="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  placeholder={t('admin.categories.namePlaceholder')}
                  value={categoryForm.data.name}
                  onChange={(e) => categoryForm.setData('name', e.target.value)}
                />
                <input
                  type="text"
                  className="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  placeholder={t('admin.categories.descriptionPlaceholder')}
                  value={categoryForm.data.description}
                  onChange={(e) => categoryForm.setData('description', e.target.value)}
                />
                <button
                  type="submit"
                  className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  disabled={categoryForm.processing}
                >
                  {editingCategory ? t('admin.categories.updateButton') : t('admin.categories.addButton')}
                </button>
                {editingCategory && (
                  <button
                    type="button"
                    onClick={() => {
                      setEditingCategory(null);
                      categoryForm.reset();
                    }}
                    className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                  >
                    {t('admin.common.cancel')}
                  </button>
                )}
              </form>

              {/* Display validation errors for categories */}
              {Object.keys(categoryForm.errors).length > 0 && (
                <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                  <strong>Lỗi:</strong>
                  <ul className="mt-2 list-disc list-inside">
                    {Object.values(categoryForm.errors).map((error, index) => (
                      <li key={index}>{error}</li>
                    ))}
                  </ul>
                </div>
              )}

              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead className="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.common.id')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.common.name')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.common.description')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.common.actions')}</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {categories.map((category) => (
                      <tr key={category.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{category.id}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{category.name}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{category.description || '-'}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm">
                          <button
                            onClick={() => handleEditCategory(category)}
                            className="mr-2 px-3 py-1 bg-blue-200 dark:bg-blue-600 text-blue-800 dark:text-white rounded-md hover:bg-blue-300 dark:hover:bg-blue-500"
                          >
                            {t('admin.common.edit')}
                          </button>
                          <button
                            onClick={() => handleDeleteCategory(category.id)}
                            className="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700"
                          >
                            {t('admin.common.delete')}
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* Products Tab */}
        {activeTab === 'products' && (
          <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div className="p-6">
              <h3 className="text-lg font-medium mb-4">{t('admin.products.title')}</h3>

              {/* Product Form */}
              <form onSubmit={handleProductSubmit} className="grid gap-4 mb-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <input 
                    type="text" 
                    placeholder={t('admin.products.namePlaceholder')}
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                    value={productForm.data.name}
                    onChange={(e) => productForm.setData('name', e.target.value)}
                  />
                  <input 
                    type="number" 
                    placeholder={t('admin.products.pricePlaceholder')}
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                    value={productForm.data.price}
                    onChange={(e) => productForm.setData('price', e.target.value)}
                  />
                  <input 
                  type="text" 
                  placeholder="Author" 
                  className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                  value={productForm.data.author}
                  onChange={(e) => productForm.setData('author', e.target.value)}
                  required
                />
                </div>

                <textarea 
                  placeholder={t('admin.products.descriptionPlaceholder')}
                  rows={3} 
                  className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                  value={productForm.data.description}
                  onChange={(e) => productForm.setData('description', e.target.value)}
                />

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <input 
                    type="number" 
                    placeholder={t('admin.products.stockPlaceholder')}
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                    value={productForm.data.stock}
                    onChange={(e) => productForm.setData('stock', e.target.value)}
                  />
                  <select 
                    className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                    value={productForm.data.category_id}
                    onChange={(e) => productForm.setData('category_id', e.target.value)}
                  >
                    <option value="">{t('admin.products.selectCategory')}</option>
                    {categories.map(category => (
                      <option key={category.id} value={category.id}>{category.name}</option>
                    ))}
                  </select>
                  
                </div>

                <input 
                  type="text" 
                  placeholder={t('admin.products.imagePlaceholder')}
                  className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                  value={productForm.data.image_url}
                  onChange={(e) => productForm.setData('image_url', e.target.value)}
                />


                <div className="flex gap-2">
                  <button 
                    type="submit"
                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
                    disabled={productForm.processing}
                  >
                    {editingProduct ? t('admin.products.updateButton') : t('admin.products.addButton')}
                  </button>
                  {editingProduct && (
                    <button
                      type="button"
                      onClick={() => {
                        setEditingProduct(null);
                        productForm.reset();
                      }}
                      className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                    >
                      {t('admin.common.cancel')}
                    </button>
                  )}
                </div>
              </form>

              {/* Display validation errors for products */}
              {Object.keys(productForm.errors).length > 0 && (
                <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                  <strong>Lỗi:</strong>
                  <ul className="mt-2 list-disc list-inside">
                    {Object.values(productForm.errors).map((error, index) => (
                      <li key={index}>{error}</li>
                    ))}
                  </ul>
                </div>
              )}

              {/* Products Table */}
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead className="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.common.id')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.common.name')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.products.price')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.products.stock')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.products.category')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.products.image')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.common.actions')}</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {paginatedProducts.map((product) => (
                      <tr key={product.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.id}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.name}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.author || 'N/A'}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{Number(product.price).toLocaleString("vi-VN")} VND</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.stock}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{product.category?.name || 'N/A'}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                          {product.image_url ? (
                            <img src={product.image_url} alt={product.name} className="h-10 w-10 object-cover rounded" />
                          ) : t('admin.products.noImage')}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                          <button 
                            onClick={() => handleEditProduct(product)}
                            className="mr-2 px-3 py-1 bg-blue-200 dark:bg-blue-600 text-blue-800 dark:text-white rounded-md hover:bg-blue-300 dark:hover:bg-blue-500"
                          >
                            {t('admin.common.edit')}
                          </button>
                          <button 
                            onClick={() => handleDeleteProduct(product.id)}
                            className="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700"
                          >
                            {t('admin.common.delete')}
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Pagination */}
              <div className="flex justify-between items-center mt-4">
                <button 
                  disabled={currentPage === 1} 
                  onClick={() => setCurrentPage(currentPage - 1)}
                  className="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-white rounded-md disabled:opacity-50"
                >
                  {t('admin.common.previous')}
                </button>
                <span className="text-gray-700 dark:text-gray-300">
                  {t('admin.common.pageInfo', { 
                    current: currentPage, 
                    total: Math.ceil(products.length / itemsPerPage) 
                  })}
                </span>
                <button 
                  disabled={currentPage === Math.ceil(products.length / itemsPerPage)} 
                  onClick={() => setCurrentPage(currentPage + 1)}
                  className="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-white rounded-md disabled:opacity-50"
                >
                  {t('admin.common.next')}
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Orders Tab */}
        {/* Orders Tab */}
        {activeTab === 'orders' && (
          <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div className="p-6">
              <h3 className="text-lg font-medium mb-4">{t('admin.orders.title')}</h3>
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead className="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.orders.orderId')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.orders.customer')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.orders.date')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.orders.total')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.orders.status')}</th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{t('admin.common.actions')}</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {filteredOrders.map((order) => (
                      <tr key={order.id}>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{order.id}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{order.user?.name || t('admin.orders.guest')}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{new Date(order.created_at).toLocaleDateString()}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${(Number(order.total_price) || 0).toFixed(2)}</td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                          <select 
                            value={order.status} 
                            className="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white text-sm"
                            disabled
                          >
                            <option value="pending">{t('admin.orders.status.pending')}</option>
                            <option value="processing">{t('admin.orders.status.processing')}</option>
                            <option value="completed">{t('admin.orders.status.completed')}</option>
                            <option value="cancelled">{t('admin.orders.status.cancelled')}</option>
                            <option value="return">{t('admin.orders.status.return')}</option>
                          </select>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                          <button 
                            className="px-3 py-1 bg-blue-200 dark:bg-blue-600 text-blue-800 dark:text-white rounded-md hover:bg-blue-300 dark:hover:bg-blue-500"
                            disabled
                          >
                            {t('admin.orders.view')}
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}
      </div>
    </AdminLayout>
  );
}