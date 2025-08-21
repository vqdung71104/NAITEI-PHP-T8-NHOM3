import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
// import LanguageSync from '@/components/LanguageSync';

export default function Dashboard() {
  const { t, i18n } = useTranslation();
  const { props } = usePage();
  const { locale, _token } = props;

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
    <AuthenticatedLayout
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          {t('Dashboard')}
        </h2>
      }
    >
      {/* <LanguageSync locale={locale} /> */}
      <Head title={t('Dashboard')} />

      <div className="py-12">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900 space-y-4">
              <div>{t("You're logged in!")}</div>

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

              <div className="text-sm text-gray-500">
                Server locale: <b>{locale}</b> | i18n: <b>{i18n.language}</b>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}