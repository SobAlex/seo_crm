<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <TextLink :href="create().url" variant="create">
                    Новый статус
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Бизнес-процесс</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Цвет</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Сортировка</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Стартовый</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Конечный</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="status in processStatuses" :key="status.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ status.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink
                                        :href="show({ process_status: status.id }).url"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ status.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ status.business_process?.title }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-block w-6 h-6 rounded-full border" :style="{ backgroundColor: status.color }"></span>
                                    <span class="ml-1">{{ status.color }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ status.sort_order }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span v-if="status.is_start_status" class="text-green-600">✓</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span v-if="status.is_end_status" class="text-green-600">✓</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { create, show } from '@/routes/process-statuses'

defineProps<{
    processStatuses: any[]
}>()
</script>
