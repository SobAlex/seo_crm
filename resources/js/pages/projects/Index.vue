<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <TextLink :href="create().url" variant="create">
                    Новый проект
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Клиент</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Статус</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Дата начала</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="project in projects.data" :key="project.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ project.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink
                                        :href="show(project.id).url"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ project.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <TextLink
                                        :href="`/clients/${project.client_id}`"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ project.client?.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span :class="statusClass(project.status)" class="px-2 py-1 rounded-full text-xs">
                                        {{ statusLabel(project.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(project.start_date) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Пагинация -->
                    <!-- <div class="mt-4" v-html="projects.links"></div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { create, show } from '@/routes/projects'

defineProps<{
    projects: any
}>()

const statusLabel = (status: string) => {
    const labels: Record<string, string> = {
        active: 'Активный',
        paused: 'Приостановлен',
        completed: 'Завершён',
    }
    return labels[status] || status
}

const statusClass = (status: string) => {
    const classes: Record<string, string> = {
        active: 'bg-green-100 text-green-800',
        paused: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-gray-100 text-gray-800',
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('ru-RU')
}
</script>
