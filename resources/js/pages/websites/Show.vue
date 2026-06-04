<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">Просмотр сайта</h1>
                        <div class="flex space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">
                                Назад
                            </TextLink>

                            <TextLink :href="edit({ website: website.id }).url" variant="edit" :showIcon="true">
                                Редактировать
                            </TextLink>

                            <Button variant="delete" :showIcon="true" size="lg" @click="destroyWebsite">
                                Удалить
                            </Button>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-4 pt-4">
                        <dl class="divide-y divide-gray-100">
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">ID</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ website.id }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">URL</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <a :href="website.url" target="_blank" class="text-blue-600 hover:underline">{{ website.url }}</a>
                                </dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Проект</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <TextLink
                                        :href="`/projects/${website.project_id}`"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ website.project?.title }}
                                    </TextLink>
                                </dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Тип сайта</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ website.website_type?.title }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">CMS</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ website.cms || '—' }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Регион</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ website.region || '—' }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Дата создания</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDateTime(website.created_at) }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Дата обновления</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDateTime(website.updated_at) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-2">Яндекс.Метрика</h2>
                        <div v-if="website.metrika_counter">
                            <p>Счётчик ID: {{ website.metrika_counter.counter_id }}</p>
                            <p>Статус: {{ website.metrika_counter.sync_status || 'не синхронизирован' }}</p>
                            <p>Последняя синхронизация: {{ formatDateTime(website.metrika_counter.last_sync_at) }}</p>
                            <button @click="detachCounter" class="btn btn-danger mt-2">Отвязать счётчик</button>
                        </div>
                        <div v-else>
                            <a :href="route('metrika.redirect')" class="btn btn-primary">Подключить Яндекс.Метрику</a>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { index, edit } from '@/routes/websites'
import { destroy } from '@/actions/App/Http/Controllers/WebsiteController'

const props = defineProps<{
    website: any
}>()

const formatDateTime = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleString('ru-RU')
}

const destroyWebsite = () => {
    if (confirm(`Удалить сайт "${props.website.url}"?`)) {
        const { url, method } = destroy({ website: props.website.id })
        router[method](url)
    }
}

const detachCounter = () => {
    if (confirm('Отвязать счётчик Яндекс.Метрики от этого сайта?')) {
        router.delete(`/metrika/${website.metrika_counter.id}`, {
            preserveState: true,
            onSuccess: () => router.reload(),
        });
    }
}
</script>
