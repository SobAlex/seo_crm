<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">

                <TextLink :href="create().url" variant="create">
                    Новый клиент
                </TextLink>

            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Телефон</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="client in clients.data" :key="client.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ client.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <TextLink
                                        :href="show(client.id).url"
                                        variant="show"
                                        :showIcon="true">
                                        {{ client.title }}
                                    </TextLink>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ client.email || '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ client.phone || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Пагинация -->
                    <!-- <div class="mt-4" v-html="clients.links"></div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TextLink from "@/components/TextLink.vue"
import { create, show } from '@/routes/clients'

defineProps({
    clients: Object
})

</script>
