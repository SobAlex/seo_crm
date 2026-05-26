<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <TextLink :href="create().url" variant="create">
                    Новый план
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="space-y-6">
                        <div v-for="planning in plannings" :key="planning.id" class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <TextLink
                                        :href="show({ planning: planning.id }).url"
                                        variant="show"
                                        :showIcon="true"
                                        class="text-lg font-semibold"
                                    >
                                        {{ planning.title }}
                                    </TextLink>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ planning.website?.url }} | {{ planning.metric_label || planning.metric_type }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ formatDate(planning.period_start) }} — {{ formatDate(planning.period_end) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold" :class="progressColor(planning.progress?.status)">
                                        {{ planning.progress?.progress_percent || 0 }}%
                                    </div>
                                    <div class="text-xs text-gray-500">выполнено</div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        class="h-2 rounded-full transition-all"
                                        :class="progressBarColor(planning.progress?.status)"
                                        :style="{ width: `${Math.min(planning.progress?.progress_percent || 0, 100)}%` }"
                                    ></div>
                                </div>
                                <div class="flex justify-between mt-2 text-xs text-gray-500">
                                    <span>Цель: {{ planning.target_value }}</span>
                                    <span>Факт: {{ planning.progress?.cumulative_fact || 0 }}</span>
                                    <span>Прогноз: {{ planning.progress?.forecast || 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="plannings.length === 0" class="text-center py-8 text-gray-500">
                            Нет планов. Создайте первый план.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { create, show } from '@/routes/plannings'

defineProps<{
    plannings: any[]
}>()

const formatDate = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('ru-RU')
}

const progressColor = (status: string) => {
    const colors: Record<string, string> = {
        completed: 'text-green-600',
        on_track: 'text-blue-600',
        warning: 'text-yellow-600',
        critical: 'text-red-600',
        not_started: 'text-gray-400',
    }
    return colors[status] || 'text-gray-600'
}

const progressBarColor = (status: string) => {
    const colors: Record<string, string> = {
        completed: 'bg-green-600',
        on_track: 'bg-blue-600',
        warning: 'bg-yellow-600',
        critical: 'bg-red-600',
        not_started: 'bg-gray-400',
    }
    return colors[status] || 'bg-blue-600'
}
</script>
