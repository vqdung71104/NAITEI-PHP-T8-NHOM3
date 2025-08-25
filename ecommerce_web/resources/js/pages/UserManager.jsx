import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';

export default function UserManager({ users }) {
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 15;

  const userForm = useForm({
    status: ''
  });

  const [filters, setFilters] = useState({
    status: '',
    startDate: '',
    endDate: ''
  });
  const [selectedStatus, setSelectedStatus] = useState({});
  const [filteredUsers, setFilteredUsers] = useState(users);
  const [editingUser, setEditingUser] = useState(null);
  const [notifications, setNotifications] = useState([]);

  // Function to add a notification
  const addNotification = (type, message) => {
    const id = Date.now();
    const newNotification = { id, type, message };
    setNotifications(prev => [...prev, newNotification]);
    
    // Auto remove after 30 seconds
    setTimeout(() => {
      removeNotification(id);
    }, 30000);
  };

  // Function to remove a notification
  const removeNotification = (id) => {
    setNotifications(prev => prev.filter(notification => notification.id !== id));
  };

  // Sync filteredUsers with users props when changed
  useEffect(() => {
    setFilteredUsers(users);
  }, [users]);
  const sortedUsers = [...filteredUsers].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  const paginatedUsers = sortedUsers.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );

  // Handle Find button
  const handleFindUsers = () => {
    let result = users;

    if (filters.status) {
      result = result.filter(
        (user) => user.status.toLowerCase() === filters.status.toLowerCase()
      );
    }

    if (filters.startDate && filters.endDate) {
      const startDate = new Date(filters.startDate);
      const endDate = new Date(filters.endDate);
      endDate.setHours(23, 59, 59, 999); // Set to end of day

      result = result.filter((user) => {
        const userDate = new Date(user.created_at);
        return userDate >= startDate && userDate <= endDate;
      });
    }

    setFilteredUsers(result);
  };

  // Format datetime
  const formatDateTime = (dateString) => {
    const date = new Date(dateString);
    if (isNaN(date)) return dateString;
    return date.toLocaleString();
  };

  // Handle start date change
  const handleStartDateChange = (date) => {
    setFilters(prev => ({
      ...prev,
      startDate: date,
      endDate: prev.endDate && new Date(prev.endDate) < new Date(date) ? date : prev.endDate
    }));
  };

  // Start editing user status
  const handleStartEditUser = (user) => {
    setEditingUser(user.id);
    setSelectedStatus(prev => ({
      ...prev,
      [user.id]: user.status
    }));

    userForm.setData({
      status: user.status
    });
  };

  // Handle status change in select
  const handleStatusChange = (userId, newStatus) => {
    setSelectedStatus(prev => ({
      ...prev,
      [userId]: newStatus
    }));

    userForm.setData('status', newStatus);
  };

  // Save new status
  const handleSaveStatus = (userId) => {
    const newStatus = selectedStatus[userId];

    if (!newStatus) {
      alert('Please select a status');
      return;
    }

    userForm.put(route('admin.users.update', userId), {
      onSuccess: () => {
        setEditingUser(null);
        setSelectedStatus(prev => {
          const newState = { ...prev };
          delete newState[userId];
          return newState;
        });
        addNotification('success', 'User updated successfully');
      },
      onError: (errors) => {
        console.error('Error updating user status:', errors);
        addNotification('error', 'Failed to update user status');
      }
    });
  };

  // Cancel edit
  const handleCancelEdit = (userId) => {
    setEditingUser(null);
    setSelectedStatus(prev => {
      const newState = { ...prev };
      delete newState[userId];
      return newState;
    });
  };

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      {/* Notification Container */}
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
              {notification.type === 'success' ? 'Success!' : 'Error!'}
            </strong>
            <span>{notification.message}</span>
          </div>
        ))}
      </div>
      <div className="p-6">
        <h3 className="text-lg font-medium mb-4">Manage Users</h3>

        {/* Filters */}
        <div className="flex flex-col md:flex-row gap-4 mb-4">
          <select
            className="w-full md:w-48 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
            value={filters.status}
            onChange={(e) =>
              setFilters((prev) => ({ ...prev, status: e.target.value }))
            }
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
          </select>

          <div className="flex items-center gap-2">
            <input
              type="date"
              className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
              value={filters.startDate}
              onChange={(e) => handleStartDateChange(e.target.value)}
              max={filters.endDate || undefined}
            />
            <span className="text-gray-500 dark:text-gray-400">to</span>
            <input
              type="date"
              className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
              value={filters.endDate}
              onChange={(e) => setFilters(prev => ({ ...prev, endDate: e.target.value }))}
              min={filters.startDate || undefined}
            />
          </div>

          <button
            onClick={() => setFilters({ status: '', startDate: '', endDate: '' })}
            className="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
          >
            Clear Filters
          </button>
          <button
            onClick={handleFindUsers}
            className="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Find
          </button>
        </div>

        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead className="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User ID</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              {paginatedUsers.map((user) => (
                <tr key={user.id}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{user.id}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{user.name}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{user.email}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{user.role}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {editingUser === user.id ? (
                      <select
                        className="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                        value={selectedStatus[user.id] || user.status}
                        onChange={(e) => handleStatusChange(user.id, e.target.value)}
                      >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                      </select>
                    ) : (
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                        user.status === 'active' ? 'bg-green-100 text-green-800' :
                        user.status === 'inactive' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-red-100 text-red-800'
                      }`}>
                        {user.status}
                      </span>
                    )}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {editingUser === user.id ? (
                      <div className="flex gap-2">
                        <button
                          onClick={() => handleSaveStatus(user.id)}
                          className="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700"
                          disabled={userForm.processing}
                        >
                          {userForm.processing ? 'Saving...' : 'Save'}
                        </button>
                        <button
                          onClick={() => handleCancelEdit(user.id)}
                          className="px-3 py-1 bg-gray-500 text-white rounded-md hover:bg-gray-600"
                        >
                          Cancel
                        </button>
                      </div>
                    ) : (
                      <button
                        onClick={() => handleStartEditUser(user)}
                        className="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                      >
                        Change Status
                      </button>
                    )}
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
            Previous
          </button>
          <span className="text-gray-700 dark:text-gray-300">Page {currentPage} of {Math.ceil(filteredUsers.length / itemsPerPage)}</span>
          <button
            disabled={currentPage === Math.ceil(filteredUsers.length / itemsPerPage)}
            onClick={() => setCurrentPage(currentPage + 1)}
            className="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-white rounded-md disabled:opacity-50"
          >
            Next
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