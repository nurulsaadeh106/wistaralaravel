# NotificationComponent.vue
<template>
  <div class="notifications-panel">
    <!-- Notification Bell -->
    <div class="relative">
      <button
        @click="toggleNotifications"
        class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none"
      >
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
          />
        </svg>

        <!-- Notification Badge -->
        <span
          v-if="unreadCount > 0"
          class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
        >
          {{ unreadCount }}
        </span>
      </button>

      <!-- Notifications Dropdown -->
      <div
        v-if="showNotifications"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50"
      >
        <div class="p-4 border-b">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Notifikasi</h3>
            <button
              v-if="unreadCount > 0"
              @click="markAllAsRead"
              class="text-sm text-blue-600 hover:text-blue-800"
            >
              Tandai Semua Dibaca
            </button>
          </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
          <div v-if="notifications.length === 0" class="p-4 text-center text-gray-500">
            Tidak ada notifikasi
          </div>

          <template v-else>
            <div
              v-for="notification in notifications"
              :key="notification.id"
              :class="['p-4 border-b hover:bg-gray-50 cursor-pointer',
                      { 'bg-blue-50': !notification.read_at }]"
              @click="handleNotificationClick(notification)"
            >
              <!-- Order Status Notification -->
              <template v-if="notification.type === 'App\\Notifications\\OrderStatusNotification'">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <svg
                      class="w-6 h-6 text-blue-500"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                      />
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">
                      {{ notification.data.message }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                      Order #{{ notification.data.order_id }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                      {{ formatDate(notification.created_at) }}
                    </p>
                  </div>
                </div>
              </template>

              <!-- Email Verification Notification -->
              <template v-else-if="notification.type === 'App\\Notifications\\CustomVerifyEmail'">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <svg
                      class="w-6 h-6 text-green-500"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">
                      Verifikasi Email
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                      Silakan verifikasi email Anda untuk mengaktifkan akun
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                      {{ formatDate(notification.created_at) }}
                    </p>
                  </div>
                </div>
              </template>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

export default {
  name: 'NotificationComponent',

  setup() {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const showNotifications = ref(false);
    const toast = useToast();
    let notificationInterval;

    const fetchNotifications = async () => {
      try {
        const response = await axios.get('/api/notifications');
        notifications.value = response.data;
        unreadCount.value = notifications.value.filter(n => !n.read_at).length;
      } catch (error) {
        console.error('Failed to fetch notifications:', error);
      }
    };

    const markAllAsRead = async () => {
      try {
        await axios.post('/api/notifications/mark-all-read');
        await fetchNotifications();
        toast.success('Semua notifikasi telah ditandai sebagai dibaca');
      } catch (error) {
        toast.error('Gagal menandai notifikasi sebagai dibaca');
      }
    };

    const markAsRead = async (notificationId) => {
      try {
        await axios.post(`/api/notifications/${notificationId}/mark-as-read`);
        await fetchNotifications();
      } catch (error) {
        console.error('Failed to mark notification as read:', error);
      }
    };

    const handleNotificationClick = async (notification) => {
      if (!notification.read_at) {
        await markAsRead(notification.id);
      }

      // Handle different notification types
      if (notification.type === 'App\\Notifications\\OrderStatusNotification') {
        // Navigate to order detail
        window.location.href = `/orders/${notification.data.order_id}`;
      } else if (notification.type === 'App\\Notifications\\CustomVerifyEmail') {
        // Navigate to email verification
        window.location.href = '/email/verify';
      }
    };

    const toggleNotifications = () => {
      showNotifications.value = !showNotifications.value;
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    // Click outside to close
    const handleClickOutside = (event) => {
      if (showNotifications.value && !event.target.closest('.notifications-panel')) {
        showNotifications.value = false;
      }
    };

    onMounted(() => {
      fetchNotifications();
      document.addEventListener('click', handleClickOutside);
      // Poll for new notifications every minute
      notificationInterval = setInterval(fetchNotifications, 60000);
    });

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside);
      clearInterval(notificationInterval);
    });

    return {
      notifications,
      unreadCount,
      showNotifications,
      toggleNotifications,
      markAllAsRead,
      handleNotificationClick,
      formatDate
    };
  }
};
</script>

<style scoped>
.notifications-panel {
  position: relative;
  display: inline-block;
}
</style>
