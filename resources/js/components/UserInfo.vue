<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

type Props = {
    user?: User | null;
    showEmail?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    user: undefined,
    showEmail: false,
});

const { getInitials } = useInitials();

const userName = computed(() => props.user?.name || 'Подрядчик');
const userEmail = computed(() => props.user?.email || '');
const showAvatar = computed(() => props.user?.avatar && props.user?.avatar !== '');
</script>

<template>
    <div class="flex items-center gap-2">
        <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
            <AvatarImage v-if="showAvatar" :src="user!.avatar" :alt="userName" />
            <AvatarFallback class="rounded-lg text-black dark:text-white">
                {{ getInitials(userName) }}
            </AvatarFallback>
        </Avatar>

        <div class="grid flex-1 text-left text-sm leading-tight">
            <span class="truncate font-medium">{{ userName }}</span>
            <span v-if="showEmail && userEmail" class="truncate text-xs text-muted-foreground">
                {{ userEmail }}
            </span>
        </div>
    </div>
</template>
