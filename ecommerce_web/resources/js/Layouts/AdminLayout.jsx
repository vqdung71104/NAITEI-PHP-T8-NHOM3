import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { Link } from '@inertiajs/react'; // Import Link từ Inertia
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

export default function AdminLayout({ children, breadcrumbs, ...props }) {
  const { t, i18n } = useTranslation();
  const { props:pageProps } = usePage();
  const { locale, _token } = pageProps;
  
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
  return (
    <AppLayoutTemplate 
      breadcrumbs={breadcrumbs} 
      {...props}
      sidebarItems={[
        {
          type: 'header',
          content: 'Admin',
          onClick: () => {},
        },
        {
          type: 'link',
          icon: null,
          text: 'Logout',
          href: route('logout'),
          method: 'post',
        }
      ]}
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
                  {t("Admin Dashboard")}
                </h1>
              </div>
              <div className="flex items-center space-x-4">
                <span className="text-sm text-gray-500 dark:text-gray-400">
                  {t("Welcome, Admin")}
                </span>
                <a
                  href={route('home')}
                  className="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  {t("View Site")}
                </a>
                {/* Thêm nút logout ở đây */}
                <Link
                  href={route('logout')}
                  method="post"
                  as="button"
                  className="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300"
                >
                  {t("Logout")}
                </Link>

                <div className="flex gap-2">
                <button
                  className="px-3 py-2 rounded bg-blue-600 text-white"
                  onClick={() => changeLang('vi')}
                >
                  {t('change_to_vi')}
                </button>
                <button
                  className="px-3 py-2 rounded bg-gray-600 text-white"
                  onClick={() => changeLang('en')}
                >
                  {t('change_to_en')}
                </button>
                </div>
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