import { useState, useEffect } from 'react';
import { useForm, router } from '@inertiajs/react';
import { EyeIcon, EyeSlashIcon } from '@heroicons/react/24/outline';
import { useTranslation } from 'react-i18next';

export default function UserManager({ users: initialUsers }) {
  const { t } = useTranslation();
  const [currentPage, setCurrentPage] = useState(1);
  const [editingUser, setEditingUser] = useState(null);
  const itemsPerPage = 15;

  const [users, setUsers] = useState(initialUsers || []);
  const [notifications, setNotifications] = useState([]);

  // Sync users state with initialUsers prop when it changes
  useEffect(() => {
    setUsers(initialUsers || []);
  }, [initialUsers]);

  const userForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'customer',
    status: 'active',
  });

  const [showPassword, setShowPassword] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);
  const [pendingDeleteUserId, setPendingDeleteUserId] = useState(null);

  const addNotification = (type, message) => {
    const id = Date.now();
    const newNotification = { id, type, message };
    setNotifications(prev => [...prev, newNotification]);

    setTimeout(() => {
      removeNotification(id);
    }, 30000);
  };

  const removeNotification = (id) => {
    setNotifications(prev => prev.filter(notification => notification.id !== id));
  };

  // Create a new sorted array instead of mutating the state
  const sortedUsers = [...users].sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
  const paginatedUsers = sortedUsers.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );

  const handleUserSubmit = (e) => {
    e.preventDefault();

    if (userForm.data.password !== userForm.data.password_confirmation) {
      addNotification('error', 'Passwords do not match');
      return;
    }

    userForm.transform((data) => {
      const transformed = { ...data };
      if (editingUser && !transformed.password) {
        delete transformed.password;
        delete transformed.password_confirmation;
      }
      return transformed;
    });

    if (editingUser) {
      userForm.put(route('admin.users.update', editingUser.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          setEditingUser(null);
          userForm.reset();
          addNotification('success', 'User updated successfully');
          router.reload({ only: ['users'] });
        },
        onError: (errors) => {
          addNotification('error', 'Failed to update user: ' + Object.values(errors).join(', '));
        },
      });
    } else {
      userForm.post(route('admin.users.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          userForm.reset();
          addNotification('success', 'User created successfully');
          router.reload({ only: ['users'] });
        },
        onError: (errors) => {
          addNotification('error', 'Failed to create user: ' + Object.values(errors).join(', '));
        },
      });
    }
  };

  const handleEditUser = (user) => {
    setEditingUser(user);
    userForm.setData({
      name: user.name,
      email: user.email,
      password: '',
      password_confirmation: '',
      role: user.role || 'customer',
      status: user.status || 'active',
    });
    setShowPassword(false);
    setShowConfirm(false);
  };

  const handleDeleteUser = (userId) => {
    setPendingDeleteUserId(userId);
  };
  const confirmDeleteUser = () => {
    if (pendingDeleteUserId !== null) {
      userForm.delete(route('admin.users.destroy', pendingDeleteUserId), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          addNotification('success', 'User deleted successfully');
          router.reload({ only: ['users'] });
          setPendingDeleteUserId(null);
        },
        onError: () => {
          addNotification('error', 'Failed to delete user');
          setPendingDeleteUserId(null);
        },
      });
    }
  };
  const cancelDeleteUser = () => {
    setPendingDeleteUserId(null);
  };
  return (
    // <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    //   {/* Confirmation Modal */}
    //   {pendingDeleteUserId !== null && (
    //     <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    //       <div className="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6 w-full max-w-sm">
    //         <h2 className="text-lg font-semibold mb-4">Confirm Deletion</h2>
    //         <p className="mb-6">Are you sure you want to delete this user?</p>
    //         <div className="flex justify-end space-x-2">
    //           <button
    //             className="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600"
    //             onClick={cancelDeleteUser}
    //           >
    //             Cancel
    //           </button>
    //           <button
    //             className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
    //             onClick={confirmDeleteUser}
    //           >
    //             Delete
    //           </button>
    //         </div>
    //       </div>
    //     </div>
    //   )}
    //   {/* Notifications */}
    //   <div className="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 space-y-2 w-80">
    //     {notifications.map((notification) => (
    //       <div
    //         key={notification.id}
    //         className={`relative p-4 rounded-md shadow-lg border-l-4 ${
    //           notification.type === 'success'
    //             ? 'bg-green-100 border-green-500 text-green-700'
    //             : 'bg-red-100 border-red-500 text-red-700'
    //         } fade in`}
    //       >
    //         <button
    //           onClick={() => removeNotification(notification.id)}
    //           className="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
    //         >
    //           &times;
    //         </button>
    //         <strong className="block">
    //           {notification.type === 'success' ? 'Success!' : 'Error!'}
    //         </strong>
    //         <span>{notification.message}</span>
    //       </div>
    //     ))}
    //   </div>

    //   <div className="p-6">
    //     <h3 className="text-lg font-medium mb-4 dark:text-white">Manage Users</h3>

    //     {/* User Form */}
    //     <form onSubmit={handleUserSubmit} className="grid gap-4 mb-6">
    //       <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
    //         <input
    //           type="text"
    //           placeholder="User name"
    //           className="px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
    //           value={userForm.data.name}
    //           onChange={(e) => userForm.setData('name', e.target.value)}
    //         />

    //         <input
    //           type="text"
    //           placeholder="User email"
    //           className="px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
    //           value={userForm.data.email}
    //           onChange={(e) => userForm.setData('email', e.target.value)}
    //         />

    //         <div className="relative">
    //           <input
    //             type={showPassword ? 'text' : 'password'}
    //             placeholder="User password"
    //             className="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
    //             value={userForm.data.password}
    //             onChange={(e) => userForm.setData('password', e.target.value)}
    //           />
    //           <button
    //             type="button"
    //             className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-500"
    //             onClick={() => setShowPassword(!showPassword)}
    //           >
    //             {showPassword ? <EyeSlashIcon className="h-5 w-5" /> : <EyeIcon className="h-5 w-5" />}
    //           </button>
    //         </div>

    //         <div className="relative">
    //           <input
    //             type={showConfirm ? 'text' : 'password'}
    //             placeholder="Confirm password"
    //             className="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
    //             value={userForm.data.password_confirmation}
    //             onChange={(e) => userForm.setData('password_confirmation', e.target.value)}
    //           />
    //           <button
    //             type="button"
    //             className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-500"
    //             onClick={() => setShowConfirm(!showConfirm)}
    //           >
    //             {showConfirm ? <EyeSlashIcon className="h-5 w-5" /> : <EyeIcon className="h-5 w-5" />}
    //           </button>
    //         </div>

    //         {/* Role */}
    //         <select
    //           value={userForm.data.role}
    //           onChange={(e) => userForm.setData('role', e.target.value)}
    //           className="px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
    //         >
    //           <option value="admin">Admin</option>
    //           <option value="customer">Customer</option>
    //         </select>

    //         {/* Status */}
    //         <select
    //           value={userForm.data.status}
    //           onChange={(e) => userForm.setData('status', e.target.value)}
    //           className="px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
    //         >
    //           <option value="active">Active</option>
    //           <option value="inactive">Inactive</option>
    //         </select>
    //       </div>

    //       <div className="flex gap-2">
    //         <button
    //           type="submit"
    //           className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
    //           disabled={userForm.processing}
    //         >
    //           {editingUser ? 'Update User' : 'Add User'}
    //         </button>
    //         {editingUser && (
    //           <button
    //             type="button"
    //             onClick={() => {
    //               setEditingUser(null);
    //               userForm.reset();
    //             }}
    //             className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
    //           >
    //             Cancel
    //           </button>
    //         )}
    //       </div>
    //     </form>

    //     {/* Validation errors */}
    //     {Object.keys(userForm.errors).length > 0 && (
    //       <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
    //         <strong>Error:</strong>
    //         <ul className="mt-2 list-disc list-inside">
    //           {Object.values(userForm.errors).map((error, index) => (
    //             <li key={index}>{error}</li>
    //           ))}
    //         </ul>
    //       </div>
    //     )}

    //     {/* Users Table */}
    //     <div className="overflow-x-auto">
    //       <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    //         <thead className="bg-gray-50 dark:bg-gray-700">
    //           <tr>
    //             <th className="px-6 py-3 text-left text-xs font-medium">ID</th>
    //             <th className="px-6 py-3 text-left text-xs font-medium">Name</th>
    //             <th className="px-6 py-3 text-left text-xs font-medium">Email</th>
    //             <th className="px-6 py-3 text-left text-xs font-medium">Role</th>
    //             <th className="px-6 py-3 text-left text-xs font-medium">Status</th>
    //             <th className="px-6 py-3 text-left text-xs font-medium">Actions</th>
    //           </tr>
    //         </thead>
    //         <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
    //           {paginatedUsers.map((user) => (
    //             <tr key={user.id}>
    //               <td className="px-6 py-4">{user.id}</td>
    //               <td className="px-6 py-4">{user.name}</td>
    //               <td className="px-6 py-4">{user.email}</td>
    //               <td className="px-6 py-4">{user.role}</td>
    //               <td className="px-6 py-4">{user.status}</td>
    //               <td className="px-6 py-4 font-medium">
    //                 <button
    //                   onClick={() => handleEditUser(user)}
    //                   className="text-blue-600 hover:text-blue-900"
    //                 >
    //                   Edit
    //                 </button>
    //                 <button
    //                   onClick={() => handleDeleteUser(user.id)}
    //                   className="text-red-600 hover:text-red-900 ml-4"
    //                 >
    //                   Delete
    //                 </button>
    //               </td>
    //             </tr>
    //           ))}
    //         </tbody>
    //       </table>
    //     </div>

    //     {/* Pagination */}
    //     <div className="flex justify-between items-center mt-4">
    //       <button
    //         disabled={currentPage === 1}
    //         onClick={() => setCurrentPage(currentPage - 1)}
    //         className="px-3 py-1 bg-gray-200 rounded-md disabled:opacity-50"
    //       >
    //         Previous
    //       </button>
    //       <span>Page {currentPage} of {Math.ceil(users.length / itemsPerPage)}</span>
    //       <button
    //         disabled={currentPage === Math.ceil(users.length / itemsPerPage)}
    //         onClick={() => setCurrentPage(currentPage + 1)}
    //         className="px-3 py-1 bg-gray-200 rounded-md disabled:opacity-50"
    //       >
    //         Next
    //       </button>
    //     </div>
    //   </div>

    //   <style jsx>{`
    //     .fade.in {
    //       opacity: 1;
    //       transition: opacity 0.5s;
    //     }
    //     .fade {
    //       opacity: 0;
    //       transition: opacity 0.5s;
    //     }
    //   `}</style>
    // </div>
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      {/* Confirmation Modal */}
      {pendingDeleteUserId !== null && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
          <div className="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6 w-full max-w-sm">
            <h2 className="text-lg font-semibold mb-4">{t('admin.users.confirm_deletion')}</h2>
            <p className="mb-6">{t('admin.users.confirm_delete_message')}</p>
            <div className="flex justify-end space-x-2">
              <button
                className="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600"
                onClick={cancelDeleteUser}
              >
                {t('admin.users.cancel')}
              </button>
              <button
                className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                onClick={confirmDeleteUser}
              >
                {t('admin.users.delete')}
              </button>
            </div>
          </div>
        </div>
      )}
      
      {/* Notifications */}
      <div className="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 space-y-2 w-80">
        {notifications.map((notification) => (
          <div
            key={notification.id}
            className={`relative p-4 rounded-md shadow-lg border-l-4 ${
              notification.type === 'success'
                ? 'bg-green-100 border-green-500 text-green-700'
                : 'bg-red-100 border-red-500 text-red-700'
            } fade in`}
          >
            <button
              onClick={() => removeNotification(notification.id)}
              className="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
            >
              &times;
            </button>
            <strong className="block">
              {notification.type === 'success' ? t('admin.users.success') : t('admin.users.error')}
            </strong>
            <span>{notification.message}</span>
          </div>
        ))}
      </div>

      <div className="p-6">
        <h3 className="text-lg font-medium mb-4 dark:text-white">{t('admin.users.title')}</h3>

        {/* User Form */}
        <form onSubmit={handleUserSubmit} className="grid gap-4 mb-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
              type="text"
              placeholder={t('admin.users.name_placeholder')}
              className="px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
              value={userForm.data.name}
              onChange={(e) => userForm.setData('name', e.target.value)}
            />

            <input
              type="text"
              placeholder={t('admin.users.email_placeholder')}
              className="px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
              value={userForm.data.email}
              onChange={(e) => userForm.setData('email', e.target.value)}
            />

            <div className="relative">
              <input
                type={showPassword ? 'text' : 'password'}
                placeholder={t('admin.users.password_placeholder')}
                className="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
                value={userForm.data.password}
                onChange={(e) => userForm.setData('password', e.target.value)}
              />
              <button
                type="button"
                className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-500"
                onClick={() => setShowPassword(!showPassword)}
              >
                {showPassword ? <EyeSlashIcon className="h-5 w-5" /> : <EyeIcon className="h-5 w-5" />}
              </button>
            </div>

            <div className="relative">
              <input
                type={showConfirm ? 'text' : 'password'}
                placeholder={t('admin.users.confirm_password_placeholder')}
                className="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
                value={userForm.data.password_confirmation}
                onChange={(e) => userForm.setData('password_confirmation', e.target.value)}
              />
              <button
                type="button"
                className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-500"
                onClick={() => setShowConfirm(!showConfirm)}
              >
                {showConfirm ? <EyeSlashIcon className="h-5 w-5" /> : <EyeIcon className="h-5 w-5" />}
              </button>
            </div>

            {/* Role */}
            <select
              value={userForm.data.role}
              onChange={(e) => userForm.setData('role', e.target.value)}
              className="px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
            >
              <option value="admin">{t('admin.users.role.admin')}</option>
              <option value="customer">{t('admin.users.role.customer')}</option>
            </select>

            {/* Status */}
            <select
              value={userForm.data.status}
              onChange={(e) => userForm.setData('status', e.target.value)}
              className="px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
            >
              <option value="active">{t('admin.users.status.active')}</option>
              <option value="inactive">{t('admin.users.status.inactive')}</option>
            </select>
          </div>

          <div className="flex gap-2">
            <button
              type="submit"
              className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              disabled={userForm.processing}
            >
              {editingUser ? t('admin.users.update_user') : t('admin.users.add_user')}
            </button>
            {editingUser && (
              <button
                type="button"
                onClick={() => {
                  setEditingUser(null);
                  userForm.reset();
                }}
                className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
              >
                {t('admin.users.cancel')}
              </button>
            )}
          </div>
        </form>

        {/* Validation errors */}
        {Object.keys(userForm.errors).length > 0 && (
          <div className="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <strong>{t('admin.users.validation_error')}</strong>
            <ul className="mt-2 list-disc list-inside">
              {Object.values(userForm.errors).map((error, index) => (
                <li key={index}>{error}</li>
              ))}
            </ul>
          </div>
        )}

        {/* Users Table */}
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead className="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium">{t('admin.users.table.id')}</th>
                <th className="px-6 py-3 text-left text-xs font-medium">{t('admin.users.table.name')}</th>
                <th className="px-6 py-3 text-left text-xs font-medium">{t('admin.users.table.email')}</th>
                <th className="px-6 py-3 text-left text-xs font-medium">{t('admin.users.table.role')}</th>
                <th className="px-6 py-3 text-left text-xs font-medium">{t('admin.users.table.status')}</th>
                <th className="px-6 py-3 text-left text-xs font-medium">{t('admin.users.table.actions')}</th>
              </tr>
            </thead>
            <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              {paginatedUsers.map((user) => (
                <tr key={user.id}>
                  <td className="px-6 py-4">{user.id}</td>
                  <td className="px-6 py-4">{user.name}</td>
                  <td className="px-6 py-4">{user.email}</td>
                  <td className="px-6 py-4">{t(`admin.users.role.${user.role}`)}</td>
                  <td className="px-6 py-4">{t(`admin.users.status.${user.status}`)}</td>
                  <td className="px-6 py-4 font-medium">
                    <button
                      onClick={() => handleEditUser(user)}
                      className="text-blue-600 hover:text-blue-900"
                    >
                      {t('admin.users.edit')}
                    </button>
                    <button
                      onClick={() => handleDeleteUser(user.id)}
                      className="text-red-600 hover:text-red-900 ml-4"
                    >
                      {t('admin.users.delete')}
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
            className="px-3 py-1 bg-gray-200 rounded-md disabled:opacity-50"
          >
            {t('admin.users.pagination.previous')}
          </button>
          <span>
            {t('admin.users.pagination.page')} {currentPage} {t('admin.users.pagination.of')} {Math.ceil(users.length / itemsPerPage)}
          </span>
          <button
            disabled={currentPage === Math.ceil(users.length / itemsPerPage)}
            onClick={() => setCurrentPage(currentPage + 1)}
            className="px-3 py-1 bg-gray-200 rounded-md disabled:opacity-50"
          >
            {t('admin.users.pagination.next')}
          </button>
        </div>
      </div>

      <style jsx>{`
        .fade.in {
          opacity: 1;
          transition: opacity 0.5s;
        }
        .fade {
          opacity: 0;
          transition: opacity 0.5s;
        }
      `}</style>
    </div>
  );
}