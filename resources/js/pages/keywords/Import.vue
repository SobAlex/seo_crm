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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="ключевое слово 1&#10;ключевое слово 2"
                            ></textarea>
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <Link :href="`/websites/${website.id}`" class="btn btn-secondary">Отмена</Link>
                            <button type="submit" class="btn btn-primary" :disabled="processing">
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
    router.post(`/websites/${props.website.id}/keywords/import`, form.value, {
        preserveState: true,
        onFinish: () => {
            processing.value = false;
            form.value.keywords = '';
        },
        onSuccess: () => {
            router.visit(`/websites/${props.website.id}/keywords`); // или куда нужно
        },
    });
};
</script>
