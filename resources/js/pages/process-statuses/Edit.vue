<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">Редактирование статуса</h1>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title" class="required">Название</Label>
                            <Input id="title" v-model="form.title" :error="errors.title" />
                            <InputError :message="errors.title" />
                        </div>

                        <div>
                            <Label for="business_process_id" class="required">Бизнес-процесс</Label>
                            <select id="business_process_id" v-model="form.business_process_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Выберите бизнес-процесс</option>
                                <option v-for="process in businessProcesses" :key="process.id" :value="process.id">
                                    {{ process.title }}
                                </option>
                            </select>
                            <InputError :message="errors.business_process_id" />
                        </div>

                        <div>
                            <Label for="color">Цвет</Label>
                            <div class="flex items-center space-x-2">
                                <Input id="color" v-model="form.color" type="color" class="w-16" />
                                <Input v-model="form.color" placeholder="#cccccc" />
                            </div>
                        </div>

                        <div>
                            <Label for="sort_order">Сортировка</Label>
                            <Input id="sort_order" v-model.number="form.sort_order" type="number" />
                        </div>

                        <div class="flex items-center space-x-4">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="form.is_start_status" />
                                <span>Стартовый статус</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="form.is_end_status" />
                                <span>Конечный статус</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <TextLink :href="index().url" variant="escape" :showIcon="true">Отмена</TextLink>
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
import { update } from '@/actions/App/Http/Controllers/ProcessStatusController'
import { index } from '@/routes/process-statuses'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps<{
    processStatus: any
    businessProcesses: any[]
}>()

const form = reactive({
    title: props.processStatus.title,
    business_process_id: props.processStatus.business_process_id,
    color: props.processStatus.color || '#cccccc',
    sort_order: props.processStatus.sort_order,
    is_start_status: props.processStatus.is_start_status,
    is_end_status: props.processStatus.is_end_status,
})

const errors = ref({})
const processing = ref(false)

const submit = () => {
    processing.value = true
    const { url, method } = update({ process_status: props.processStatus.id })

    router[method](url, form, {
        preserveState: false,
        onFinish: () => (processing.value = false),
        onSuccess: () => router.visit(`/process-statuses/${props.processStatus.id}`),
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
