<!-- resources/views/components/notification.blade.php -->
<style>
    .notification-panel {
        position: relative;
        display: inline-block;
    }

    .notification-button {
        position: relative;
        padding: 0.5rem;
        background: none;
        border: none;
        cursor: pointer;
    }

    .notification-button:hover {
        color: #4B5563;
    }

    .notification-badge {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(50%, -50%);
        background: #EF4444;
        color: white;
        border-radius: 9999px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: bold;
    }

    .notification-dropdown {
        position: absolute;
        right: 0;
        margin-top: 0.5rem;
        width: 20rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 50;
    }

    .notification-header {
        padding: 1rem;
        border-bottom: 1px solid #E5E7EB;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notification-list {
        max-height: 24rem;
        overflow-y: auto;
    }

    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #E5E7EB;
        cursor: pointer;
    }

    .notification-item:hover {
        background: #F3F4F6;
    }

    .notification-item.unread {
        background: #EFF6FF;
    }

    .notification-icon {
        width: 1.5rem;
        height: 1.5rem;
        margin-right: 1rem;
    }

    .notification-content {
        margin-left: 2.5rem;
    }

    .mark-read-button {
        color: #2563EB;
        font-size: 0.875rem;
        background: none;
        border: none;
        cursor: pointer;
    }

    .mark-read-button:hover {
        color: #1D4ED8;
    }
</style>

<div class="notification-panel">
    <button onclick="toggleNotifications()" class="notification-button">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="notification-badge">{{ $unreadCount }}</span>
        @endif
    </button>

    <div id="notificationDropdown" class="notification-dropdown" style="display: none;">
        <div class="notification-header">
            <h3 style="font-size: 1.125rem; font-weight: 600;">Notifikasi</h3>
            @if($unreadCount > 0)
                <button onclick="markAllAsRead()" class="mark-read-button">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        <div class="notification-list">
            @forelse($notifications as $notification)
                <div class="notification-item {{ !$notification->read_at ? 'unread' : '' }}"
                     onclick="handleNotificationClick('{{ $notification->id }}', '{{ $notification->data['url'] ?? '#' }}')">
                    <div style="display: flex; align-items: start;">
                        @if($notification->type === 'App\\Notifications\\OrderStatusNotification')
                            <svg class="notification-icon text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        @else
                            <svg class="notification-icon text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif

                        <div class="notification-content">
                            <p style="font-weight: 500; color: #111827;">
                                {{ $notification->data['message'] }}
                            </p>
                            @if(isset($notification->data['order_id']))
                                <p style="color: #6B7280; margin-top: 0.25rem;">
                                    Order #{{ $notification->data['order_id'] }}
                                </p>
                            @endif
                            <p style="color: #9CA3AF; font-size: 0.75rem; margin-top: 0.25rem;">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 2rem; color: #6B7280;">
                    Tidak ada notifikasi
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
let isDropdownOpen = false;

function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    isDropdownOpen = !isDropdownOpen;
    dropdown.style.display = isDropdownOpen ? 'block' : 'none';
}

function handleNotificationClick(id, url) {
    // Kirim request untuk menandai notifikasi sebagai telah dibaca
    fetch(`/notifications/${id}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        // Redirect ke URL terkait
        if (url && url !== '#') {
            window.location.href = url;
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        window.location.reload();
    });
}

// Click outside to close dropdown
document.addEventListener('click', function(event) {
    const panel = document.querySelector('.notification-panel');
    const isClickInside = panel.contains(event.target);

    if (!isClickInside && isDropdownOpen) {
        toggleNotifications();
    }
});
</script>