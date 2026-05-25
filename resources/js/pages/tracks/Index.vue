<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <TextLink :href="create({ project_id: projectId }).url" variant="create">
                    Новый трек
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Бизнес-процесс</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Сортировка</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Статус</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="track in tracks.data" :key="track.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ track.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink
                                        :href="show({ track: track.id }).url"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ track.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <TextLink
                                        :href="`/projects/${track.project_id}`"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ track.project?.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ track.business_process?.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ track.sort_order }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span :class="track.is_active ? 'text-green-600' : 'text-red-600'">
                                        {{ track.is_active ? 'Активен' : 'Неактивен' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- <div class="mt-4" v-html="tracks.links"></div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { create, show } from '@/routes/tracks'

defineProps<{
    tracks: any
    projectId?: number
}>()
</script>
