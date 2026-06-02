<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div v-if="!isContractor" class="flex justify-between items-center mb-4">
                <div class="flex space-x-2 items-center">
                    <select
                        v-model="selectedTrackId"
                        @change="applyFilters"
                        class="rounded-md border-gray-300 shadow-sm py-2 px-3"
                    >
                        <option :value="null">Все треки</option>
                        <option v-for="track in allTracks" :key="track.id" :value="track.id">
                            {{ track.title }}
                        </option>
                    </select>

                    <select
                        v-model="filters.status_id"
                        @change="applyFilters"
                        class="rounded-md border-gray-300 shadow-sm py-2 px-3"
                    >
                        <option value="">Все статусы</option>
                        <option v-for="status in statuses" :key="status.id" :value="status.id">
                            {{ status.title }}
                        </option>
                    </select>

                    <TextLink :href="`/tasks`" variant="outline">
                        Сбросить
                    </TextLink>
                </div>
                <TextLink :href="`/tasks/create?track_id=${selectedTrackId || ''}`" variant="create">
                    Новая задача
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Трек</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Статус</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Приоритет</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Дедлайн</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Исполнитель</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="task in tasks.data" :key="task.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ task.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink
                                        :href="`/tasks/${task.id}`"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ task.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ task.track?.title }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs" :style="{ backgroundColor: task.status?.color + '20', color: task.status?.color }">
                                        {{ task.status?.title }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span :class="priorityClass(task.priority)" class="px-2 py-1 rounded-full text-xs">
                                        {{ priorityLabel(task.priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(task.deadline) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ task.assignee_user?.name || task.assignee_contractor?.name || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- <div class="mt-4" v-html="tasks.links"></div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import TextLink from '@/components/TextLink.vue'
import { ref, computed } from 'vue'


const user = usePage().props.auth.user
const isContractor = computed(() => user?.role === 'contractor')

const props = defineProps<{
    tasks: any
    trackId?: number
    filters: any
    statuses: any[]
    allTracks: any[]
}>()

const selectedTrackId = ref(props.trackId || null)
const filters = ref({ status_id: props.filters?.status_id || '' })

const priorityLabel = (priority: string) => {
    const labels: Record<string, string> = { low: 'Низкий', medium: 'Средний', high: 'Высокий' }
    return labels[priority] || priority
}

const priorityClass = (priority: string) => {
    const classes: Record<string, string> = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-red-100 text-red-800',
    }
    return classes[priority] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('ru-RU')
}

const applyFilters = () => {
    const params = new URLSearchParams()

    if (selectedTrackId.value) {
        params.append('track_id', String(selectedTrackId.value))
    }
    if (filters.value.status_id) {
        params.append('status_id', filters.value.status_id)
    }

    const url = `/tasks${params.toString() ? '?' + params.toString() : ''}`
    router.get(url)
}
</script>
