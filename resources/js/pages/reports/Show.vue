<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">{{ report.title }}</h1>
                        <div class="flex space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">
                                Назад
                            </TextLink>

                            <a :href="`/reports/${report.id}/download`" class="btn btn-outline" target="_blank">
                                📥 Скачать PDF
                            </a>

                            <Button v-if="!report.sent_to_client_at" variant="success" size="lg" @click="sendReport">
                                📧 Отправить клиенту
                            </Button>

                            <Button variant="delete" :showIcon="true" size="lg" @click="destroyReport">
                                Удалить
                            </Button>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-4 pt-4">
                        <dl class="divide-y divide-gray-100">
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">ID</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ report.id }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Проект</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ report.project?.title }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Клиент</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ report.project?.client?.title }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Период</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDate(report.period_start) }} — {{ formatDate(report.period_end) }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Сформирован</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDateTime(report.generated_at) }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Кем сформирован</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ report.generated_by?.name }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Отправлен клиенту</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <span v-if="report.sent_to_client_at">{{ formatDateTime(report.sent_to_client_at) }}</span>
                                    <span v-else class="text-yellow-600">Не отправлен</span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Предпросмотр комментариев -->
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-3">Комментарии в отчёте</h2>
                        <div class="bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto">
                            <div v-if="report.content?.comments && Object.keys(report.content.comments).length">
                                <div v-for="(tasks, trackTitle) in report.content.comments" :key="trackTitle" class="mb-4">
                                    <h3 class="font-semibold text-gray-800">{{ trackTitle }}</h3>
                                    <div v-for="(comments, taskTitle) in tasks" :key="taskTitle" class="ml-4 mt-2">
                                        <h4 class="text-sm font-medium text-gray-600">{{ taskTitle }}</h4>
                                        <div v-for="comment in comments" :key="comment.date" class="ml-4 mt-1 p-2 bg-white rounded border">
                                            <div class="text-xs text-gray-400">{{ formatDateTime(comment.date) }}</div>
                                            <div class="text-sm text-gray-700">{{ comment.text }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-gray-500 text-center py-8">
                                Нет комментариев с меткой "Для отчета" за указанный период.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { index } from '@/routes/reports'
import { destroy, send } from '@/actions/App/Http/Controllers/ReportController'

const props = defineProps<{
    report: any
}>()

const formatDate = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('ru-RU')
}

const formatDateTime = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleString('ru-RU')
}

const sendReport = () => {
    console.log('Нажата кнопка отправки')
    if (confirm('Отправить отчёт клиенту на email?')) {
        console.log('Подтверждено')
        const { url, method } = send({ report: props.report.id })
        console.log('URL:', url, 'Method:', method)
        router[method](url)
    }
}

const destroyReport = () => {
    if (confirm(`Удалить отчёт "${props.report.title}"?`)) {
        const { url, method } = destroy({ report: props.report.id })
        router[method](url)
    }
}
</script>
