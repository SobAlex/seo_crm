<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="$page.props.flash?.success" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-2 rounded shadow">
            {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-4 py-2 rounded shadow">
            {{ $page.props.flash.error }}
        </div>

        <slot />
    </AppLayout>
</template>
