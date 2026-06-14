<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">Ключевые слова: {{ website.url }}</h1>
                        <div class="flex space-x-2">
                            <TextLink :href="`/keywords/websites/${website.id}/import`" variant="create">
                                Импорт
                            </TextLink>
                            <button @click="updatePositions" class="btn btn-secondary">
                                Обновить позиции
                            </button>
                            <button v-if="keywords.data.length" @click="destroyAll" class="btn btn-danger">
                                Удалить все
                            </button>
                        </div>
                    </div>

                    <div v-if="keywords.data.length === 0" class="text-center text-gray-500 py-8">
                        Нет ключевых слов. Нажмите «Импорт» для добавления.
                    </div>
                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Ключевое слово</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Позиция</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Дата проверки</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(kw, idx) in keywords.data" :key="kw.id">
                                <td class="px-6 py-4">{{ idx + 1 }}</td>
                                <td class="px-6 py-4">{{ kw.keyword }}</td>
                                <td class="px-6 py-4">{{ kw.latest_position?.position ?? '—' }}</td>
                                <td class="px-6 py-4">{{ formatDate(kw.latest_position?.checked_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import TextLink from '@/components/TextLink.vue'

const props = defineProps({
    website: Object,
    keywords: Object,
})

const updatePositions = () => {
    router.post(`/keywords/websites/${props.website.id}/update-positions`, {
        search_engine: 'google',
        region: 'ru',
    })
}

const destroyAll = () => {
    if (confirm(`Удалить все ключевые слова для сайта ${props.website.url}?`)) {
        router.delete(`/keywords/websites/${props.website.id}/keywords`)
    }
}

const formatDate = (date) => {
    if (!date) return '—'
    return new Date(date).toLocaleString('ru-RU')
}
</script>
