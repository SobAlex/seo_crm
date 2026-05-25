<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Редактирование ключевого слова</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="website_id" class="required">Сайт</Label>
                            <select id="website_id" v-model="form.website_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите сайт</option>
                                <option v-for="website in websites" :key="website.id" :value="website.id">
                                    {{ website.url }} ({{ website.project?.title }})
                                </option>
                            </select>
                            <InputError :message="errors.website_id" />
                        </div>

                        <div>
                            <Label for="keyword" class="required">Ключевое слово</Label>
                            <Input id="keyword" v-model="form.keyword" :error="errors.keyword" />
                            <InputError :message="errors.keyword" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="frequency">Частота</Label>
                                <Input id="frequency" v-model.number="form.frequency" type="number" min="0" />
                            </div>

                            <div>
                                <Label for="difficulty">Сложность (0-100)</Label>
                                <Input id="difficulty" v-model.number="form.difficulty" type="number" min="0" max="100" />
                            </div>

                            <div>
                                <Label for="current_position">Текущая позиция</Label>
                                <Input id="current_position" v-model.number="form.current_position" type="number" min="0" />
                            </div>

                            <div>
                                <Label for="target_position">Целевая позиция</Label>
                                <Input id="target_position" v-model.number="form.target_position" type="number" min="0" />
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink :href="index({ website_id: keyword.website_id }).url" variant="escape" :showIcon="true">Отмена</TextLink>
                            <Button type="submit" variant="update" size="lg" :showIcon="true" :disabled="processing">
                                {{ processing ? 'Сохранение...' : 'Обновить' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import { update } from '@/actions/App/Http/Controllers/KeywordController'
import { index } from '@/routes/keywords'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps<{
    keyword: any
    websites: any[]
}>()

const form = reactive({
    website_id: props.keyword.website_id,
    keyword: props.keyword.keyword,
    frequency: props.keyword.frequency,
    difficulty: props.keyword.difficulty,
    current_position: props.keyword.current_position,
    target_position: props.keyword.target_position,
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = update({ keyword: props.keyword.id })

    router[method](url, form, {
        preserveState: false,
        onFinish: () => (processing.value = false),
        onSuccess: () => router.visit(`/keywords/${props.keyword.id}`),
        onError: (err) => (errors.value = err),
    })
}
</script>

<style scoped>
.required::after {
    content: ' *';
    color: #ef4444;
}
</style>
