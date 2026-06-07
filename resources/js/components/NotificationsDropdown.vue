<template>
    <div class="relative">
        <button
            @click="toggleDropdown"
            class="relative p-1 rounded-full hover:bg-gray-100 focus:outline-none"
        >
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span
                v-if="unreadCount > 0"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <div
            v-show="isOpen"
            class="fixed bottom-2 right-4 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        >
            <div class="px-4 py-2 border-b border-gray-200">
                <h3 class="text-sm font-medium text-gray-900">Уведомления</h3>
            </div>

            <div class="max-h-64 overflow-y-auto">
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                    :class="{ 'bg-blue-50': !notification.read_at }"
                    @click="markAsRead(notification)"
                >
                    <p class="text-sm text-gray-800">{{ notification.data.message }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ formatTime(notification.created_at) }}</p>
                </div>

                <div v-if="!notifications.length" class="px-4 py-6 text-center text-sm text-gray-500">
                    Нет уведомлений
                </div>
            </div>

            <div v-if="notifications.length" class="px-4 py-2 border-t border-gray-200 text-center">
                <button @click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800">
                    Отметить все как прочитанные
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const isOpen = ref(false);
const notifications = ref([]);
const page = usePage();

const initialUnreadCount = computed(() => page.props.auth.user?.unread_notifications_count ?? 0);
const unreadCount = ref(initialUnreadCount.value);

const fetchNotifications = async () => {
    const response = await fetch('/notifications');
    const data = await response.json();
    notifications.value = data;
    unreadCount.value = data.filter((n) => !n.read_at).length;
};

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        fetchNotifications();
    }
};

const markAsRead = async (notification) => {
    if (notification.read_at) {
        if (notification.data?.url) {
            router.visit(notification.data.url);
        }
        return;
    }

    try {
        await fetch(`/notifications/${notification.id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({}),
        });
        notification.read_at = true;
        unreadCount.value--;
        if (notification.data?.url) {
            router.visit(notification.data.url);
        }
    } catch (error) {
        console.error('Ошибка при отметке прочитанным', error);
        if (notification.data?.url) {
            router.visit(notification.data.url);
        }
    }
};

const markAllAsRead = async () => {
    try {
        await fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({}),
        });
        await fetchNotifications();
    } catch (error) {
        console.error('Ошибка при отметке всех прочитанными', error);
    }
};

const formatTime = (date) => {
    const diff = new Date().getTime() - new Date(date).getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Только что';
    if (minutes < 60) return `${minutes} мин назад`;
    if (hours < 24) return `${hours} ч назад`;
    return `${days} д назад`;
};

const setupEcho = () => {
    if (window.Echo && window.userId) {
        window.Echo.private(`user.${window.userId}`)
            .notification((notification) => {
                notifications.value.unshift({
                    id: notification.id,
                    data: notification.data,
                    created_at: new Date().toISOString(),
                    read_at: null,
                });
                unreadCount.value++;
            });
    }
};

onMounted(() => {
    setupEcho();
});
</script>
