<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">Просмотр трека</h1>
                        <div class="flex space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">
                                Назад
                            </TextLink>

                            <TextLink :href="edit({ track: track.id }).url" variant="edit" :showIcon="true">
                                Редактировать
                            </TextLink>

                            <Button variant="delete" :showIcon="true" size="lg" @click="destroyTrack">
                                Удалить
                            </Button>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-4 pt-4">
                        <dl class="divide-y divide-gray-100">
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">ID</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ track.id }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Название</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ track.title }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Описание</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ track.description || '—' }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Проект</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <TextLink
                                        :href="`/projects/${track.project_id}`"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ track.project?.title }}
                                    </TextLink>
                                </dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Бизнес-процесс</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ track.business_process?.title }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Сортировка</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ track.sort_order }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Активен</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ track.is_active ? 'Да' : 'Нет' }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Дата создания</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDateTime(track.created_at) }}</dd>
                            </div>
                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-900">Дата обновления</dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">{{ formatDateTime(track.updated_at) }}</dd>
                            </div>
                        </dl>
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
import { index, edit } from '@/routes/tracks'
import { destroy } from '@/actions/App/Http/Controllers/TrackController'

const props = defineProps<{
    track: any
}>()

const formatDateTime = (date: string | null) => {
    if (!date) return '—'
    return new Date(date).toLocaleString('ru-RU')
}

const destroyTrack = () => {
    if (confirm(`Удалить трек "${props.track.title}"?`)) {
        const { url, method } = destroy({ track: props.track.id })
        router[method](url)
    }
}
</script>
