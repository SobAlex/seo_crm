<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Сайты с ключевыми словами</h1>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Сайт</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Ключей</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="site in websites.data" :key="site.id">
                                <td class="px-6 py-4">{{ site.url }}</td>
                                <td class="px-6 py-4">{{ site.keywords_count }}</td>
                                <td class="px-6 py-4">
                                    <TextLink :href="`/keywords/websites/${site.id}`" variant="show">
                                        Ключевые слова
                                    </TextLink>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import TextLink from '@/components/TextLink.vue'

defineProps({ websites: Object })
</script>
