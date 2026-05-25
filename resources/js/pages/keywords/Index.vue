<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <TextLink :href="create({ website_id: websiteId }).url" variant="create">
                    Новое ключевое слово
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Ключевое слово</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Сайт</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Частота</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Сложность</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Текущая позиция</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Целевая позиция</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="keyword in keywords.data" :key="keyword.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ keyword.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink
                                        :href="show({ keyword: keyword.id }).url"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ keyword.keyword }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <TextLink
                                        :href="`/websites/${keyword.website_id}`"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ keyword.website?.url }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ keyword.frequency || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ keyword.difficulty || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ keyword.current_position || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ keyword.target_position || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- <div class="mt-4" v-html="keywords.links"></div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { create, show } from '@/routes/keywords'

defineProps<{
    keywords: any
    websiteId?: number
}>()
</script>
