<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-4">
                <div class="flex space-x-2 items-center">
                    <select v-model="selectedProjectId" @change="filterByProject" class="rounded-md border-gray-300 shadow-sm py-2 px-3">
                        <option :value="null">Все проекты</option>
                        <option v-for="project in projects" :key="project.id" :value="project.id">
                            {{ project.title }} ({{ project.client?.title }})
                        </option>
                    </select>
                </div>
                <TextLink :href="create().url" variant="create">
                    Сформировать отчёт
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Проект</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Период</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Сформирован</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Отправлен</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="report in reports.data" :key="report.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ report.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink :href="show({ report: report.id }).url" variant="show" :showIcon="true">
                                        {{ report.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ report.project?.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(report.period_start) }} — {{ formatDate(report.period_end) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ formatDateTime(report.generated_at) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span v-if="report.sent_to_client_at" class="text-green-600">✓ Отправлен</span>
                                    <span v-else class="text-yellow-600">Не отправлен</span>
                                </td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a :href="`/reports/${report.id}/download`" class="text-blue-600 hover:text-blue-900">
                                        📥 PDF
                                    </a>
                                    <button v-if="!report.sent_to_client_at" @click="sendReport(report)" class="text-green-600 hover:text-green-900">
                                        📧 Отправить
                                    </button>
                                    <button @click="destroyReport(report)" class="text-red-600 hover:text-red-900">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- <div class="mt-4" v-html="reports.links"></div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import TextLink from '@/components/TextLink.vue'
import { create, show } from '@/routes/reports'
import { destroy, send } from '@/actions/App/Http/Controllers/ReportController'
import { ref } from 'vue'

const props = defineProps<{
    reports: any
    projects: any[]
    projectId?: number
}>()

const selectedProjectId = ref(props.projectId || null)

const formatDate = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('ru-RU')
}

const formatDateTime = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleString('ru-RU')
}

const filterByProject = () => {
    const url = `/reports${selectedProjectId.value ? `?project_id=${selectedProjectId.value}` : ''}`
    router.get(url)
}

const sendReport = (report: any) => {
    if (confirm(`Отправить отчёт "${report.title}" клиенту?`)) {
        const { url, method } = send({ report: report.id })
        router[method](url)
    }
}

const destroyReport = (report: any) => {
    if (confirm(`Удалить отчёт "${report.title}"?`)) {
        const { url, method } = destroy({ report: report.id })
        router[method](url)
    }
}
</script>
