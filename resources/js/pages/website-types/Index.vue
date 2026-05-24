<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <TextLink :href="create().url" variant="create">
                    Новый тип сайта
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Метрики</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Иконка</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Сортировка</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="type in websiteTypes" :key="type.id">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink
                                        :href="show(type.id).url"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ type.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ formatMetrics(type.default_metrics) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ type.icon || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ type.sort_order }}</td>
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
import { create, show } from '@/routes/website-types'

defineProps<{
    websiteTypes: any[]
}>()

const metricLabels: Record<string, string> = {
    visits: 'Визиты',
    depth: 'Глубина',
    time_on_site: 'Время на сайте',
    bounce_rate: 'Отказы',
    conversion: 'Конверсия',
}

const formatMetrics = (metrics: string[] | null) => {
    if (!metrics?.length) return '—'
    return metrics.map(m => metricLabels[m] || m).join(', ')
}
</script>
