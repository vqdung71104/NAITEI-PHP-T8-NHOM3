import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { Link } from '@inertiajs/react'; // Import Link từ Inertia

export default function AdminLayout({ children, breadcrumbs, ...props }) {
  return (
    <AppLayoutTemplate 
      breadcrumbs={breadcrumbs} 
      {...props}
      sidebarItems={[]} // Đặt mảng rỗng để loại bỏ sidebar
      hideBranding={true}
    >
      <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
        {/* Admin Header */}
        <div className="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center py-4">
              <div className="flex items-center">
                <button 
                  //onClick={() => {}}
                  className="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                >
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
                <a
                  href={route('home')}
                  className="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  View Site
                </a>
                {/* Thêm nút logout ở đây */}
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

        {/* Main Content */}
        <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
          {children}
        </main>
      </div>
    </AppLayoutTemplate>
  );
}