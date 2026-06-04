<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Выберите счётчик Яндекс.Метрики</h1>
                    <p class="mb-4 text-gray-600">
                        Выберите счётчик, который хотите привязать к сайту.
                    </p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Сайт</label>
                        <select v-model="selectedWebsiteId" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">Выберите сайт</option>
                            <option v-for="website in websites" :key="website.id" :value="website.id">
                                {{ website.url }} ({{ website.project?.title }})
                            </option>
                        </select>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID счётчика</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="counter in counters" :key="counter.id">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ counter.id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ counter.name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <button
                                        @click="attachCounter(counter.id)"
                                        :disabled="!selectedWebsiteId"
                                        class="btn btn-primary"
                                    >
                                        Привязать
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="counters.length === 0">
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                    У вас нет счётчиков или не удалось их загрузить.
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
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    counters: Array,
    websites: Array,
});

const selectedWebsiteId = ref('');

const attachCounter = (counterId) => {
    if (!selectedWebsiteId.value) {
        alert('Выберите сайт');
        return;
    }
    router.post('/metrika/attach', {
        website_id: selectedWebsiteId.value,
        counter_id: counterId,
    });
};
</script>
