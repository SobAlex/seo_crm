<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <TextLink :href="create().url" variant="create">
                    Новый бизнес-процесс
                </TextLink>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Описание</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="process in businessProcesses" :key="process.id">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ process.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <TextLink
                                        :href="show(process.id).url"
                                        variant="show"
                                        :showIcon="true"
                                    >
                                        {{ process.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ process.description || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>  <!-- ← вот этот тег был пропущен -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { create, show } from '@/routes/business-processes'

defineProps<{
    businessProcesses: any[]
}>()
</script>
