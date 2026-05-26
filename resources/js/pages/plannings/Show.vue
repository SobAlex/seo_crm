<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">{{ planning.title }}</h1>
                        <div class="flex space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">
                                Назад
                            </TextLink>

                            <TextLink :href="edit({ planning: planning.id }).url" variant="edit" :showIcon="true">
                                Редактировать
                            </TextLink>

                            <Button variant="delete" :showIcon="true" size="lg" @click="destroyPlanning">
                                Удалить
                            </Button>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-4 pt-4">
                        <dl class="divide-y divide-gray-100">
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Сайт</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ planning.website?.url }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Трек</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ planning.track?.title || '—' }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Период</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDate(planning.period_start) }} — {{ formatDate(planning.period_end) }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Метрика</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ planning.metric_label || planning.metric_type }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Целевое значение</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ planning.target_value }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Прогресс -->
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-3">Прогресс выполнения</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between mb-2">
                                <span>Прогресс:</span>
                                <span class="font-bold" :class="progressColor(planning.progress?.status)">
                                    {{ planning.progress?.progress_percent || 0 }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                                <div
                                    class="h-3 rounded-full transition-all"
                                    :class="progressBarColor(planning.progress?.status)"
                                    :style="{ width: `${Math.min(planning.progress?.progress_percent || 0, 100)}%` }"
                                ></div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div>
                                    <div class="text-gray-500">Выполнено</div>
                                    <div class="font-semibold">{{ planning.progress?.cumulative_fact || 0 }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">План</div>
                                    <div class="font-semibold">{{ planning.progress?.cumulative_target || 0 }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Дней прошло</div>
                                    <div class="font-semibold">{{ planning.progress?.days_passed || 0 }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Прогноз</div>
                                    <div class="font-semibold">{{ planning.progress?.forecast || 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Факты по неделям -->
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-3">Фактические значения по неделям</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Неделя</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Даты</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">План</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Факт</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Ручной ввод</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="fact in planning.facts" :key="fact.week_number">
                                        <td class="px-4 py-2 text-sm">Неделя {{ fact.week_number }}</td>
                                        <td class="px-4 py-2 text-sm">{{ formatDate(fact.week_start) }} — {{ formatDate(fact.week_end) }}</td>
                                        <td class="px-4 py-2 text-sm">{{ (planning.target_value / fact.days_in_week * fact.days_in_week).toFixed(2) }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span :class="fact.actual_value !== null ? 'text-green-600' : 'text-gray-400'">
                                                {{ fact.actual_value !== null ? fact.actual_value : '—' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <div class="flex items-center space-x-2">
                                                <input
                                                    v-model="manualValues[fact.week_number]"
                                                    type="number"
                                                    step="0.01"
                                                    :placeholder="fact.manual_value !== null ? fact.manual_value : 'Ввести'"
                                                    class="w-24 rounded-md border-gray-300 text-sm"
                                                />
                                                <button
                                                    @click="saveManualFact(fact.week_number)"
                                                    class="text-blue-600 hover:text-blue-800 text-sm"
                                                >
                                                    Сохранить
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { index, edit } from '@/routes/plannings'
import { destroy } from '@/actions/App/Http/Controllers/PlanningController'

const props = defineProps<{
    planning: any
}>()

const manualValues = ref<Record<number, number>>({})

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

const saveManualFact = (weekNumber: number) => {
    const value = manualValues.value[weekNumber]
    if (value === undefined) return

    router.post(`/plannings/${props.planning.id}/manual-fact`, {
        week_number: weekNumber,
        manual_value: value,
    }, {
        preserveState: true,
        onSuccess: () => {
            manualValues.value[weekNumber] = undefined
        },
    })
}

const destroyPlanning = () => {
    if (confirm(`Удалить план "${props.planning.title}"?`)) {
        const { url, method } = destroy({ planning: props.planning.id })
        router[method](url)
    }
}
</script>
