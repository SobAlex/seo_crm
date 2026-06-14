<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Импорт ключевых слов</h1>
                    <p class="text-sm text-gray-500 mb-4">
                        Сайт: <strong>{{ website.url }}</strong>
                    </p>
                    <p class="text-sm text-gray-500 mb-4">
                        Введите ключевые слова (каждое с новой строки). Весь текущий список будет заменён.
                    </p>

                    <form @submit.prevent="submit">
                        <div class="mt-4">
                            <textarea
                                v-model="form.keywords"
                                rows="10"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="ключевое слово 1&#10;ключевое слово 2&#10;ключевое слово 3"
                            ></textarea>
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <Link
                                :href="`/keywords/websites/${website.id}`"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300"
                            >
                                Отмена
                            </Link>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                :disabled="processing"
                            >
                                {{ processing ? 'Импорт...' : 'Импортировать' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';

const props = defineProps({
    website: Object,
});

const form = ref({ keywords: '' });
const processing = ref(false);

const submit = () => {
    processing.value = true;
    router.post(`/keywords/websites/${props.website.id}/import`, form.value, {
        preserveState: true,
        onFinish: () => {
            processing.value = false;
        },
        onSuccess: () => {
            form.value.keywords = '';
        },
    });
};
</script>
