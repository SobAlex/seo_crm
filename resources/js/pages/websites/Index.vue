<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <TextLink :href="create({ project_id: projectId }).url" variant="create">
                    Новый сайт
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">URL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Проект</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Тип сайта</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">CMS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Регион</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="website in websites.data" :key="website.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ website.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink
                                        :href="show({ website: website.id }).url"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ website.url }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <TextLink
                                        :href="`/projects/${website.project_id}`"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ website.project?.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ website.website_type?.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ website.cms || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ website.region || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- <div class="mt-4" v-html="websites.links"></div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { create, show } from '@/routes/websites'

defineProps<{
    websites: any
    projectId?: number
}>()
</script>
