<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Создание проекта</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title" class="required">Название</Label>
                            <Input id="title" v-model="form.title" :error="errors.title" />
                            <InputError :message="errors.title" />
                        </div>

                        <div>
                            <Label for="client_id" class="required">Клиент</Label>
                            <select id="client_id" v-model="form.client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите клиента</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.title }}
                                </option>
                            </select>
                            <InputError :message="errors.client_id" />
                        </div>

                        <div>
                            <Label for="description">Описание</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label for="start_date">Дата начала</Label>
                                <Input id="start_date" v-model="form.start_date" type="date" />
                            </div>

                            <div>
                                <Label for="end_date">Дата окончания</Label>
                                <Input id="end_date" v-model="form.end_date" type="date" />
                            </div>

                            <div>
                                <Label for="status">Статус</Label>
                                <select id="status" v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="active">Активный</option>
                                    <option value="paused">Приостановлен</option>
                                    <option value="completed">Завершён</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">Отмена</TextLink>
                            <Button type="submit" variant="store" size="lg" :showIcon="true" :disabled="processing">
                                {{ processing ? 'Сохранение...' : 'Создать' }}
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
import { store } from '@/actions/App/Http/Controllers/ProjectController'
import { index } from '@/routes/projects'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

defineProps<{
    clients: any[]
}>()

const form = reactive({
    title: '',
    description: '',
    client_id: '',
    status: 'active',
    start_date: '',
    end_date: '',
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = store()

    router[method](url, form, {
        preserveState: true,
        onFinish: () => (processing.value = false),
        onSuccess: () => router.visit(index().url),
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
